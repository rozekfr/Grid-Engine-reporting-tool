<?php
/**
 * Toto je script, který přiděluje nastvení stránek do divu settings.
 */
switch($_GET["m"]){
    case 'cluster':
        include 'cluster_settings.php';
        break;

    case 'uzivatele':
        include 'uzivatele_settings.php';
        break;

    case 'ulohy':
        include 'ulohy_settings.php';
        break;

    case 'prostredky':
        include 'prostredky_settings.php';
        break;
}
?>