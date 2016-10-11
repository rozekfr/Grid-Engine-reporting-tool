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
            <h1>Informace pro administrátora výpočetního clusteru</h1>
            <p class="info">V této sekci se administrátor dozví všechny potřebné informace o tom, jak udržet reportovací nástroj aktuální.</p>
            <h2>Skripty, které musí běžet neustále</h2>
            <ul class="info">
                <li><span class="terminal">update_rrd.sh</span> - je skript, který každou minutu aktualizuje obsah RRD databází</li>
                <li><span class="terminal">pending_jobs_updater.sh</span> - je skript, který každou minutu zjišťuje čekající úlohy v clusteru</li>
                <li><span class="terminal">get_job_info_checker.sh</span> - je skript, který každých 15 vteřin , jestli existují žádosti o informace o úloze, pokud ano žádosti vyřidí.</li>
                <li><span class="terminal">resource_list_checker.sh</span> - je skript, který každých 15 vteřin ověřuje, jestli existují žádosti na prostředky, které jsou blokovány, pokud ano žádosti vyřídí.</li>
            </ul>

            <h2>Vytvoření RRD databází</h2>
            <ul class="info">
                <li>RRD databáze se vytvoří z terminálu PHP skriptem <span class="terminal">create_rrd_databases.php</span></li>
                <li>Skript automaticky vytvoří databáze pro sloty, čekající úlohy, grafické karty, globální prostředky a pro ostatní uzly v clusteru - tyto uzly musí být nejprve zjištěny z terminálu skriptem <span class="terminal">get_groups.php</span>.</li>
            </ul>

            <h2>Uzly výpočetního clusteru</h2>
            <ul class="info">
                <li>K získání uzlů výpočetního clusteru spusťte z terminálu PHP skript <span class="terminal">get_groups.php</span>.</li>
                <li>Pro získání informací o GPU k jednotilivým uzlům spusťte z terminálu PHP skript <span class="terminal">get_gpu_info.php</span>.</li>
                <li>Import uzlů do databáze se provádí pomocí PHP skriptu <a href="../import_db_nodes.php" target="_blank">import_db_nodes.php</a>, který naimportuje pouze uzly, které v databázi nejsou.</li>
            </ul>

            <h2>Uživatelé výpočetního clusteru</h2>
            <ul class="info">
                <li>K získání uživatelů výpočetního clusteru a jejich výchozích skupin, je nutné nejprve získat soubor <b>/etc/passwd</b> a zkopírovat ho do kořenového adresáře projektu pod názvem <b>passwd</b> - (<span class="terminal">cat /etc/passwd > passwd</span>)</li>
                <li>K získání aktivních uživatelů SGE spusťte z terminálu PHP skript <span class="terminal">get_sge_users.php</span></li>
                <li>PHP skript <a href="../import_db_users.php" target="_blank">import_db_users.php</a> pak naimportuje do databáze uživatele z /etc/passwd, kteří v ní nejsou.</li>
            </ul>

            <h2>Aktualizace statistik</h2>
            <ul class="info">
                <li>Pro import statistik ze souboru <i>accounting</i> tento soubor zkopírujte do kořenového adresáře projektu.</li>
                <li>Spuštěním PHP skriptu <a href="../accounting_parser.php" target="_blank">accounting_parser.php</a> odtud nebo z terminálu se pak tento soubor zpracuje a data se přidají do databáze. To může trvat velice dlouho kvůli obrovskému množství dat, které tento soubor obsahuje.</li>
            </ul>

            <h2>Přidávání uživatelů do skupin</h2>
            <ul class="info">
                <li>Uživatele můžete přiřazovat do skupin v sekci <b>SKUPINY UŽIVATELŮ</b>.</li>
                <li>Zde si správce vytvoří skupiny a poté do nich přiřazuje uživatele.</li>
            </ul>

            <h2>Aktualizace spotřeby uzlů</h2>
            <ul class="info">
                <li>Spotřeba uzlů se konfiguruje v sekci <b>SPOTŘEBA UZLŮ</b>.</li>
                <li>V této sekci správce přiřazuje hodnoty spotřeby k jednotlivým konfiguracím. Tyto hodnoty jsou pak nastaveny všem uzlům, kteří mají tuto konfiguraci.</li>
            </ul>

            <h2>Aktualizace konfigurace uzlů</h2>
            <ul class="info">
                <li>Konfigurace uzlů se provádí v sekci <b>KONFIGURACE UZLŮ</b>.</li>
                <li>V této sekci správce může vytvářet nové konfigurace a přiřazovat je k uzlům.</li>
            </ul>

        </div> 
        <div id="podmenu">
<?php
  include 'podmenu.php';
?>            
        </div>
      </div>
    </body>
</html>
