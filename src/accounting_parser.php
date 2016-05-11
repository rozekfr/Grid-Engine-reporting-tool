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

    /**
     * Parsuje accounting soubor, vrací true pokud už přesáhla rozsah datumů
     */
    function accounting_parser($db,$db_table,$file,$start,$end)
    {
        if(empty($db_table)){
            $db->query("TRUNCATE TABLE stats_ulohy");
            $db->query("TRUNCATE TABLE stats_uzivatele");
        }
        $handle = fopen($file, "r");
        $users = array();
        $jobs = array();
        while (!feof($handle)) {
            $line = fgets($handle);
            if (!empty($line)) {
                //parsing line
                $polozky = explode(":", $line);
                $id = $polozky[5]; //job_number
                $uzivatel = $polozky[3]; //owner
                $real_time = floatval($polozky[13]); //ru_wallclock [s]
                $cpu_time = floatval($polozky[36]); //cpu [s]
                $vyuzita_pamet = floatval($polozky[37]) * 1024; //mem [MBs]
                $max_mem = floatval($polozky[16]) / 1024; //ru_maxrss [MB]
                //alokované zdroje
                $resources = $polozky[39];
                $resources = explode("-", $resources);
                foreach ($resources as $r) {
                    $matches = array();
                    if (preg_match_all("/^l.*/", $r, $matches)) {
                        $r = explode(" ", $r);
                        $r = explode(",", $r[1]);
                        $alloc_mem = null;
                        foreach ($r as $rs){
                            if (preg_match_all("/^mem_free=/", $rs, $matches)) {
                                $tmp = explode("=", $rs);
                                $alloc_mem = my_unit($tmp[1]);
                            }
                            if (is_null($alloc_mem) and preg_match_all("/^ram_free=/", $rs, $matches)) {
                                $tmp = explode("=", $rs);
                                $alloc_mem = my_unit($tmp[1]);
                            }
                        }
                    }
                }
            }
            //parsing line done

            //$jobs
            if (array_key_exists($id, $jobs)) { //úloha už je v poli
                $jobs[$id]["tasks"] += 1;
                $jobs[$id]["real_time"] += $real_time;
                $jobs[$id]["cpu_time"] += $cpu_time;
                $jobs[$id]["max_mem"] = ($jobs[$id]["max_mem"] < $max_mem ? $max_mem : $jobs[$id]["max_mem"]);
                $jobs[$id]["used_memory"] += $vyuzita_pamet;
            } else {
                //ještě není
                $jobs[$id] = array("id" => $id, "user" => $uzivatel, "tasks" => 1, "real_time" => $real_time, "cpu_time" => $cpu_time, "max_mem" => $max_mem, "used_memory" => $vyuzita_pamet, "alloc_memory" => $alloc_mem);
            }

            //$users
            if (array_key_exists($uzivatel, $users)) {
                //uživatel už je v poli
                if (!array_search($id, $users[$uzivatel]["jobs"])) {
                    array_push($users[$uzivatel]["jobs"], $id);
                }
                $users[$uzivatel]["tasks"] += 1;
                $users[$uzivatel]["real_time"] += $real_time;
                $users[$uzivatel]["cpu_time"] += $cpu_time;
            }
            else {
                //ještě není
                $users[$uzivatel] = array("user" => $uzivatel, "jobs" => array($id), "tasks" => 1, "real_time" => $real_time, "cpu_time" => $cpu_time);
            }
        }
        fclose($handle);


        //výpočty pro pole jobů a vložení do databáze
        foreach ($jobs as $job) {
            //efektivita
            if ($job["real_time"] === 0.0 or $job["cpu_time"] === 0.0) {
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
            $ret = $db->query("INSERT INTO `stats_ulohy".(!empty($db_table) ? "_$db_table" : "")."`(`id`, `id_ulohy`, `uzivatel`, `pocet_tasku`, `realny_cas`, `cpu_cas`,`prum_cas_na_task`, `efektivita`, `alokovana_pamet_MB`, `vyuzita_pamet_MB`,`max_vyuzita_pamet_MB`) VALUES (NULL,{$job["id"]},'{$job["user"]}',{$job["tasks"]},{$job["real_time"]},{$job["cpu_time"]},$avg_time_per_task,$efektivita,$alokovana_pamet,$vyuzita_pamet,{$job["max_mem"]})");
            if(!$ret){
                echo "JOB:".$db->error."<br>";
            }
        }

        //výpočty pro pole uživatelů a vložení do databáze
        foreach ($users as $user) {
            //efektivita
            if ($user["real_time"] === 0.0 or $user["cpu_time"] === 0.0) {
                unset($efektivita);
            } else {
                $efektivita = round($user["cpu_time"] / $user["real_time"] * 100.0, 2);
            }
            $efektivita = isset($efektivita) ? "'$efektivita'" : "NULL";
            //počet úloh
            $pocetUloh = count($user["jobs"]);
            //průměrný čas na úlohu
            $avg_time_per_job = $user["real_time"]/$pocetUloh;
            //průmerný čas na task
            $avg_time_per_task = $user["real_time"]/$user["tasks"];

            //skupina
            $skupina = "-";

            //spotřeba
            $spotreba = 0;


            //vložení do databáze
            $ret = $db->query("INSERT INTO `stats_uzivatele".(!empty($db_table) ? "_$db_table" : "")."`(`id`, `uzivatel`, `skupina`, `pocet_uloh`, `pocet_tasku`, `realny_cas`, `cpu_cas`, `efektivita`, `prum_cas_na_ulohu`, `prum_cas_na_task`, `spotreba`) VALUES (NULL,'{$user["user"]}','$skupina',$pocetUloh,{$user["tasks"]},{$user["real_time"]},{$user["cpu_time"]},$efektivita,$avg_time_per_job,$avg_time_per_task,$spotreba)");
            if(!$ret){
                echo "USER:".$db->error."<br>";
            }
        }
    }


    // --- hlavní tělo programu --- //
    include 'pripojeniDB.php';
    $start = (!isset($_GET["start"]) ? time() : $_GET["start"]);
    $end = (!isset($_GET["end"]) ? (time() - (30 * 24 * 60 * 60)) : $_GET["end"]);
    $db_table = "";
    accounting_parser($db,$db_table,"accounting",$start,$end);
?>