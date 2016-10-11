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
        //přidání konfigurace
        if(!empty($_GET["u"]) and $_GET["u"] == "pk" and isset($_POST["nazev"])){
            if(!empty($_POST["nazev"])){
                $dotazNazev = $db->query("SELECT nazev FROM konfigurace WHERE nazev='{$_POST["nazev"]}'");
                $nazev = $dotazNazev->fetch_assoc();
                if(empty($nazev["nazev"])) {
                    $ret = $db->query("INSERT INTO konfigurace (nazev,mb,cpu,pocet_slotu,ram,hdd,gpu,net,info) VALUE ('{$_POST["nazev"]}','{$_POST["mb"]}','{$_POST["cpu"]}','{$_POST["sloty"]}','{$_POST["ram"]}','{$_POST["hdd"]}','{$_POST["gpu"]}','{$_POST["net"]}','{$_POST["info"]}');");
                    if (!$ret) {
                        print_error($db,"Konfiguraci se nepovedlo vložit!");
                    }
                    else {
                        header("Location: konfigurace.php?s={$_GET["s"]}");
                    }
                }
                else{
                    print_error($db,"Název konfigurace už existuje, vyberte jiný!");
                }
            }
            else{
                print_error($db,"Název konfigurace nesmí být prázdný!");
            }
        }
        //formulář pro přídání konfigurace
        if(!empty($_GET["u"]) and $_GET["u"] == "pk" and !isset($_POST["nazev"])){
            echo "<form action='konfigurace.php?s={$_GET["s"]}&amp;u=pk' method='POST'>";
                echo "<h1>Přidání konfigurace</h1>";
                echo "<table>";
                    echo "<tr><th>Název konfigurace:</th><td><input type='text' name='nazev' value='".(isset($_POST["nazev"]) ? $_POST["nazev"] : "")."'></td></tr>";
                    echo "<tr><th>Základní deska:</th><td><input type='text' name='mb' value='".(isset($_POST["mb"]) ? $_POST["mb"] : "")."'></td></tr>";
                    echo "<tr><th>Procesor:</th><td><input type='text' name='cpu' value='".(isset($_POST["cpu"]) ? $_POST["cpu"] : "")."'></td></tr>";
                   echo "<tr><th>Počet jader (slotů):</th><td><input type='number' name='sloty' min='1' value='".(isset($_POST["sloty"]) ? $_POST["sloty"] : "")."'></td></tr>";
                    echo "<tr><th>Operační paměť:</th><td><input type='text' name='ram' value='".(isset($_POST["ram"]) ? $_POST["ram"] : "")."'></td></tr>";
                    echo "<tr><th>Pevný disk:</th><td><input type='text' name='hdd' value='".(isset($_POST["hdd"]) ? $_POST["hdd"] : "")."'></td></tr>";
                    echo "<tr><th>Grafická karta:</th><td><input type='text' name='gpu' value='".(isset($_POST["gpu"]) ? $_POST["gpu"] : "")."'></td></tr>";
                    echo "<tr><th>Síťová karta:</th><td><input type='text' name='net' value='".(isset($_POST["net"]) ? $_POST["net"] : "")."'></td></tr>";
                    echo "<tr><th>Další informace:</th><td><textarea name='info'>".(isset($_POST["info"]) ? $_POST["info"] : "")."</textarea></td></tr>";
                    echo "<tr><td></td><td><input type='submit' value='přidat konfiguraci'></td></tr>";
                echo "</table>";
            echo "</form>";
        }

        //úprava konfigurace
        if(!empty($_GET["u"]) and $_GET["u"] == "uk" and isset($_POST["nazev"])){
            if(!empty($_POST["nazev"])){
                $ret = $db->query("UPDATE konfigurace SET mb='{$_POST["mb"]}',cpu='{$_POST["cpu"]}',pocet_slotu='{$_POST["sloty"]}',ram='{$_POST["ram"]}',hdd='{$_POST["hdd"]}',gpu='{$_POST["gpu"]}',net='{$_POST["net"]}',info='{$_POST["info"]}' WHERE nazev='{$_GET["k"]}'");
                if(!$ret){
                    print_error($db,"Konfiguraci se nepovedlo upravit!");
                }
                else{
                    header("Location: konfigurace.php?s={$_GET["s"]}&k={$_GET["k"]}");
                }
            }
            else{
                print_error($db,"Název konfigurace nesmí být prázdný!");
            }
        }
        //formulář pro úpravu konfigurace
        if(!empty($_GET["u"]) and $_GET["u"] == "uk"){
            echo "<form action='konfigurace.php?s={$_GET["s"]}&amp;k={$_GET["k"]}&amp;u=uk' method='POST'>";
                $dotazKonfigurace = $db->query("SELECT * FROM konfigurace WHERE nazev='{$_GET["k"]}'");
                $konfigurace = $dotazKonfigurace->fetch_assoc();
                echo "<h1>Úprava konfigurace</h1>";
                echo "<table>";
                    echo "<tr><th>Název konfigurace:</th><td><input type='text' name='nazev' value='{$konfigurace["nazev"]}' readonly></td></tr>";
                    echo "<tr><th>Základní deska:</th><td><input type='text' name='mb' value='{$konfigurace["mb"]}'></td></tr>";
                    echo "<tr><th>Procesor:</th><td><input type='text' name='cpu' value='{$konfigurace["cpu"]}'></td></tr>";
                    echo "<tr><th>Počet jader (slotů):</th><td><input type='number' name='sloty' min='1' value='{$konfigurace["pocet_slotu"]}'></td></tr>";
                    echo "<tr><th>Operační paměť:</th><td><input type='text' name='ram' value='{$konfigurace["ram"]}'></td></tr>";
                    echo "<tr><th>Pevný disk:</th><td><input type='text' name='hdd' value='{$konfigurace["hdd"]}'></td></tr>";
                    echo "<tr><th>Grafická karta:</th><td><input type='text' name='gpu' value='{$konfigurace["gpu"]}'></td></tr>";
                    echo "<tr><th>Síťová karta:</th><td><input type='text' name='net' value='{$konfigurace["net"]}'></td></tr>";
                    echo "<tr><th>Další informace:</th><td><textarea name='info'>{$konfigurace["info"]}</textarea></td></tr>";
                    echo "<tr><td></td><td><input type='submit' value='upravit konfiguraci'></td></tr>";
                echo "</table>";
            echo "</form>";
        }

        //smazání konfigurace
        if(!empty($_GET["u"]) and $_GET["u"] == "sk"){
            $ret = $db->query("UPDATE uzly SET id_konfigurace=NULL WHERE id_konfigurace='{$_GET["k"]}'");
            if(!$ret){
                print_error($db,"Nepovedlo se odstranit konfiguraci z uzlů, ke kterým byla přidělena!");
            }
            else {
                $ret = $db->query("DELETE FROM konfigurace WHERE nazev='{$_GET["k"]}'");
                if(!$ret){
                    print_error($db,"Konfiguraci se nepovedlo odstranit!");
                }
                else{
                    header("Location: konfigurace.php?s={$_GET["s"]}");
                }
            }
        }

        //přidání konfigurace k uzlu
        if(!empty($_GET["u"]) and $_GET["u"] == "pu"){
            $ret = $db->query("UPDATE uzly SET id_konfigurace='{$_GET["k"]}' WHERE nazev='{$_GET["n"]}'");
            if(!$ret){
                print_error($db,"Nepovedlo se k uzlu přiřadit konfiguraci!");
            }
            else{
                header("Location: konfigurace.php?s={$_GET["s"]}&k={$_GET["k"]}");
            }
        }

        //odebrání konfigurace uzlu
        if(!empty($_GET["u"]) and $_GET["u"] == "ou"){
            $ret = $db->query("UPDATE uzly SET id_konfigurace=NULL WHERE nazev='{$_GET["n"]}'");
            if(!$ret){
                print_error($db,"Nepovedlo se odebrat uzlu konfiguraci!");
            }
            else{
                header("Location: konfigurace.php?s={$_GET["s"]}&k={$_GET["k"]}");
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
        <h1>Konfigurace uzlů</h1>
        <p class="info">V této sekci administrátor může vkládat konfigurace k jednotlivým uzlům.</p>
        <?php
            if(empty($_GET["k"])){
                echo "<table>";
                echo "<tr><th>Konfigurace</th><th>Informace</th></tr>";
                $dotazKonfigurace = $db->query("SELECT nazev FROM konfigurace");
                if($dotazKonfigurace->num_rows == 0){
                    echo "<tr><td colspan='2'>Nemáte definované žádné konfigurace.</td></tr>";
                }
                while($konfigurace = $dotazKonfigurace->fetch_assoc()){
                    echo "<tr><td>{$konfigurace["nazev"]}</td><td><a href='konfigurace.php?s={$_GET["s"]}&amp;k={$konfigurace["nazev"]}'>detail</a></td></tr>";
                }
                echo "</table>";
            }
            else{
                $dotazKonfigurace = $db->query("SELECT * FROM konfigurace WHERE nazev='{$_GET["k"]}'");
                $konfigurace = $dotazKonfigurace->fetch_assoc();
                echo "<table>";
                    echo "<tr><th>Název konfigurace</th><td>{$konfigurace["nazev"]}</td></tr>";
                    echo "<tr><th>Základní deska</th><td>{$konfigurace["mb"]}</td></tr>";
                    echo "<tr><th>Procesor</th><td>{$konfigurace["cpu"]}</td></tr>";
                    echo "<tr><th>Počet jader (slotů)</th><td>{$konfigurace["pocet_slotu"]}</td></tr>";
                    echo "<tr><th>Operační paměť</th><td>{$konfigurace["ram"]}</td></tr>";
                    echo "<tr><th>Pevný disk</th><td>{$konfigurace["hdd"]}</td></tr>";
                    echo "<tr><th>Grafická karta</th><td>{$konfigurace["gpu"]}</td></tr>";
                    echo "<tr><th>Síťová karta</th><td>{$konfigurace["net"]}</td></tr>";
                    echo "<tr><th>Další informace</th><td>{$konfigurace["info"]}</td></tr>";
                echo "</table>";

                //uzly s touto konfigurací
                echo "<div class='left'>";
                $dotazUzly = $db->query("SELECT nazev FROM uzly WHERE id_konfigurace='{$_GET["k"]}' ORDER BY nazev");
                    echo "<table>";
                    echo "<tr><th colspan='2'>Uzly s touto konfigurací</tr>";
                    if($dotazUzly->num_rows == 0){
                        echo "<tr><td colspan='2'>Tato konfigurace není přiřazena žádnému uzlu.</td></tr>";
                    }
                    else {
                        while ($uzly = $dotazUzly->fetch_assoc()) {
                            echo "<tr><td>{$uzly["nazev"]}</td><td><a href='konfigurace.php?s={$_GET["s"]}&amp;k={$_GET["k"]}&u=ou&amp;n={$uzly["nazev"]}'>odebrat</a</td></tr>";
                        }
                    }
                    echo "</table>";
                echo "</div>";

                //uzly bez konfigurací
                echo "<div class='right'>";
                $dotazUzly = $db->query("SELECT nazev FROM uzly WHERE id_konfigurace IS NULL ORDER BY nazev");
                echo "<table>";
                echo "<tr><th colspan='2'>Uzly bez konfigurace</tr>";
                if($dotazUzly->num_rows == 0){
                    echo "<tr><td colspan='2'>Všechny uzly už mají přidělenou konfiguraci.</td></tr>";
                }
                else{
                    while($uzly = $dotazUzly->fetch_assoc()) {
                        echo "<tr><td>{$uzly["nazev"]}</td><td><a href='konfigurace.php?s={$_GET["s"]}&amp;k={$_GET["k"]}&u=pu&amp;n={$uzly["nazev"]}'>přidat</a</td></tr>";
                    }
                }
                echo "</table>";
                echo "</div>";
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
