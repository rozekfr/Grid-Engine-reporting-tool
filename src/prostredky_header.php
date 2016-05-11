<?php
    //styly
    echo "<link rel='stylesheet' type='text/css' href='css/prostredky.css'>";
    //scripty
    echo "<script type='text/javascript' src='js/javascriptrrd/javascriptrrd.wlibs.js'></script>";
    if(!empty($_GET["s"]) and $_GET["s"] == "globals"){
        if(!empty($_GET["n"]) and $_GET["n"] == "servers"){
            echo "<script type='text/javascript' src='js/prostredky_servery.js'></script>";
        }
        else if(!empty($_GET["n"]) and $_GET["n"] == "gpus"){
            echo "<script type='text/javascript' src='js/prostredky_gpu.js'></script>";
        }
    }
    else{
        echo "<script type='text/javascript' src='js/prostredky_nodes.js'></script>";
    }
?>