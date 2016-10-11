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

include 'functions.php';
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
<div id="black"></div>
<div id="upravy" onmouseover="zobrazUpravu()">
    <a class="zavri" onclick="zavriUpravu();">X</a>
    <?php
        //úprava spotřeby
        if(!empty($_GET["u"]) and $_GET["u"] == "us" and isset($_POST["konfigurace"])){
            if(!empty($_POST["konfigurace"])) {
                $ret = $db->query("UPDATE konfigurace SET pocet_slotu='{$_POST["sloty"]}',prikon_uzlu='{$_POST["prikon_uzlu"]}',prikon_gpu='{$_POST["prikon_gpu"]}' WHERE nazev='{$_POST["konfigurace"]}'");
                if(!$ret){
                    print_error($db,"Nepovedlo se upravit spotřebu u konfigurace {$_GET["k"]}!");
                }
                else{
                    header("Location: spotreba.php?s={$_GET["s"]}&k={$_GET["k"]}");
                }
            }
            else{
                print_error($db,"Musíte zadat konfiguraci!");
            }
        }
        //formulář pro úpravu spotřeby
        if(!empty($_GET["u"]) and $_GET["u"] == "us" and !isset($_POST["konfigurace"])){
            $dotazSpotreba = $db->query("SELECT pocet_slotu,prikon_uzlu,prikon_gpu FROM konfigurace WHERE nazev='{$_GET["k"]}'");
            $spotreba = $dotazSpotreba->fetch_assoc();
            echo "<form action='spotreba.php?s={$_GET["s"]}&amp;k={$_GET["k"]}&amp;u=us' method='POST'>";
            echo "<h1>Úprava spotřeby</h1>";
            echo "<table>";
            echo "<tr><th>Konfigurace:</th><td><input type='text' name='konfigurace' readonly value='{$_GET["k"]}'></td></tr>";
            echo "<tr><th>Počet jader (slotů):</th><td><input type='number' name='sloty' min='1' value='{$spotreba["pocet_slotu"]}'></td></tr>";
            echo "<tr><th>Příkon uzlu:</th><td><input type='number' name='prikon_uzlu' min='0' value='{$spotreba["prikon_uzlu"]}'> W</td></tr>";
            echo "<tr><th>Příkon GPU:</th><td><input type='number' name='prikon_gpu' min='0' value='{$spotreba["prikon_gpu"]}'> W</td></tr>";
            echo "<tr><td></td><td><input type='submit' value='Upravit spotřebu'></td></tr>";
            echo "</table>";
            echo "</form>";
        }

        //smazání spotřeby
        if(!empty($_GET["u"]) and $_GET["u"] == "ss"){
            $ret = $db->query("UPDATE konfigurace SET prikon_uzlu=NULL,prikon_gpu=NULL WHERE nazev='{$_GET["k"]}'");
            if(!$ret){
                print_error($db,"Nepodařilo se vymazat spotřebu u konfigurace {$_GET["k"]}");
            }
            else{
                header("Location: spotreba.php?s={$_GET["s"]}&k={$_GET["k"]}");
            }
        }
    ?>

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
        <h1>Spotřeba uzlů</h1>
        <p class="info">V této sekci administrátor může definovat spotřebu jednotlivých uzlů.</p>
        <?php
            if(empty($_GET["k"])){
                echo "<table>";
                echo "<tr><th>Konfigurace</th><th>Počet jader (slotů)</th><th>Příkon uzlu</th><th>Příkon GPU</th><th>Informace</th></tr>";
                $dotazSpotreba = $db->query("SELECT nazev,pocet_slotu,prikon_uzlu,prikon_gpu FROM konfigurace");
                if($dotazSpotreba->num_rows == 0){
                    echo "<tr><td colspan='4'>Nejsou k dispozici žádné konfigurace se spotřebou.</td></tr>";
                }
                else{
                    while($spotreba = $dotazSpotreba->fetch_assoc()){
                        echo "<tr><td>{$spotreba["nazev"]}</td><td>{$spotreba["pocet_slotu"]}</td><td>".(!is_null($spotreba["prikon_uzlu"]) ? "{$spotreba["prikon_uzlu"]} W" : "")."</td><td>".(!is_null($spotreba["prikon_gpu"]) ? "{$spotreba["prikon_gpu"]} W" : "")."</td><td><a href='spotreba.php?s={$_GET["s"]}&amp;k={$spotreba["nazev"]}'>detail</a></td></tr>";
                    }
                }
                echo "</table>";
            }
            else{
                $dotazSpotreba = $db->query("SELECT nazev,pocet_slotu,prikon_uzlu,prikon_gpu FROM konfigurace WHERE nazev='{$_GET["k"]}'");
                $spotreba = $dotazSpotreba->fetch_assoc();
                echo "<table>";
                    echo "<tr><th>Konfigurace:</th><td>{$spotreba["nazev"]}</td></tr>";
                    echo "<tr><th>Počet jader (slotů):</th><td>".(!is_null($spotreba["pocet_slotu"]) ? "{$spotreba["pocet_slotu"]}" : "")."</td></tr>";
                    echo "<tr><th>Příkon uzlu:</th><td>".(!is_null($spotreba["prikon_uzlu"]) ? "{$spotreba["prikon_uzlu"]} W" : "")."</td></tr>";
                    echo "<tr><th>Příkon GPU:</th><td>".(!is_null($spotreba["prikon_gpu"]) ? "{$spotreba["prikon_gpu"]} W" : "")."</td></tr>";
                echo "</table>";
            }
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
