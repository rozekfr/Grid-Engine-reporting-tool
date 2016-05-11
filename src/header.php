<?php
/**
 * Toto je script, který přiděluje hlavičky stránek do head.
 */
switch($_GET["m"]){
    case 'cluster':
        include 'cluster_header.php';
        break;

    case 'uzivatele':
        include 'uzivatele_header.php';
        break;

    case 'ulohy':
        include 'ulohy_header.php';
        break;

    case 'prostredky':
        include 'prostredky_header.php';
        break;
}
?>