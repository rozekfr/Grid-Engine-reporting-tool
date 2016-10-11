<?php
/*
 * Tento script slouží k vytvoření všech potřebných rrd databází pro správnou funkčnost nástroje.
 */

/** Vytvoří databázi pro sloty.
 * @param $directory Složka, do které se databáze vytvoří.
 */
function create_slots_rrd($directory){

    $rrd_file = "slots.rrd";

    $_opts = array("--start", "now",
        "--step", "60",
        "DS:vyuzite:GAUGE:120:0:U",
        "DS:dostupne:GAUGE:120:0:U",
        "RRA:AVERAGE:0.5:1:7200",
        "RRA:AVERAGE:0.5:5:6048",
        "RRA:AVERAGE:0.5:30:2880",
        "RRA:AVERAGE:0.5:120:43800",
    );

    if(file_exists($directory."/".$rrd_file)){
        echo "RRD databáze pro sloty už existuje. Nebyla vytvořena ani změněna.\n";
    }
    else {
        $ret = rrd_create($directory . "/" . $rrd_file, $_opts, count($_opts));

        if ($ret) {
            echo "RRD databáze pro sloty vytvořena.\n";
        } else {
            $err = rrd_error();
            echo "RRD databázi pro sloty se nepovedlo vytvořit!\n";
            echo $err . "\n";
        }
    }
}

/** Vytvoří databázi pro úlohy.
 * @param $directory Složka, do které se databáze vytvoří.
 */
function create_jobs_rrd($directory){
    $rrd_file = "jobs.rrd";

    $_opts = array("--start", "now",
        "--step", "60",
        "DS:cekajici:GAUGE:120:0:U",
        "DS:provadene:GAUGE:120:0:U",
        "RRA:AVERAGE:0.5:1:7200",
        "RRA:AVERAGE:0.5:5:6048",
        "RRA:AVERAGE:0.5:30:2880",
        "RRA:AVERAGE:0.5:120:43800"
    );

    if(file_exists($directory."/".$rrd_file)){
        echo "RRD databáze pro úlohy už existuje. Nebyla vytvořena ani změněna.\n";
    }
    else {
        $ret = rrd_create($directory . "/" . $rrd_file, $_opts, count($_opts));

        if ($ret) {
            echo "RRD databáze pro úlohy vytvořena.\n";
        } else {
            $err = rrd_error();
            echo "RRD databázi pro úlohy se nepovedlo vytvořit!\n";
            echo $err . "\n";
        }
    }
}

/**Vytvoří databázi pro grafiky.
 * @param $directory Složka, do které se databáze vytvoří.
 */
function create_gpu_rrd($directory){
    $rrd_file = "GPUs.rrd";

    $_opts = array("--start", "now",
        "--step", "60",
        "DS:obsazene:GAUGE:120:0:U",
        "DS:dostupne:GAUGE:120:0:U",
        "RRA:AVERAGE:0.5:1:7200",
        "RRA:AVERAGE:0.5:5:6048",
        "RRA:AVERAGE:0.5:30:2880",
        "RRA:AVERAGE:0.5:120:43800"
    );

    if(file_exists($directory."/".$rrd_file)){
        echo "RRD databáze pro grafiky už existuje. Nebyla vytvořena ani změněna.\n";
    }
    else {
        $ret = rrd_create($directory . "/" . $rrd_file, $_opts, count($_opts));

        if ($ret) {
            echo "RRD databáze pro grafiky vytvořena.\n";
        } else {
            $err = rrd_error();
            echo "RRD databázi pro grafiky se nepovedlo vytvořit!\n";
            echo $err . "\n";
        }
    }
}

/** Vytvoří databázi pro globální zdroj pro průměrnou hodnotu.
 * @param $directory Složka, do které se databáze vytvoří.
 * @param $name Jméno globálního zdroje.
 */
function create_gr_rrd($directory, $name){
    $rrd_file = "$name.rrd";

    $_opts = array("--start", "now",
        "--step", "60",
        "DS:$name:GAUGE:120:0:100",
        "RRA:AVERAGE:0.5:1:7200",
        "RRA:AVERAGE:0.5:5:6048",
        "RRA:AVERAGE:0.5:30:2880",
        "RRA:AVERAGE:0.5:120:43800",
    );

    if(file_exists($directory."/".$rrd_file)){
        echo "RRD databáze pro globální zdroj $name už existuje. Nebyla vytvořena ani změněna.\n";
    }
    else {
        $ret = rrd_create($directory . "/" . $rrd_file, $_opts, count($_opts));

        if ($ret) {
            echo "RRD databáze globálního zdroje $name vytvořena.\n";
        } else {
            $err = rrd_error();
            echo "RRD databázi globálního zdroje $name se nepovedlo vytvořit!\n";
            echo $err . "\n";
        }
    }
}

/** Vytvoří databázi pro globální zdroj pro maximální hodnotu.
 * @param $directory Složka, do které se databáze vytvoří.
 * @param $name Jméno globálního zdroje.
 */
function create_gr_max_rrd($directory, $name){
    $rrd_file = $name."_MAX.rrd";

    $_opts = array("--start", "now",
        "--step", "60",
        "DS:$name:GAUGE:120:0:100",
        "RRA:MAX:0.5:1:7200",
        "RRA:MAX:0.5:5:6048",
        "RRA:MAX:0.5:30:2880",
        "RRA:MAX:0.5:120:43800"
    );

    if(file_exists($directory."/".$rrd_file)){
        echo "RRD databáze pro globální zdroj ".$name."_MAX už existuje. Nebyla vytvořena ani změněna.\n";
    }
    else {
        $ret = rrd_create($directory . "/" . $rrd_file, $_opts, count($_opts));

        if ($ret) {
            echo "RRD databáze globálního zdroje " . $name . "_MAX vytvořena.\n";
        } else {
            $err = rrd_error();
            echo "RRD databázi globálního zdroje " . $name . "_MAX se nepovedlo vytvořit!\n";
            echo $err . "\n";
        }
    }
}

/** Vytvoří databázi pro uzel v SGE.
 * @param $directory Složka, do které se databáze vytvoří.
 * @param $name Jméno uzlu.
 */
function create_node_rrd($directory, $name){
    $rrd_file = "$name.rrd";

    $_opts = array("--start", "now",
        "--step", "60",
        "DS:cpu:GAUGE:120:0:100",
        "DS:mem_used:GAUGE:120:0:U",
        "DS:mem_total:GAUGE:120:0:U",
        "DS:disk_free:GAUGE:120:0:U",
        "DS:gpu_used:GAUGE:120:0:U",
        "DS:gpu_total:GAUGE:120:0:U",
        "DS:used_slots:GAUGE:120:0:U",
        "DS:free_slots:GAUGE:120:0:U",
        "RRA:AVERAGE:0.5:1:7200",
        "RRA:AVERAGE:0.5:5:6048",
        "RRA:AVERAGE:0.5:30:2880",
        "RRA:AVERAGE:0.5:120:43800"
    );

    if(file_exists($directory."/".$rrd_file)){
        echo "RRD databáze pro uzel $name už existuje. Nebyla vytvořena ani změněna.\n";
        return;
    }
    else {
        $ret = rrd_create($directory . "/" . $rrd_file, $_opts, count($_opts));

        if ($ret) {
            echo "RRD databáze uzlu $name vytvořena.\n";
        } else {
            $err = rrd_error();
            echo "RRD databázi uzlu $name se nepovedlo vytvořit!\n";
            echo $err . "\n";
        }
    }
}

$directory = "rrd_databaze";

//sloty
create_slots_rrd($directory);

//úlohy
create_jobs_rrd($directory);

//grafiky
create_gpu_rrd($directory);

//globalní prostředky
$handle = fopen("qstat","r");
$qstat = fread($handle, filesize("qstat"));
$qstat = preg_split("/.*###.*/",$qstat);

$queues = $qstat[0];
$queues = preg_split("/.*---.*/",$queues);
$q = explode("\n",$queues[1]);
$matches = array();
foreach($q as $line){
    $line = trim($line);
    if (preg_match_all("/^gc:.*$/", $line, $matches)) {
        $tmp = explode("=", $line);
        $tmp2 = explode(":", $tmp[0]);
        $name = $tmp2[1];
        create_gr_rrd($directory,$name);
        create_gr_max_rrd($directory,$name);
    }
}

//uzly
$handle = fopen("groups","r");
$groups = fread($handle, filesize("groups"));

$groups = explode("\n",$groups);
$unique_nodes = array();
foreach($groups as $group){
    if(!empty($group)) {
        $tmp = explode(":", $group);
        $nodes = explode(",", $tmp[1]);
        foreach ($nodes as $node) {
            if (array_search($node, $unique_nodes) === false and !empty($node)){
                array_push($unique_nodes, $node);
                create_node_rrd($directory,$node);
            }
        }
    }
}

?>