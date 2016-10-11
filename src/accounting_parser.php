<?php

    /** Vrací přepočítanou hodnotu podle daných jednotek ve $v na MB
     * @param $v hodnota z qstat obsahující jednotky (K,M,G,T)
     * @return float přepočítaná jednotka na MB
     */
    function my_unit($v)
    {
        $value = substr($v, 0, strlen($v) - 1);
        $unit = substr($v,strlen($v) - 1, 1);
        switch($unit){
            case "T":
                $value = $value * 1024 * 1024;
                break;

            case "G":
                $value = $value * 1024;
                break;

            case "M":
                $value = $value;
                break;

            case "K":
                $value = $value / 1024;
                break;
        }
        return floatval($value);
    }

	/** Vrací RegExp na globální souborové servery.
	 * @return string
	 */
	function get_globals(){
		$globals = "";
		$groups = file_get_contents("groups");
		$groups = explode("\n",$groups);
		foreach($groups as $group){
			if(!empty($group)){
				$g = explode(":",$group);
				if($g[0] == "@globals"){
					$g1 = explode(",",$g[1]);
					foreach($g1 as $server){
						if(!empty($server)){
							$globals.=$server."|";
						}
					}
				}
			}
		}
		$globals = trim($globals,"|");
		return $globals;
	}


    /**
     * Parsuje accounting soubor a vkládá data do databáze tak, že je přičítá k existujícím.
     */
    function accounting_parser($db)
    {
	    if(file_exists("accounting_last_end_time")){
		    $last_end_time = intval(file_get_contents("accounting_last_end_time"));
	    }
	    else{
		    $last_end_time = 0;
	    }

	    //porovnání času dokončení úlohy s časem dokončení posledního uloženého záznamu, pokud je menší, tak už byl tento accounting zpracován - rezerva 5s
	    $f = fopen("accounting", 'r');
	    $line = fgets($f);
	    $line = explode(":",$line);
	    fclose($f);
	    $first_end_time = $line[10];
	    if($first_end_time < $last_end_time){
		    echo "Tento accounting soubor už byl zpracován.<br>";
		    return;
	    }

	    $handle = fopen("accounting", "r");
        $users = array();
        $jobs = array();
		$global_servers = get_globals();
        while (!feof($handle)) {
            $line = fgets($handle);
            if (!empty($line)) {
                //parsing line
                $polozky = explode(":", $line);
	            $node = $polozky[1];
	            $node = explode(".",$node);
	            $node = $node[0]; //uzel
                $id = $polozky[5]; //job_number
                $uzivatel = $polozky[3]; //owner
	            $start_time = $polozky[9]; //start_time
	            $end_time = $polozky[10]; //end_time
                $real_time = floatval($polozky[13]); //ru_wallclock [s]
                $cpu_time = floatval($polozky[36]); //cpu [s]
                $vyuzita_pamet = floatval($polozky[37]) * 1024; //mem [MBs]
                $max_mem = floatval($polozky[16]) / 1024; //ru_maxrss [MB]

	            if($end_time > $last_end_time){
		            $last_end_time = $end_time;
	            }

	            //alokované zdroje
                $resources = $polozky[39];
                $resources = explode("-", $resources);

	            //spotřeba
	            $spotreba_gpu = 0;
	            $spotreba_serveru = 0;

	            //spotřeba CPU
	            $dotazSpotreba = $db->query("SELECT pocet_slotu,prikon_uzlu FROM uzly JOIN konfigurace on id_konfigurace=konfigurace.nazev WHERE uzly.nazev='$node'");
	            if($dotazSpotreba->num_rows == 0){
		            $spotreba_cpu = 0;
	            }
	            else {
		            $spotreba = $dotazSpotreba->fetch_assoc();
		            $spotreba_cpu = $cpu_time * intval($spotreba["prikon_uzlu"]) / intval($spotreba["pocet_slotu"]);
	            }

	            foreach ($resources as $r) {
                    $matches = array();
                    if (preg_match_all("/^l.*/", $r, $matches)) {
                        $r = explode(" ", $r);
                        $r = explode(",", $r[1]);
                        $alloc_mem = null;
	                    $gpu = 0;
                        foreach ($r as $rs){
                            if (preg_match_all("/^mem_free=/", $rs, $matches)) {
                                $tmp = explode("=", $rs);
                                $alloc_mem = my_unit($tmp[1]);
                            }
                            if (is_null($alloc_mem) and preg_match_all("/^ram_free=/", $rs, $matches)) {
                                $tmp = explode("=", $rs);
                                $alloc_mem = my_unit($tmp[1]);
                            }
                            if (preg_match_all("/^gpu=/", $rs, $matches)) {
                                $tmp = explode("=", $rs);
                                $gpu = intval($tmp[1]);
	                            if($gpu > 0){
		                            //spotřeba GPU
		                            $dotazSpotreba = $db->query("SELECT prikon_gpu FROM uzly JOIN konfigurace on id_konfigurace=konfigurace.nazev WHERE uzly.nazev='$node'");
		                            $spotreba = $dotazSpotreba->fetch_assoc();
		                            $spotreba_gpu = $real_time * $gpu * intval($spotreba["prikon_gpu"]);
	                            }
                            }
	                        if(preg_match_all("/^(".$global_servers.")=/", $rs, $matches)){
		                        $tmp = explode("=",$rs);
		                        $server = $tmp[0];
		                        $value = $tmp[1];
		                        $dotazSpotreba = $db->query("SELECT prikon_uzlu FROM uzly JOIN konfigurace on id_konfigurace=konfigurace.nazev WHERE uzly.nazev='$server'");
		                        $spotreba = $dotazSpotreba->fetch_assoc();
		                        $spotreba_serveru += $real_time * (intval($spotreba["prikon_uzlu"])*$value)/100;
	                        }
                        }
                    }
                }
	            $spotreba = ($spotreba_cpu + $spotreba_gpu + $spotreba_serveru)/3600; //převod na Wh

	            //$jobs
	            if (array_key_exists($id, $jobs)) {
		            //úloha už je v poli
		            $jobs[$id]["tasks"] += 1;
		            $jobs[$id]["start_time"] = ($jobs[$id]["start_time"] < $start_time  ? $jobs[$id]["start_time"] : $start_time);
		            $jobs[$id]["real_time"] += $real_time;
		            $jobs[$id]["cpu_time"] += $cpu_time;
		            $jobs[$id]["max_mem"] = ($jobs[$id]["max_mem"] < $max_mem ? $max_mem : $jobs[$id]["max_mem"]);
		            $jobs[$id]["used_memory"] += $vyuzita_pamet;
		            $jobs[$id]["gpu"] = intval($jobs[$id]["gpu"] < $gpu ? $gpu : $jobs[$id]["gpu"]); //ukládám maximum
		            $jobs[$id]["spotreba"] += (isset($spotreba) ? floatval($spotreba) : 0.0);
	            }
	            else {
		            //ještě není
		            $jobs[$id] = array("id" => $id, "user" => $uzivatel, "tasks" => 1, "gpu" => $gpu, "start_time" => $start_time,"real_time" => $real_time, "cpu_time" => $cpu_time, "spotreba" => $spotreba, "max_mem" => $max_mem, "used_memory" => $vyuzita_pamet, "alloc_memory" => $alloc_mem);
	            }
            }
            //parsing line done
        }
        fclose($handle);

        //výpočty pro pole jobů a vložení do databáze
        foreach ($jobs as $job) {
            //efektivita
            if ($job["real_time"] == 0) {
                unset($efektivita);
            } else {
                $efektivita = round($job["cpu_time"]/$job["real_time"] * 100.0, 2);
            }
            $efektivita = isset($efektivita) ? "$efektivita" : "NULL";
            //pruměrný čas na jeden task
            $avg_time_per_task = $job["real_time"]/$job["tasks"];
            //alokovaná paměť
            $alokovana_pamet = isset($job["alloc_memory"]) ? $job["alloc_memory"] : "NULL";
            //využitá paměť
            if($job["cpu_time"] === 0.0 or $job["used_memory"] === 0.0){
                unset($vyuzita_pamet);
            }
            else {
                $vyuzita_pamet = $job["used_memory"] / $job["cpu_time"];
            }
            $vyuzita_pamet = isset($vyuzita_pamet) ? "$vyuzita_pamet" : "NULL";

            //vložení do databáze
            $ret = $db->query("INSERT INTO `sge_rt_stats_ulohy` (`id_ulohy`, `uzivatel`, `pocet_tasku`, `pocet_gpu`, `cas_startu`,`realny_cas`, `cpu_cas`,`prum_cas_na_task`, `efektivita`, `spotreba`, `alokovana_pamet_MB`, `vyuzita_pamet_MB`,`max_vyuzita_pamet_MB`) VALUES ({$job["id"]},'{$job["user"]}',{$job["tasks"]},{$job["gpu"]},FROM_UNIXTIME(".$job["start_time"]."),{$job["real_time"]},{$job["cpu_time"]},$avg_time_per_task,$efektivita,{$job["spotreba"]},$alokovana_pamet,$vyuzita_pamet,{$job["max_mem"]}) ON DUPLICATE KEY UPDATE id_ulohy=id_ulohy, uzivatel=uzivatel, pocet_tasku=pocet_tasku + {$job["tasks"]},pocet_gpu=GREATEST(pocet_gpu,{$job["gpu"]}),cas_startu=LEAST(cas_startu,FROM_UNIXTIME(".$job["start_time"].")),realny_cas=realny_cas + {$job["real_time"]}, cpu_cas = cpu_cas + {$job["cpu_time"]},prum_cas_na_task=(realny_cas+{$job["real_time"]})/(pocet_tasku+{$job["tasks"]}),efektivita=IF(realny_cas+{$job["real_time"]} = 0 OR cpu_cas+{$job["cpu_time"]} = 0,NULL,(cpu_cas+{$job["cpu_time"]})/(realny_cas+{$job["real_time"]}) * 100.0),spotreba=spotreba+{$job["spotreba"]},alokovana_pamet_MB=alokovana_pamet_MB,vyuzita_pamet_MB=vyuzita_pamet_MB+$vyuzita_pamet,max_vyuzita_pamet_MB=GREATEST(max_vyuzita_pamet_MB,{$job["max_mem"]})");
            if(!$ret){
                echo "JOB:".$db->error."<br>\n";
            }

	        //přidání uživatele do SGE
	        $db->query("UPDATE uzivatele SET sge=1 WHERE uzivatel='{$job["user"]}'");
        }

	    file_put_contents("accounting_last_end_time",$last_end_time);
	    echo "Accounting byl zpracován.";
    }


    // --- hlavní tělo programu --- //
    include 'pripojeniDB.php';
    accounting_parser($db);

	//smazání starých
	$dotazTabulky = $db->query("SELECT CONCAT('DROP TABLE ',GROUP_CONCAT(CONCAT(table_schema,'.',table_name)),';') AS statement FROM information_schema.tables WHERE TABLE_NAME LIKE 'sge_rt_stats_ulohy_%' OR TABLE_NAME LIKE 'sge_rt_stats_uzivatele%'");
	$statement = $dotazTabulky->fetch_assoc();

	$ret = $db->query($statement["statement"]);

	if(!$ret){
		echo $db->error."<br>\n";
	}

	//vytvoření tabulky uživatelů
	$ret = $db->query("CREATE TABLE sge_rt_stats_uzivatele (PRIMARY KEY (uzivatel)) AS (SELECT uzivatel,COUNT(id_ulohy) as pocet_uloh,SUM(pocet_tasku) AS pocet_tasku, SUM(realny_cas) AS realny_cas, SUM(cpu_cas) AS cpu_cas, (IF(SUM(realny_cas)=0,NULL,(SUM(cpu_cas)/SUM(realny_cas)) * 100)) AS efektivita, ROUND(pocet_gpu/COUNT(id_ulohy)) AS prum_vyuzitych_gpu, SUM(realny_cas)/COUNT(id_ulohy) AS prum_cas_na_ulohu, SUM(realny_cas)/SUM(pocet_tasku) AS prum_cas_na_task,SUM(spotreba) AS spotreba FROM sge_rt_stats_ulohy GROUP BY uzivatel)");

	if(!$ret){
		echo $db->error."<br>\n";
	}

	//uzivatele poslední týden
	$ret = $db->query("CREATE TABLE sge_rt_stats_uzivatele_posledni_tyden (PRIMARY KEY (uzivatel)) AS (SELECT uzivatel,COUNT(id_ulohy) as pocet_uloh,SUM(pocet_tasku) AS pocet_tasku, SUM(realny_cas) AS realny_cas, SUM(cpu_cas) AS cpu_cas, (IF(SUM(realny_cas)=0,NULL,(SUM(cpu_cas)/SUM(realny_cas)) * 100)) AS efektivita, ROUND(pocet_gpu/COUNT(id_ulohy)) AS prum_vyuzitych_gpu, SUM(realny_cas)/COUNT(id_ulohy) AS prum_cas_na_ulohu, SUM(realny_cas)/SUM(pocet_tasku) AS prum_cas_na_task,SUM(spotreba) AS spotreba FROM sge_rt_stats_ulohy WHERE cas_startu BETWEEN DATE_SUB(now(),INTERVAL 1 WEEK) AND now() GROUP BY uzivatel)");

	if(!$ret){
		echo $db->error."<br>\n";
	}

	//uzivatele poslední mesic
	$ret = $db->query("CREATE TABLE sge_rt_stats_uzivatele_posledni_mesic (PRIMARY KEY (uzivatel)) AS (SELECT uzivatel,COUNT(id_ulohy) as pocet_uloh,SUM(pocet_tasku) AS pocet_tasku, SUM(realny_cas) AS realny_cas, SUM(cpu_cas) AS cpu_cas, (IF(SUM(realny_cas)=0,NULL,(SUM(cpu_cas)/SUM(realny_cas)) * 100)) AS efektivita, ROUND(pocet_gpu/COUNT(id_ulohy)) AS prum_vyuzitych_gpu, SUM(realny_cas)/COUNT(id_ulohy) AS prum_cas_na_ulohu, SUM(realny_cas)/SUM(pocet_tasku) AS prum_cas_na_task,SUM(spotreba) AS spotreba FROM sge_rt_stats_ulohy WHERE cas_startu BETWEEN DATE_SUB(now(),INTERVAL 1 MONTH) AND now() GROUP BY uzivatel)");

	if(!$ret){
		echo $db->error."<br>\n";
	}

	//úlohy poslední týden
	$ret = $db->query("CREATE TABLE sge_rt_stats_ulohy_posledni_tyden (PRIMARY KEY (id_ulohy)) AS SELECT * FROM sge_rt_stats_ulohy WHERE cas_startu BETWEEN DATE_SUB(now(),INTERVAL 1 WEEK) AND now()");

	if(!$ret){
		echo $db->error."<br>\n";
	}

	//úlohy poslední měsíc
	$ret = $db->query("CREATE TABLE sge_rt_stats_ulohy_posledni_mesic (PRIMARY KEY (id_ulohy)) AS SELECT * FROM sge_rt_stats_ulohy WHERE cas_startu BETWEEN DATE_SUB(now(),INTERVAL 1 MONTH) AND now()");

	if(!$ret){
		echo $db->error."<br>\n";
	}

?>