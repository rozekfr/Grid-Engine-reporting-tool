<?php
    $hostname = "localhost";
    $user = "root";
    $password = "";
	$database = "vut_bakalarka";
    $db = new mysqli($hostname, $user, $password, $database);
    if ($db->connect_errno) {
        echo "Omlouváme se, ale nepodařilo se nam připojit k databázi.";
        exit();
    }
    else{
        $db->set_charset('utf8');
    }
?>