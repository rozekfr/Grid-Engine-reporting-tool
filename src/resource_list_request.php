<?php
    $script = "RL_requests.sh";
    $id = rand(0,100000);
    $file = "RL_response_$id.txt";
    $txt = "qstat -l {$_GET["rl"]} | grep -v SLAVE > $file";
    $myfile = file_put_contents($script, $txt.PHP_EOL , FILE_APPEND);

    echo $file;
?>