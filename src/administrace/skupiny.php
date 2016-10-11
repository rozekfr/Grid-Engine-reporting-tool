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

include_once 'functions.php';

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
                //přidání skupiny
                if(!empty($_GET["u"]) and $_GET["u"] == "vs" and isset($_POST["nazev"])){
                    if(!empty($_POST["nazev"])){
                        $dotazNazev = $db->query("SELECT nazev FROM skupiny WHERE nazev='{$_POST["nazev"]}'");
                        $nazev = $dotazNazev->fetch_assoc();
                        if(empty($nazev["nazev"])) {
                            $ret = $db->query("INSERT INTO skupiny (nazev,info) VALUE ('{$_POST["nazev"]}','{$_POST["info"]}')");
                            if (!$ret) {
                                print_error($db, "Nepodařilo se vytvořit skupinu!");
                            } else {
                                header("Location: skupiny.php?s={$_GET["s"]}");
                            }
                        }
                        else{
                            print_error($db,"Tato skupina už existuje, zvolte jiný název.");
                        }
                    }
                    else{
                        print_error($db,"Název skupiny musí být vyplněn!");
                    }
                }
                //formulář pro přidání skupiny
                if(!empty($_GET["u"]) and $_GET["u"] == "vs" and !isset($_POST["nazev"])){
                    echo "<form action='skupiny.php?s={$_GET["s"]}&amp;u=vs' method='POST'>";
                        echo "<h1>Vytvoření nové skupiny</h1>";
                        echo "<table>";
                            echo "<tr><th>Název skupiny:</th><td><input type='text' name='nazev'></td></tr>";
                            echo "<tr><th>Popis skupiny:</th><td><textarea name='info'></textarea></td></tr>";
                            echo "<tr><td></td><td><input type='submit' value='Vytvořit skupinu'></td></tr>";
                        echo "</table>";
                    echo "</form>";
                }

                //úprava skupiny
                if(!empty($_GET["u"]) and $_GET["u"] == "us" and isset($_POST["nazev"])){
                    if(!empty($_POST["nazev"])){
                        $ret = $db->query("UPDATE skupiny SET info='{$_POST["info"]}' WHERE nazev='{$_GET["skupina"]}'");
                        if (!$ret) {
                            print_error($db, "Nepodařilo se vytvořit skupinu!");
                        }
                        else{
                            header("Location: skupiny.php?s={$_GET["s"]}&skupina={$_GET["skupina"]}");
                        }
                    }
                    else{
                        print_error($db,"Název skupiny musí být vyplněn!");
                    }
                }
                //formulář pro úpravu skupiny
                if(!empty($_GET["u"]) and $_GET["u"] == "us" and !isset($_POST["nazev"])){
                    $skupina = $db->query("SELECT nazev,info FROM skupiny WHERE nazev='{$_GET["skupina"]}'")->fetch_assoc();
                    echo "<form action='skupiny.php?s={$_GET["s"]}&amp;skupina={$_GET["skupina"]}&amp;u=us' method='POST'>";
                    echo "<h1>Úprava skupiny</h1>";
                    echo "<table>";
                    echo "<tr><th>Název skupiny</th><td><input type='text' name='nazev' value='{$skupina["nazev"]}' readonly></td></tr>";
                    echo "<tr><th>Popis skupiny</th><td><textarea name='info'>{$skupina["info"]}</textarea></td></tr>";
                    echo "<tr><td></td><td><input type='submit' value='Vytvořit skupinu'></td></tr>";
                    echo "</table>";
                    echo "</form>";
                }

                //smazání skupiny
                if(!empty($_GET["u"]) and $_GET["u"] == "ss"){
                    $ret = $db->query("DELETE FROM rozpis_uzivatele_skupiny WHERE id_skupiny='{$_GET["skupina"]}'");
                    if(!$ret){
                        print_error($db,"Nepovedlo se odstranit propojení mezi skupinou a uživateli!");
                    }
                    else{
                        $ret = $db->query("DELETE FROM skupiny WHERE nazev='{$_GET["skupina"]}'");
                        if(!$ret){
                            print_error($db, "Nepodařilo se odstranit skupinu!");
                        }
                        else{
                            header("Location: skupiny.php?s={$_GET["s"]}");
                        }
                    }
                }

                //přidání uživatele do skupiny
                if(!empty($_GET["u"]) and $_GET["u"] == "pu"){
                    $dotazId = $db->query("SELECT MAX(id) as id FROM rozpis_uzivatele_skupiny");
                    $id = $dotazId->fetch_assoc();
                    if(empty($id["id"])) $id["id"] = 1;
                    else $id["id"]++;
                    $ret = $db->query("INSERT INTO rozpis_uzivatele_skupiny (id, uzivatel, id_skupiny) VALUE ('{$id["id"]}','{$_GET["uzivatel"]}','{$_GET["skupina"]}')");
                    if(!$ret){
                        print_error($db,"Uživatele se nepodařilo zařadit do skupiny!");
                    }
                    else{
                        header("Location: skupiny.php?s={$_GET["s"]}&skupina={$_GET["skupina"]}");
                    }
                }

                //odebrání uživatele ze skupiny
                if(!empty($_GET["u"]) and $_GET["u"] == "ou"){
                    $ret = $db->query("DELETE FROM rozpis_uzivatele_skupiny WHERE id='{$_GET["id"]}'");
                    if(!$ret){
                        print_error($db,"Uživatele se nepodařilo odebraz ze skupiny!");
                    }
                    else{
                        header("Location: skupiny.php?s={$_GET["s"]}&skupina={$_GET["skupina"]}");
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
                <h1>Skupiny uživatelů</h1>
                <p class="info">V této sekci může administrátor spravovat skupiny uživatelů a přiřazovat do nich uživatele.</p>

                <?php
                    //seznam skupin
                    if(empty($_GET["skupina"])){
                        echo "<table>";
                        echo "<tr><th>Skupina</th><th>Informace</th></tr>";
                        $dotazSkupiny = $db->query("SELECT * FROM skupiny");
                        if($dotazSkupiny->num_rows == 0){
                            echo "<tr><td colspan='2'>Nemáte definovány žádné skupiny.</td></tr>";
                        }
                        else{
                            while ($skupina = $dotazSkupiny->fetch_assoc()) {
                                echo "<tr><td>{$skupina["nazev"]}</td><td><a href='skupiny.php?s={$_GET["s"]}&amp;skupina={$skupina["nazev"]}'>detail</a></td></tr>";
                            }
                        }
                        echo "</table>";
                    }
                    //konkrétní skupina s uživateli
                    else{
                        $dotazSkupina = $db->query("SELECT nazev,info FROM skupiny WHERE nazev='{$_GET["skupina"]}'");
                        $skupina = $dotazSkupina->fetch_assoc();
                        echo "<table>";
                            echo "<tr><th>Název skupiny:</th><td>{$skupina["nazev"]}</td></tr>";
                            echo "<tr><th>Popis skupiny:</th><td>{$skupina["info"]}</td></tr>";
                        echo "</table>";

                        //seznam uživatelů ve skupině
                        echo "<div class='left'>";
                            echo "<table>";
                                echo "<tr><th colspan='2'>Seznam uživatelů v této skupině</th></tr>";
                                $dotazUzivateleVeSkupine = $db->query("SELECT id,uzivatel FROM rozpis_uzivatele_skupiny WHERE id_skupiny='{$_GET["skupina"]}'");
                                if($dotazUzivateleVeSkupine->num_rows == 0){
                                    echo "<tr><td colspan='2'>V této skupině nejsou žádní uživatelé.</td></tr>";
                                }
                                else {
                                    while ($uzivatel = $dotazUzivateleVeSkupine->fetch_assoc()) {
                                        echo "<tr><td>{$uzivatel["uzivatel"]}</td><td><a href='skupiny.php?s={$_GET["s"]}&amp;skupina={$_GET["skupina"]}&amp;id={$uzivatel["id"]}&amp;u=ou'>odebrat</a></td></tr>";
                                    }
                                }
                            echo "</table>";
                        echo "</div>";

                        //seznam uživatelů, kteří v této skupině nejsou
                        echo "<div class='right'>";
                        echo "<table>";
                        echo "<tr><th colspan='2'>Seznam uživatelů</th></tr>";
                        $dotazUzivatele = $db->query("SELECT uzivatel FROM uzivatele WHERE uzivatel NOT IN (SELECT uzivatel FROM rozpis_uzivatele_skupiny WHERE id_skupiny='{$_GET["skupina"]}') AND sge=1");
                        if($dotazUzivatele->num_rows == 0){
                            echo "<tr><td colspan='2'>Nejsou k dispozici žádní uživatelé.</td></tr>";
                        }
                        else {
                            while ($uzivatel = $dotazUzivatele->fetch_assoc()) {
                                echo "<tr><td>{$uzivatel["uzivatel"]}</td><td><a href='skupiny.php?s={$_GET["s"]}&amp;skupina={$_GET["skupina"]}&amp;uzivatel={$uzivatel["uzivatel"]}&amp;u=pu'>přidat</a></td></tr>";
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
