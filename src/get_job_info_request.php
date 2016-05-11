<?php
    if(!empty($_GET["id"])){
        $txt = "qstat -j {$_GET["id"]} > PJ_response_{$_GET["id"]}.txt";
        $myfile = file_put_contents('PJ_requests.sh', $txt.PHP_EOL , FILE_APPEND);
    }
?>