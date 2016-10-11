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
<div id="black"></div>
<div id="upravy" onmouseover="zobrazUpravu()">
    <a class="zavri" onclick="zavriUpravu();">X</a>
    <?php
        //změna aktivity na aktivního
        if(!empty($_GET["u"]) and $_GET["u"] == "ua"){
            $ret = $db->query("UPDATE uzivatele SET aktivita=1 WHERE uzivatel='{$_GET["uzivatel"]}'");
            if(!$ret){
                print_error($db,"Nepodařilo se udělat uživatele {$_GET["uzivatel"]} aktivním!");
            }
            else{
                if(empty($_GET["detail"])) {
                    header("Location: uzivatele.php?s={$_GET["s"]}");
                }
                else{
                    header("Location: uzivatele.php?s={$_GET["s"]}&uzivatel={$_GET["uzivatel"]}&detail=true");
                }
            }
        }

        //změna aktivity na neaktivního
        if(!empty($_GET["u"]) and $_GET["u"] == "oa"){
            $ret = $db->query("UPDATE uzivatele SET aktivita=0 WHERE uzivatel='{$_GET["uzivatel"]}'");
            if(!$ret){
                print_error($db,"Nepodařilo se udělat uživatele {$_GET["uzivatel"]} neaktivním!");
            }
            else{
                if(empty($_GET["detail"])) {
                    header("Location: uzivatele.php?s={$_GET["s"]}");
                }
                else{
                    header("Location: uzivatele.php?s={$_GET["s"]}&uzivatel={$_GET["uzivatel"]}&detail=true");
                }
            }
        }

        //odstranění ze skupiny
        if(!empty($_GET["u"]) and $_GET["u"] == "zs"){
            $ret = $db->query("DELETE FROM rozpis_uzivatele_skupiny WHERE id='{$_GET["id"]}'");
            if(!$ret){
                print_error($db,"Nepodařilo se zrušit skupinu!");
            }
            else{
                header("Location: uzivatele.php?s={$_GET["s"]}&uzivatel={$_GET["uzivatel"]}&detail=true");
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
        <h1>Seznam uživatelů</h1>
        <p class="info">V této sekci je administrátorovi poskytnut pohled na seznam uživatelů. Může zde také určovat, zdali jsou uživatelé aktivní.</p>

        <?php
            //seznam uživatelů
            if(empty($_GET["uzivatel"])){
                echo "<div class='left'>";
                    echo "<table>";
                    echo "<tr><th colspan='3'>Seznam aktivních uživatelů</th></tr>";
                    echo "<tr><th>Login</th><th>Aktivita</th><th>Informace</th></tr>";
                    $dotazAktivni = $db->query("SELECT uzivatel FROM uzivatele WHERE sge=1 AND aktivita=1 ORDER BY uzivatel");
                    if($dotazAktivni->num_rows == 0){
                        echo "<tr><td colspan='3'>Žádní uživatelé nejsou aktivní.</td></tr>";
                    }
                    else {
                        while ($aktivni = $dotazAktivni->fetch_assoc()) {
                            echo "<tr><td>{$aktivni["uzivatel"]}</td><td><a href='uzivatele.php?s={$_GET["s"]}&amp;uzivatel={$aktivni["uzivatel"]}&amp;u=oa'>změnit</a></td><td><a href='uzivatele.php?s={$_GET["s"]}&amp;uzivatel={$aktivni["uzivatel"]}&amp;detail=true'>detail</a></td></tr>";
                        }
                    }
                    echo "</table>";
                echo "</div>";

                echo "<div class='right'>";
                echo "<table>";
                echo "<tr><th colspan='3'>Seznam neaktivních uživatelů</th></tr>";
                echo "<tr><th>Login</th><th>Aktivita</th><th>Informace</th></tr>";
                $dotazNeaktivni = $db->query("SELECT uzivatel,jmeno FROM uzivatele WHERE sge=1 AND aktivita=0 ORDER BY uzivatel");
                if($dotazNeaktivni->num_rows == 0){
                    echo "<tr><td colspan='3'>Žádní uživatelé nejsou neaktivní.</td></tr>";
                }
                else {
                    while ($neaktivni = $dotazNeaktivni->fetch_assoc()) {
                        echo "<tr><td>{$neaktivni["uzivatel"]}</td><td><a href='uzivatele.php?s={$_GET["s"]}&amp;uzivatel={$neaktivni["uzivatel"]}&amp;u=ua'>změnit</a></td><td><a href='uzivatele.php?s={$_GET["s"]}&amp;uzivatel={$neaktivni["uzivatel"]}&amp;detail=true'>detail</a></td></tr>";
                    }
                }
                echo "</table>";
                echo "</div>";
            }
            //konkrétní uživatel
            else{
                $dotazUzivatel = $db->query("SELECT * FROM uzivatele WHERE uzivatel='{$_GET["uzivatel"]}'");
                $uzivatel = $dotazUzivatel->fetch_assoc();
                echo "<table>";
                    echo "<tr><th>Uživatel:</th><td>{$uzivatel["uzivatel"]}</td></tr>";
                    echo "<tr><th>Jméno:</th><td>{$uzivatel["jmeno"]}</td></tr>";
                    echo "<tr><th>Výchozí skupina:</th><td>{$uzivatel["vychozi_skupina"]}</td></tr>";
                    echo "<tr><th>Aktivita:</th><td>".($uzivatel["aktivita"] == 1 ? "<a href='uzivatele.php?s={$_GET["s"]}&amp;uzivatel={$_GET["uzivatel"]}&amp;detail=true&amp;u=oa'>aktivní</a>" : "<a href='uzivatele.php?s={$_GET["s"]}&amp;uzivatel={$_GET["uzivatel"]}&amp;detail=true&amp;u=ua'>neaktivní</a>")."</td></tr>";
                echo "</table>";

                $dotazSkupiny = $db->query("SELECT id,id_skupiny FROM rozpis_uzivatele_skupiny WHERE uzivatel='{$_GET["uzivatel"]}'");
                echo "<div>";
                echo "<table>";
                    echo "<tr><th colspan='2'>Patří do skupiny</th></tr>";
                    if($dotazSkupiny->num_rows == 0){
                        echo "<tr><td colspan='2'>Tento uživatel nepatří do žádné skupiny.</td></tr>";
                    }
                    else{
                        while($skupina = $dotazSkupiny->fetch_assoc()){
                            echo "<tr><td>{$skupina["id_skupiny"]}</td><td><a href='uzivatele.php?s={$_GET["s"]}&amp;uzivatel={$_GET["uzivatel"]}&amp;detail=true&amp;id={$skupina["id"]}&amp;u=zs'>zrušit</a></td></tr>";
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
