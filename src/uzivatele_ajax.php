<?php
    //kódování a české znaky
    ini_set("default_charset", "UTF-8");
    header("Content-Type: text/html; UTF-8");
?>
<?php
    include "pripojeniDB.php";
    include "functions.php";

    $database = (empty($_GET["db"]) ? "sge_rt_stats_uzivatele" : "{$_GET["db"]}");
	$database = ((!empty($_GET["od"]) and !empty($_GET["do"])) ? "sge_rt_stats_ulohy" : $database);
    $select = $_GET["select"];
	$select_new = explode(",",$select);
	$select_db = array();
	foreach($select_new as $s){
		if($s == "uzivatel"){
			$s = $database.".uzivatel";
		}
		else if($s == "skupina"){
			$s = "vychozi_skupina AS skupina";

		}
		array_push($select_db,$s);
	}
	$select_db = implode(",",$select_db);

    $orderby = $_GET["orderby"];

    //zpracování where
    $where = $_GET["where"];
    //pro zpětné uchování fitrů do inputů
    $is_filtr = 0;
    if($where != 1) {
        $tmp_where = explode("|", $where);
        $filtry = array();
        foreach ($tmp_where as $filtr) {
            $tmp = explode(":", $filtr);
            if (!empty($tmp[1])){
                array_push($filtry, $tmp[1]);
                $is_filtr = 1;
            }
            else {
                array_push($filtry, "");
            }
        }
    }

    $where = ($is_filtr ? make_where_statement($database,$where) : "1");

	$groupBy = "";

	//pokud je období
	if(!empty($_GET["od"]) and !empty($_GET["do"])){
		$database = "sge_rt_stats_ulohy";
		if($where == "1"){
			$where = "cas_startu BETWEEN '{$_GET["od"]}' AND '{$_GET["do"]}'";
		}
		else{
			$where .= " AND cas_startu BETWEEN '{$_GET["od"]}' AND '{$_GET["do"]}'";
		}
		$select_new = explode(",",$select);
		$select_db = "";
		//úprava selectu nad tabulkou úloh k získání statistik uživatelů
		foreach($select_new as $s){
			switch($s){
				case 'uzivatel':
					$string = $database.".uzivatel AS uzivatel";
					$select_db .= (empty($select_db) ? "$string" : ", $string");
					break;

				case 'skupina':
					$string = "vychozi_skupina AS skupina";
					$select_db .= (empty($select_db) ? "$string" : ", $string");
					break;

				case 'pocet_uloh':
					$string = "COUNT(id_ulohy) as pocet_uloh";
					$select_db .= (empty($select_db) ? "$string" : ", $string");
					break;

				case 'pocet_tasku':
					$string = "SUM(pocet_tasku) AS pocet_tasku";
					$select_db .= (empty($select_db) ? "$string" : ", $string");
					break;

				case 'realny_cas':
					$string = "SUM(realny_cas) AS realny_cas";
					$select_db .= (empty($select_db) ? "$string" : ", $string");
					break;

				case 'cpu_cas':
					$string = "SUM(cpu_cas) AS cpu_cas";
					$select_db .= (empty($select_db) ? "$string" : ", $string");
					break;

				case 'efektivita':
					$string = "(IF(SUM(realny_cas)=0,NULL,(SUM(cpu_cas)/SUM(realny_cas)) * 100)) AS efektivita";
					$select_db .= (empty($select_db) ? "$string" : ", $string");
					break;

				case 'prum_vyuzitych_gpu':
					$string = "ROUND(pocet_gpu/COUNT(id_ulohy)) AS prum_vyuzitych_gpu";
					$select_db .= (empty($select_db) ? "$string" : ", $string");
					break;

				case 'prum_cas_na_ulohu':
					$string = "SUM(realny_cas)/COUNT(id_ulohy) AS prum_cas_na_ulohu";
					$select_db .= (empty($select_db) ? "$string" : ", $string");
					break;

				case 'prum_cas_na_task':
					$string = "SUM(realny_cas)/SUM(pocet_tasku) AS prum_cas_na_task";
					$select_db .= (empty($select_db) ? "$string" : ", $string");
					break;

				case 'spotreba':
					$string = "SUM(spotreba) AS spotreba";
					$select_db .= (empty($select_db) ? "$string" : ", $string");
					break;

				default:
					$string = $s;
					break;
			}
			$groupBy = "GROUP BY uzivatel";
		}
	}

    //LIMIT
    //počet položek
    $pocetPolozek = (!empty($_GET["polozky"]) ? $_GET["polozky"] : 10);
    //stránka
    $stranka = (!empty($_GET["stranka"]) ? $_GET["stranka"] : 1);
    $limit = "LIMIT ".($stranka-1)*$pocetPolozek.",".$pocetPolozek;

    //dotaz do DB
    $dotazUsers = $db->query("SELECT $select_db FROM $database JOIN uzivatele ON $database.uzivatel=uzivatele.uzivatel WHERE $where $groupBy ORDER BY $orderby");

    //nastavení statistik - filtrů a stránkování
    echo "<div id='stats_settings'>";
        echo "<span>Počet položek: </span>";
        echo "<select id='pocet_polozek' onchange='update_stats(null,1,\"uzivatele_ajax\",\"users_stats\")'>";
            echo "<option value='5' ".($pocetPolozek == "5" ? "selected" : "").">5</option>";
            echo "<option value='10' ".($pocetPolozek == "10" ? "selected" : "").">10</option>";
            echo "<option value='25' ".($pocetPolozek == "25" ? "selected" : "").">25</option>";
            echo "<option value='50' ".($pocetPolozek == "50" ? "selected" : "").">50</option>";
            echo "<option value='100' ".($pocetPolozek == "100" ? "selected" : "").">100</option>";
        echo "</select>";
        echo "<span>Stránka: </span>";
        echo "<select id='strankovani' onchange='update_stats(this,1,\"uzivatele_ajax\",\"users_stats\")'>";
            $pocet = ceil($dotazUsers->num_rows/$pocetPolozek);
            if($pocet > 0){
                $i = 1;
                while($i <= $pocet){
                    echo "<option value='$i' ".($i == intval($stranka) ? "selected" : "").">$i</option>";
                    $i++;
                }
            }
            else{
                echo "<option value='1'>1</option>";
            }
        echo "</select>";
        echo "<span>Filtry: </span>";
        echo "<input type='button' id='filtr' value='aplikovat filtry' onclick='update_stats(null,1,\"uzivatele_ajax\",\"users_stats\")'>";
    echo "</div>";

    //hlavička tabulky
    echo "<table>";
    echo "<thead>";
        if(!isset($select)){
            echo "<tr><th>Chyba: Nebyly zadány sloupce k zobrazení!</th></tr>";
        }
        else{
            $columns = explode(",",$select);

            $order = explode(" ",$orderby);
            $howorder = $order[1];
            $order = $order[0];

            echo "<tr>";
            foreach($columns as $column){
                if(!empty($column)){
                    switch($column){
                        case 'uzivatel':
                            echo "<th>Uživatel <img src='foto/".($order == $column ? ($howorder == "ASC" ? "vzestupne" : "sestupne") : "neradit").".png' id='uzivatel' class='razeni' alt='razeni' onclick='update_stats(this,1,\"uzivatele_ajax\",\"users_stats\");'></th>";
                            break;

	                    case 'vychozi_skupina':
		                    echo "<th>Skupina <img src='foto/".($order == $column ? ($howorder == "ASC" ? "vzestupne" : "sestupne") : "neradit").".png' id='uzivatel' class='razeni' alt='razeni' onclick='update_stats(this,1,\"uzivatele_ajax\",\"users_stats\");'></th>";
		                    break;

                        case 'pocet_uloh':
                            echo "<th>Počet úloh <img src='foto/".($order == $column ? ($howorder == "ASC" ? "vzestupne" : "sestupne") : "neradit").".png' id='$column' class='razeni' alt='razeni' onclick='update_stats(this,1,\"uzivatele_ajax\",\"users_stats\");'></th>";
                            break;

                        case 'pocet_tasku':
                            echo "<th>Počet tasků <img src='foto/".($order == $column ? ($howorder == "ASC" ? "vzestupne" : "sestupne") : "neradit").".png' id='$column' class='razeni' alt='razeni' onclick='update_stats(this,1,\"uzivatele_ajax\",\"users_stats\");'></th>";
                            break;

                        case 'realny_cas':
                            echo "<th>Reálný čas <img src='foto/".($order == $column ? ($howorder == "ASC" ? "vzestupne" : "sestupne") : "neradit").".png' id='$column' class='razeni' alt='razeni' onclick='update_stats(this,1,\"uzivatele_ajax\",\"users_stats\");'></th>";
                            break;

                        case 'cpu_cas':
                            echo "<th>CPU čas <img src='foto/".($order == $column ? ($howorder == "ASC" ? "vzestupne" : "sestupne") : "neradit").".png' id='$column' class='razeni' alt='razeni' onclick='update_stats(this,1,\"uzivatele_ajax\",\"users_stats\");'></th>";
                            break;

                        case 'prum_cas_na_ulohu':
                            echo "<th>Prum. čas úlohy <img src='foto/".($order == $column ? ($howorder == "ASC" ? "vzestupne" : "sestupne") : "neradit").".png' id='$column' class='razeni' alt='razeni' onclick='update_stats(this,1,\"uzivatele_ajax\",\"users_stats\");'></th>";
                            break;

                        case 'prum_cas_na_task':
                            echo "<th>Prum. čas tasku <img src='foto/".($order == $column ? ($howorder == "ASC" ? "vzestupne" : "sestupne") : "neradit").".png' id='$column' class='razeni' alt='razeni' onclick='update_stats(this,1,\"uzivatele_ajax\",\"users_stats\");'></th>";
                            break;

	                    case 'prum_vyuzitych_gpu':
		                    echo "<th>Prum. využitých GPU <img src='foto/".($order == $column ? ($howorder == "ASC" ? "vzestupne" : "sestupne") : "neradit").".png' id='$column' class='razeni' alt='razeni' onclick='update_stats(this,1,\"uzivatele_ajax\",\"users_stats\");'></th>";
		                    break;

                        case 'spotreba':
                            echo "<th>Spotřeba <img src='foto/".($order == $column ? ($howorder == "ASC" ? "vzestupne" : "sestupne") : "neradit").".png' id='$column' class='razeni' alt='razeni' onclick='update_stats(this,1,\"uzivatele_ajax\",\"users_stats\");'></th>";
                            break;

                        default:
                            echo "<th>".ucfirst($column)." <img src='foto/".($order == $column ? ($howorder == "ASC" ? "vzestupne" : "sestupne") : "neradit").".png' id='$column' class='razeni' alt='razeni' onclick='update_stats(this,1,\"uzivatele_ajax\",\"users_stats\");'></th>";
                            break;
                    }
                }
            }
            echo "</tr>";
        }
    echo "</thead>";

    //tělo tabulky
    echo "<tbody>";
    //filtry
    echo "<tr>";
    $i = 0;
    foreach($columns as $column){
        echo "<td class='filtr'><input type='text' class='filtr_input' id='filtr_{$column}' autocomplete='off' value='".(isset($filtry[$i]) ? "$filtry[$i]" : "")."' onkeyup='check_filters();'></td>";
        $i++;
    }
    echo "</tr>";
    $dotazUsers = $db->query("SELECT $select_db,aktivita FROM $database JOIN uzivatele ON $database.uzivatel=uzivatele.uzivatel WHERE $where $groupBy ORDER BY $orderby $limit");
    //data
    if(is_object($dotazUsers) and $dotazUsers->num_rows != 0){
        while($user = $dotazUsers->fetch_assoc()){
            if($user["aktivita"] == 1){
	            echo "<tr>";
            }
	        else{
		        echo "<tr class='neaktivni'>";
	        }
            foreach($columns as $column){
                if($column == "skupina"){
	                echo "<td class='skupina'>";
	                    echo "{$user["skupina"]}";
	                    $dotazSkupiny = $db->query("SELECT id_skupiny FROM rozpis_uzivatele_skupiny WHERE uzivatel='{$user["uzivatel"]}'");
	                    if($dotazSkupiny->num_rows > 0){
		                    while($sk = $dotazSkupiny->fetch_assoc()){
			                    echo ", {$sk["id_skupiny"]}";
		                    }
	                    }
                    echo "</td>";
                }
                else if($column == "realny_cas" or $column == "cpu_cas" or $column == "prum_cas_na_ulohu" or $column == "prum_cas_na_task"){
                    echo "<td class='time'>".transform_time(floatval($user[$column]))."</td>";
                }
                else if($column == "pocet_uloh" or $column == "pocet_tasku"){
                    echo "<td>".number_format($user[$column],0,","," ")."</td>";
                }
                else if($column == "efektivita" or $column == "prum_cas_na_ulohu" or $column == "prum_cas_na_task"){
                    echo "<td>".number_format($user[$column],2,","," ")."</td>";
                }
                else if($column == "spotreba"){
	                echo "<td>".write_consumption($user[$column])."</td>";
                }
                else {

                    echo "<td>{$user[$column]}</td>";
                }
            }
            echo "</tr>";
        }
    }
    else{
        echo "<tr><td colspan='".count($columns)."'>Tabulka uživatelů je prázdná.</td></tr>";
    }
    echo "</tbody>";
    echo "</table>";
?>