<?php
    //kódování a české znaky
    ini_set("default_charset", "UTF-8");
    header("Content-Type: text/html; UTF-8");
?>
<?php
    if(file_exists($_GET["file"])){
        $file = file_get_contents($_GET["file"]);
        if(!empty($file)) {
            $file = preg_split("/.*---.*/", $file);
            $rows = explode("\n", $file[1]);
            $jobs = array();
            foreach ($rows as $row) {
                if (!empty($row)) {
                    $data = explode(" ", $row);
                    $job = array();
                    foreach ($data as $item) {
                        if (!empty($item)) {
                            array_push($job, $item);
                        }
                    }
                    $jobs[$job[0]] = $job;
                }
            }
            ksort($jobs);
            echo "<table>";
            echo "<tr><th>ID úlohy</th><th>Uživatel</th><th>Čas startu/odeslání</th><th>Stav</th><th>Další informace</th></tr>";
            foreach ($jobs as $job) {
                //datum a čas
                $date = explode("/", $job[5]);
                $datetime = "$date[2]-$date[0]-$date[1] $job[6]";
                echo "<tr><td>{$job[0]}</td><td>{$job[3]}</td><td>{$datetime}</td><td>{$job[4]}</td><td><input type='button' value='zjistit' onclick='get_info_request($job[0]);'></td></tr>";
            }
            echo "</table>";
        }
        else{
            echo "<p class='info'>Žádné úlohy vámi zadané prostředky neblokují.</p>";
        }
    }
?>