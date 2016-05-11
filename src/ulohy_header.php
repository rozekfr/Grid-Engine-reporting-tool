<?php
    //styly
    if(!empty($_GET["s"]) and ($_GET["s"] == "blokujici")){
        echo "<link rel='stylesheet' type='text/css' href='css/statistiky.css'>";
        echo "<link rel='stylesheet' type='text/css' href='css/ulohy.css'>";
    }
    else{
        echo "<link rel='stylesheet' type='text/css' href='css/statistiky.css'>";
    }
    //scripty
    if(empty($_GET["s"]) or (!empty($_GET["s"]) and $_GET["s"] == "statistiky")){
        echo "<script type='text/javascript' src='js/ulohy.js'></script>";
        echo "<script type='text/javascript' src='js/stats.js'></script>";
    }
    else if(!empty($_GET["s"]) and $_GET["s"] == "efektivita"){
        echo "<script type='text/javascript' src='js/ulohy_efektivita.js'></script>";
        echo "<script type='text/javascript' src='js/stats.js'></script>";
    }
    else if(!empty($_GET["s"]) and $_GET["s"] == "cekajici"){
        echo "<script type='text/javascript' src='js/ulohy_cekajici.js'></script>";
        echo "<script type='text/javascript' src='js/stats.js'></script>";
        echo "<script type='text/javascript' src='js/ulohy_on_demand_requests.js'></script>";
    }
    else if(!empty($_GET["s"]) and $_GET["s"] == "blokujici"){
        echo "<script type='text/javascript' src='js/ulohy_on_demand_requests.js'></script>";
    }
?>