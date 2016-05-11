<?php

    $db = new mysqli('localhost', 'root', '', 'vut_bakalarka');
    if ($db->connect_errno) {
        echo "Omlouváme se, ale nepodařilo se nam připojit k databázi.";
        exit();
    }
    else{
        $db->set_charset('utf8');
    }
?>