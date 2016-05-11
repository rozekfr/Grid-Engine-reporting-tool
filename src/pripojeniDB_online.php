<?php

    $db = new mysqli('localhost', 'xrozek01', 'ar6ehemu', 'xrozek01');
    if ($db->connect_errno) {
        echo "Omlouváme se, ale nepodařilo se nam připojit k databázi.";
        exit();
    }
    else{
        $db->set_charset('utf8');
    }
?>