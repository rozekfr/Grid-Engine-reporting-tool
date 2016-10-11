<?php
    //čeština
    ini_set("default_charset", "UTF-8");
    header("Content-Type: text/html; UTF-8");
    (empty($_GET["m"]) ? $_GET["m"] = "cluster" : $_GET["m"] = $_GET["m"]);
    (empty($_GET["p"]) ? $_GET["p"] = "prehled" : $_GET["p"] = $_GET["p"]);
?>
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
                    if(preg_match_all("/.*gc:/",$line,$matches)){
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
                            $total_gpus += intval($gpu_info[$resources["name"]][1]);
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
                            $gpu = intval($tmp[1]);
	                        //přičítám pouze dostupné
	                        if($resources["available"] == "ano") {
		                        $free_gpus += $gpu;
	                        }
                        }
                        //memory free
                        if (preg_match_all("/.*hl:mem_free.*/", $line, $matches)) {
                            $mem_free = my_unit($tmp[1]);
                        }
                        if (preg_match_all("/.*hc:ram_free.*/", $line, $matches)) {
                            if(!isset($mem_free)) {
                                $mem_free = my_unit($tmp[1]);
                            }
                        }
                        //memory total
                        if(preg_match_all("/.*hl:mem_total.*/", $line, $matches)) {
                            $mem_total = my_unit($tmp[1]);
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
                $resources["memory_total"] = (isset($mem_total) ? $mem_total : null);
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

    echo "<p><b>Zachyceno: </b>$zachyceno</p>";
    echo "<h1>Sloty</h1>";
    echo "<table>";
    echo "<tr><th>Využité:</th><td>$used_slots</td></tr>";
    echo "<tr><th>Dostupné:</th><td>".($total_slots - $used_slots)."</td></tr>";
    echo "</table>";
    echo "<h1>Úlohy</h1>";
    echo "<table>";
    echo "<tr><th>Běžící:</th><td>$used_slots</td></tr>";
    echo "<tr><th>Čekající:</th><td>$pending_jobs</td></tr>";
    echo "</table>";
    echo "<h1>Globální prostředky</h1>";
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse; text-align: center; width: 200px'; margin: auto;>";
    echo "<tr><th>Název</th><th>Hodnota</th></tr>";
    for($i = 0; $i < count($global_resources); $i++){
        echo "<tr><td>{$global_resources[$i]["name"]}</td><td>{$global_resources[$i]["value"]}</td></tr>";
    }
    echo "</table>";
    echo "<h1>Grafické karty</h1>";
    echo "<table>";
    echo "<tr><th>Volné:</th><td>$free_gpus</td></tr>";
    echo "<tr><th>Využité:</th><td>".($total_gpus - $free_gpus)."</td></tr>";
    echo "<tr><th>Celkem:</th><td>$total_gpus</td></tr>";
    echo "</table>";
    echo "<h1>Uzly</h1>";
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse; text-align: center; width: 800px'; margin: auto;>";
    echo "<tr><th>Název</th><th>Dostupný</th><th>Sloty využité/celkem</th><th>Využití CPU [%]</th><th>Využití paměti (volná/celkem)</th><th>Místo na disku</th><th>Grafické karty</th></tr>";
    for($i = 0; $i < count($nodes); $i++){
        echo "<tr><td>{$nodes[$i]["name"]}</td><td>{$nodes[$i]["available"]}</td><td>{$nodes[$i]["used_slots"]}/{$nodes[$i]["total_slots"]}</td><td>{$nodes[$i]["cpu"]}</td><td>{$nodes[$i]["memory_free"]}/{$nodes[$i]["memory_total"]}</td><td>{$nodes[$i]["disk_free"]}</td><td>{$nodes[$i]["gpu_free"]}/{$nodes[$i]["gpu_total"]}</td></tr>";
    }
    echo "</table>";
?>