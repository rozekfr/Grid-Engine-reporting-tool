<?php
    //kódování a české znaky
    ini_set("default_charset", "UTF-8");
    header("Content-Type: text/html; UTF-8");
?>
<?php
    include "pripojeniDB.php";
    include "functions.php";

	$database = (empty($_GET["db"]) ? "sge_rt_stats_ulohy" : "{$_GET["db"]}");
    $select = $_GET["select"];
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

    //pokud je období
    if(!empty($_GET["od"]) and !empty($_GET["do"])){
        $database = "sge_rt_stats_ulohy";
        if($where == "1"){
            $where = "cas_startu BETWEEN '{$_GET["od"]}' AND '{$_GET["do"]}'";
        }
        else{
            $where .= " AND cas_startu BETWEEN '{$_GET["od"]}' AND '{$_GET["do"]}'";
        }
    }

    //LIMIT
    //počet položek
    $pocetPolozek = (!empty($_GET["polozky"]) ? $_GET["polozky"] : 10);
    //stránka
    $stranka = (!empty($_GET["stranka"]) ? $_GET["stranka"] : 1);
    $limit = "LIMIT ".($stranka-1)*$pocetPolozek.",".$pocetPolozek;


    //dotaz do DB
    $dotazJobs = $db->query("SELECT $select FROM $database WHERE $where ORDER BY $orderby");

    //nastavení statistik - filtrů a stránkování
    echo "<div id='stats_settings'>";
    echo "<span>Počet položek: </span>";
    echo "<select id='pocet_polozek' onchange='update_stats(null,1,\"ulohy_efektivita_ajax\",\"jobs_stats\")'>";
    echo "<option value='5' ".($pocetPolozek == "5" ? "selected" : "").">5</option>";
    echo "<option value='10' ".($pocetPolozek == "10" ? "selected" : "").">10</option>";
    echo "<option value='25' ".($pocetPolozek == "25" ? "selected" : "").">25</option>";
    echo "<option value='50' ".($pocetPolozek == "50" ? "selected" : "").">50</option>";
    echo "<option value='100' ".($pocetPolozek == "100" ? "selected" : "").">100</option>";
    echo "</select>";
    echo "<span>Stránka: </span>";
    echo "<select id='strankovani' onchange='update_stats(this,1,\"ulohy_efektivita_ajax\",\"jobs_stats\")'>";
    $pocet = ceil($dotazJobs->num_rows/$pocetPolozek);
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
    echo "<input type='button' id='filtr' value='aplikovat filtry' onclick='update_stats(null,1,\"ulohy_efektivita_ajax\",\"jobs_stats\")'>";
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
                    case 'id_ulohy':
                        echo "<th>ID úlohy <img src='foto/".($order == $column ? ($howorder == "ASC" ? "vzestupne" : "sestupne") : "neradit").".png' id='id_ulohy' class='razeni' alt='razeni' onclick='update_stats(this,1,\"ulohy_efektivita_ajax\",\"jobs_stats\");'></th>";
                        break;

                    case 'uzivatel':
                        echo "<th>Uživatel <img src='foto/".($order == $column ? ($howorder == "ASC" ? "vzestupne" : "sestupne") : "neradit").".png' id='uzivatel' class='razeni' alt='razeni' onclick='update_stats(this,1,\"ulohy_efektivita_ajax\",\"jobs_stats\");'></th>";
                        break;

                    case 'efektivita':
                        echo "<th>Efektivita výpočtu <img src='foto/".($order == $column ? ($howorder == "ASC" ? "vzestupne" : "sestupne") : "neradit").".png' id='$column' class='razeni' alt='razeni' onclick='update_stats(this,1,\"ulohy_efektivita_ajax\",\"jobs_stats\");'></th>";
                        break;

                    case 'alokovana_pamet_MB':
                        echo "<th>Alokace paměti <img src='foto/".($order == $column ? ($howorder == "ASC" ? "vzestupne" : "sestupne") : "neradit").".png' id='$column' class='razeni' alt='razeni' onclick='update_stats(this,1,\"ulohy_efektivita_ajax\",\"jobs_stats\");'></th>";
                        break;

                    default:
                        echo "<th>".ucfirst($column)." <img src='foto/".($order == $column ? ($howorder == "ASC" ? "vzestupne" : "sestupne") : "neradit").".png' id='$column' class='razeni' alt='razeni' onclick='update_stats(this,1,\"ulohy_efektivita_ajax\",\"jobs_stats\");'></th>";
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
        echo "<td class='filtr'><input type='text' class='filtr_input' id='filtr_{$column}' autocomplete='off' value='".(isset($filtry[$i]) ? "$filtry[$i]" : "")."' onkeyup='check_filters()'></td>";
        $i++;
    }
    echo "</tr>";
    //data
    $dotazJobs = $db->query("SELECT $select,vyuzita_pamet_MB,max_vyuzita_pamet_MB FROM $database WHERE $where ORDER BY $orderby $limit");
    if(is_object($dotazJobs) and $dotazJobs->num_rows != 0){
        while($job = $dotazJobs->fetch_assoc()){
            echo "<tr>";
            foreach($columns as $column){
                if($column == "alokovana_pamet_MB"){
                    if(is_null($job[$column])){
                        echo "<td class='worse'>-</td>";
                    }
                    else{
                        $dolniHranice = $job["vyuzita_pamet_MB"];
                        $horniHranice = $job["max_vyuzita_pamet_MB"] * 1.2;
                        if($job[$column] < $dolniHranice){
                            echo "<td class='worse'>".write_memory($job[$column])." (využito: ".write_memory($job["vyuzita_pamet_MB"]).", max: ".write_memory($job["max_vyuzita_pamet_MB"]).")</td>";
                        }
                        else if($job[$column] > $horniHranice){
                            echo "<td class='bad'>".write_memory($job[$column])." (využito: ".write_memory($job["vyuzita_pamet_MB"]).", max: ".write_memory($job["max_vyuzita_pamet_MB"]).")</td>";
                        }
                        else{
                            echo "<td class='good'>".write_memory($job[$column])." (využito: ".write_memory($job["vyuzita_pamet_MB"]).", max: ".write_memory($job["max_vyuzita_pamet_MB"]).")</td>";
                        }
                    }
                }
                else if($column == "efektivita"){
                    if(is_null($job[$column])){
                        echo "<td class='worse'>-</td>";
                    }
                    else{
                        if($job[$column] > 110.00){
                            echo "<td class='bad'>".number_format($job[$column], 2, ",", " ")."%</td>";
                        }
                        else if($job[$column] <= 50.00){
                            echo "<td class='worse'>".number_format($job[$column], 2, ",", " ")."%</td>";
                        }
                        else{
                            echo "<td class='good'>".number_format($job[$column], 2, ",", " ")."%</td>";
                        }
                    }
                }
                else {
                    echo "<td>{$job[$column]}</td>";
                }
            }
            echo "</tr>";
        }
    }
    else{
        echo "<tr><td colspan='".count($columns)."'>Tabulka efektivity úloh je prázdná.</td></tr>";
    }
    echo "</tbody>";
    echo "</table>";
?>