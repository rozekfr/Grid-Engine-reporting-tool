<?php
/**
 * Toto je script, který přiděluje obsah stránek do divu text.
 */
    switch($_GET["m"]){
        case 'cluster':
            include 'cluster.php';
            break;

        case 'uzivatele':
            include 'uzivatele.php';
            break;

        case 'ulohy':
            include 'ulohy.php';
            break;

        case 'prostredky':
            include 'prostredky.php';
            break;
    }
?>