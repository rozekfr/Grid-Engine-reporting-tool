<?php
/** Vrací přepočítanou hodnotu podle daných jednotek ve $v na GB
 * @param $v hodnota z qstat obsahující jednotky (G,M,T)
 * @return float přepočítaná jednotka na GB
 */
function my_unit($v){
    $value = substr($v, 0, strlen($v) - 1);
    $unit = substr($v,strlen($v) - 1, 1);
    switch($unit){
        case "T":
            $value = $value * 1024;
            break;

        case "G":
            $value = $value;
            break;

        case "M":
            $value = $value / 1024;
            break;
    }
    return floatval($value);
}

//získání informací o GPU
$gpus = file_get_contents("gpu_info");
$gpus = explode("\n",$gpus);
$total_gpus = 0;
$gpu_info = array();
$matches = array();
foreach($gpus as $node){
    if(!empty($node)){
        $n = explode(":",$node);
        $gpu_info[$n[0]] = $n;
        //počítáme jenom globální
        if (preg_match_all("/.*gpu/", $n[0], $matches)){
            if(!preg_match_all("/.*-gpu/",$n[0],$matches)) {
                $total_gpus += intval($n[1]);
            }
        }
    }
}

//parser
$used_slots = 0;
$total_slots = 0;
$free_gpus = 0;
$global_resources = array();
$nodes = array();

$qstat = file_get_contents("qstat");
$qstat = preg_split("/.*###.*/",$qstat);

$time = filemtime("qstat");

$zachyceno = Date("l",$time)." ".Date("j",$time).". ".Date("n",$time).". ".Date("Y",$time)." ".Date("H",$time).":".Date("i",$time).":".Date("s",$time);
//queues
$queues = $qstat[0];
$queues = preg_split("/.*---.*/",$queues);
foreach ($queues as $index => $queue) {
    $q = explode("\n", $queue);
    if($index > 0){
        foreach ($q as $i => $line){
            //global resources
            if ($index == 1) {
                $matches = array();
                if(preg_match_all("/.*matylda.*|.*scratch.*/",$line,$matches)){
                    $global_resource = array();
                    $tmp = explode("=",$line);
                    $tmp2 = explode(":",$tmp[0]);
                    $global_resource["name"] = $tmp2[1];
                    $global_resource["value"] = $tmp[1];
                    array_push($global_resources,$global_resource);
                }
            }
            //nodes
            if ($i == 1){
                //vynulování předchozích hodnot
                unset($cpu);
                unset($mem_free);
                unset($disk);
                unset($gpu);
                unset($slots);

                $done =  0;
                $l = explode(" ", $line);
                $l1 = array();
                foreach ($l as $v) {
                    empty($v) ?: array_push($l1, $v);
                }
                $resources = array();
                $tmp = explode("@",trim($l1[0]));
                $tmp2 = explode(".",$tmp[1]);
                $resources["queuetype"] = $tmp[0];
                $resources["name"] = $tmp2[0];



                //sloty - pokud je uzel dostupný
                if(empty($l1[5])){
                    $resources["available"] = "ano";
                    $slots = explode("/", trim($l1[2]));
                    //u long front beru jen obsazené sloty
                    if($resources["queuetype"] == "long.q"){
                        $used_slots += $slots[1];
                    }
                    else {
                        $used_slots += $slots[1];
                        $total_slots += $slots[2];
                    }
                }
                else{
                    $resources["available"] = "ne";
                    $slots = array(0,0,0); //nedostupné
                }
            }
            //inner resources
            else if($i > 1 and $resources["queuetype"] != "long.q"){
                $matches = array();
                if (preg_match_all("/^.*[ghq][lLcf]:/", $line, $matches)) {
                    $tmp = explode("=", $line);
                    //cpu
                    if (preg_match_all("/.*hl:cpu.*/", $line, $matches)) {
                        $cpu = $tmp[1];
                    }
                    //gpu
                    if (preg_match_all("/.*hc:gpu.*/", $line, $matches)) {
                        //počítáme jenom globální
                        if (preg_match_all("/.*gpu/", $resources["name"], $matches)) {
                            if(!preg_match_all("/.*-gpu/",$resources["name"],$matches)){
                                $free_gpus += intval($tmp[1]);
                                $gpu = intval($tmp[1]);
                            }
                            else{
                                $gpu = intval($tmp[1]);
                            }
                        }
                    }
                    //memory
                    if (preg_match_all("/.*hl:mem_free.*/", $line, $matches)) {
                        $mem_free = my_unit($tmp[1]);
                    }
                    if (preg_match_all("/.*hc:ram_free.*/", $line, $matches)) {
                        if(!isset($mem_free)) {
                            $mem_free = my_unit($tmp[1]);
                        }
                    }
                    //disk
                    if (preg_match_all("/.*hl:disk_free.*/", $line, $matches)) {
                        $disk = my_unit($tmp[1]);
                    }
                }
            }
        }
        //pokud není long.q
        if($resources["queuetype"] != "long.q"){
            $resources["cpu"] = (isset($cpu) ? number_format($cpu,2) : null);
            $resources["memory_free"] = (isset($mem_free) ? $mem_free : null);
            $resources["disk_free"] = (isset($disk) ? $disk : null);
            $resources["gpu_free"] = (isset($gpu) ? $gpu : 0);
            $resources["gpu_total"] = $gpu_info[$resources["name"]][1];
            $resources["used_slots"] = intval($slots[1]);
            $resources["total_slots"] = intval($slots[2]);
            array_push($nodes,$resources);
        }
        //pokud je pouze přičtu obsazené sloty
        else{
            for($i = 0; $i < count($nodes); $i++){
                if($nodes[$i]["name"] == $resources["name"]){
                    $nodes[$i]["used_slots"] += intval($slots[1]);
                    break;
                }
            }
        }
    }
}
//pending jobs
$pending_jobs = 0;
$jobs = $qstat[2];
$jobs = explode("\n",$jobs);
foreach ($jobs as $job){
    if(!empty($job)) {
        $j = explode(" ", $job);
        $columns = array();
        foreach ($j as $j1) {
            if(!empty($j1)){
                array_push($columns,$j1);
            }
        }
        $tasks = 1;
        if (isset($columns[7])){
            $tasks = 0;
            $t = explode(",",$columns[7]);
            foreach($t as $task){
                $tmp = explode(":", $task);
                $rozsah = $tmp[0];
                $krok = isset($tmp[1]) ? intval($tmp[1]) : 1;
                if (strpos($rozsah,"-") !== false){
                    $tmp = explode("-", $rozsah);
                    $start = intval($tmp[0]);
                    $end = intval($tmp[1]);
                    $tasks += ($end - $start)/$krok + 1;
                }
                else{
                    $tasks++;
                }

            }
        }
        else{
            $tasks++;
        }
        $pending_jobs += $tasks;
    }
}
//konec parseru

$directory = "rrd_databaze";

//sloty
$slotsRRD = $directory."/"."slots.rrd";
$zabrane = floatval($used_slots);
$dostupne = floatval($total_slots - $used_slots);
$ret = rrd_update($slotsRRD, "$time:$zabrane:$dostupne");
if($ret){
    echo "S: Updated ";
}
else{
    echo "S: Error: ".rrd_error()." ";
}

//úlohy
$jobsRRD = $directory."/"."jobs.rrd";
$cekajici = floatval($pending_jobs);
$provadene = floatval($used_slots);
$ret = rrd_update($jobsRRD, "$time:$cekajici:$provadene");
if($ret){
    echo "J: Updated ";
}
else{
    echo "J: Error: ".rrd_error()." ";
}

//grafiky
$jobsRRD = $directory."/"."GPUs.rrd";
$used_gpus = floatval($total_gpus - $free_gpus);
$total_gpus = floatval($total_gpus - $used_gpus);
$ret = rrd_update($jobsRRD, "$time:$used_gpus:$total_gpus");
if($ret){
    echo "G: Updated ";
}
else{
    echo "G: Error: ".rrd_error()." ";
}

//globální prosředky
foreach($global_resources as $gr){
    $gr["value"] = floatval($gr["value"]);
    $ret = rrd_update($directory."/".$gr["name"].".rrd", "$time:{$gr["value"]}");
    if($ret){
        echo "{$gr["name"]}: Updated ";
    }
    else{
        echo "{$gr["name"]}: ".rrd_error()." ";
    }
    $ret = rrd_update($directory."/".$gr["name"]."_MAX.rrd", "$time:{$gr["value"]}");
    if($ret){
        echo "{$gr["name"]}_MAX: Updated ";
    }
    else{
        echo "{$gr["name"]}_MAX: ".rrd_error()." ";
    }
}

//uzly
foreach($nodes as $node){
    $cpu = floatval($node["cpu"]);
    $mem = floatval($node["memory_free"]);
    $disk = floatval($node["disk_free"]);
    $gpu_used = floatval($node["gpu_total"] - $node["gpu_free"]);
    $gpu_total = floatval($node["gpu_total"] - $gpu_used);
    $used_slots = floatval($node["used_slots"]);
    $free_slots = floatval($node["total_slots"] - $node["used_slots"]);
    $ret = rrd_update($directory."/".$node["name"].".rrd", "$time:$cpu:$mem:$disk:$gpu_used:$gpu_total:$used_slots:$free_slots");
    if($ret){
        echo "{$node["name"]}: Updated ";
    }
    else{
        echo "{$node["name"]}: ".rrd_error()." ";
    }
}

echo "\n";
?>