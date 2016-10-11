<?php
//nastavení češtiny
ini_set("default_charset", "UTF-8");
header("Content-Type: text/html; UTF-8");
?>
<?php
session_start();
include '../pripojeniDB.php';
if(empty($_SESSION["login"])){
    header("Location: index.php");
}
//odhlášení
if(!empty($_GET["logout"])){
    unset($_SESSION["login"]);
    header("Location: index.php");
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" href="css/styly.css" type="text/css">
    <script src="js/script.js" type="text/javascript"></script>
    <title>Sun Grid Engine - Reporting tool | Administrace</title>
</head>
<body onload="<?php echo (!empty($_GET["u"]) ? "zobrazUpravu();" : "");?>">
<div id="upravy" onmouseover="zobrazUpravu()">
    <a class="zavri" onclick="zavriUpravu();">X</a>
</div>
<div id="stranka">
    <div id="top">
        <h1>Sun Grid Engine - Reporting tool - Administrace</h1>
        <a href="?logout=ano"><?php echo "odhlásit se ({$_SESSION["login"]})"?></a>
    </div>
    <div id="menu">
        <?php
        include 'menu.php';
        ?>
    </div>
    <div id="obsah">
        <h1>Přehled uzlů výpočetního clusteru</h1>
        <p class="info">V této sekci administrátor vidí přehled všech uzlů v clusteru s přiřazenou konfigurací a spotřebou.</p>
        <?php
            //seznam uzlů s konfiguracemi
            echo "<table>";
            echo "<tr><th>Uzel</th><th>Konfigurace</th><th>Počet jader (slotů)</th><th>Příkon uzlu</th><th>Příkon GPU</th></tr>";
            $dotazUzly = $db->query("SELECT uzly.nazev,id_konfigurace,pocet_slotu,prikon_uzlu,prikon_gpu FROM uzly LEFT JOIN konfigurace ON uzly.id_konfigurace=konfigurace.nazev ORDER BY nazev");
            if($dotazUzly->num_rows == 0){
                echo "<tr><td colspan='4'>Uzly nebyly importovány.</td></tr>";
            }
            else {
                while ($uzel = $dotazUzly->fetch_assoc()) {
                    echo "<tr><td>{$uzel["nazev"]}</td><td>{$uzel["id_konfigurace"]}</td><td>{$uzel["pocet_slotu"]}</td><td>{$uzel["prikon_uzlu"]}</td><td>{$uzel["prikon_gpu"]}</td></tr>";
                }
            }
            echo "</table>";
        ?>
    </div>
    <div id="podmenu">
        <?php
        include 'podmenu.php';
        ?>
    </div>
</div>
</body>
</html>
