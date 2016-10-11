<?php
//nastavení češtiny
ini_set("default_charset", "UTF-8");
header("Content-Type: text/html; UTF-8");
?>
<?php
session_start();
ob_start();
include '../pripojeniDB.php';
//přihlášení
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
//formulář pro přidání admina
if(!empty($_GET["u"]) and $_GET["u"] == "pa"){
  echo "<h1>Přidání administrátora</h1>";
  echo "<form action='admini.php?s=admini&amp;u=pa' method='POST'>";
    echo "<table>";
      echo "<tr><td>Login:</td><td><input type='text' name='login' value='".(isset($_POST["login"]) ? "{$_POST["login"]}" : "")."'></td></tr>";
      echo "<tr><td>Heslo:</td><td><input type='password' name='heslo'></td></tr>";
      echo "<tr><td>Heslo znovu:</td><td><input type='password' name='heslo_znovu'></td></tr>";
      echo "<tr><td></td><td><input type='submit' value='přidat administrátora'></td></tr>";
    echo "</table>";
  echo "</form>";
}
//přidání admina
if(!empty($_GET["u"]) and $_GET["u"] == "pa" and !empty($_POST["login"])){
  if(!empty($_POST["login"]) and !empty($_POST["heslo"]) and !empty($_POST["heslo_znovu"]) and $_POST["heslo"] == $_POST["heslo_znovu"]){
    $dotazId = $db->query("SELECT MAX(id) AS id FROM admins");
    $id = $dotazId->fetch_assoc();
    if(empty($id["id"])) $id["id"] = 1;
    else $id["id"]++;
    $dotazLogin = $db->query("SELECT login FROM admins WHERE login='{$_POST["login"]}'");
    $login = $dotazLogin->fetch_assoc();
    if(empty($login["login"])){
      $heslo = sha1(md5($_POST["heslo"]));
      $db->query("INSERT INTO `admins` (`id`, `login`, `heslo`) VALUES ('{$id["id"]}', '{$_POST["login"]}','{$heslo}')");
      header("Location: admini.php?s=admini");
    }
    else{
      print_error($db,"Tento login je už používán, zvolte jiný!");
    }
  }
  else if(empty($_POST["login"]) or empty($_POST["heslo"]) or empty($_POST["heslo_znovu"])){
    print_error($db,"Nevyplnili jste všechny položky!");
  }
  else if($_POST["heslo"] != $_POST["heslo_znovu"]){
    print_error($db,"Hesla se neshodují!");
  }
  else{
    print_error($db,"Nepodařilo se přidat nového administrátora!");
  }
}
//formulář pro úpravu administrátora
if(!empty($_GET["u"]) and $_GET["u"] == "ua" and !empty($_GET["a"])){
  $dotazAdmin = $db->query("SELECT login FROM admins WHERE id='{$_GET["a"]}'");
  $admin = $dotazAdmin->fetch_assoc();
  echo "<h1>Úprava administrátora</h1>";
  echo "<form action='admini.php?s=admini&amp;a={$_GET["a"]}&amp;u=ua' method='POST'>";
    echo "<table>";
      echo "<tr><td>Login:</td><td><input type='text' name='login' value='{$admin["login"]}'></td></tr>";
      $dotazAktualniAdmin = $db->query("SELECT superadmin FROM admins WHERE login='{$_SESSION["login"]}'");
      $aktualniAdmin = $dotazAktualniAdmin->fetch_assoc();
      if($aktualniAdmin["superadmin"]) {
        echo "<tr><td>Superadmin:</td><td><input type='text' name='superadmin' value='{$admin["login"]}'></td></tr>";
      }
      echo "<tr><td></td><td><input type='submit' value='upravit administrátora'></td></tr>";
    echo "</table>";
  echo "</form>";
}
//úprava administrátora
if(!empty($_GET["u"]) and $_GET["u"] == "ua" and !empty($_GET["a"]) and !empty($_POST["login"])){
  $dotazLogin = $db->query("SELECT login FROM admini WHERE login='{$_POST["login"]}' and id<>'{$_GET["a"]}'");
  $login = $dotazLogin->fetch_assoc();
  if(empty($login["login"])){
    $db->query("UPDATE admini SET login='{$_POST["login"]}' WHERE id='{$_GET["a"]}'");
    header("Location: admini.php?s=admini&a={$_GET["a"]}");
  }
  else if(!empty($login["login"])){
    print_error($db,"Tento login už je používán, zvolte jiný!");
  }
}
//formulář pro změnu hesla administrátora
if(!empty($_GET["u"]) and $_GET["u"] == "zh" and !empty($_GET["a"])){
  echo "<h1>Změna hesla administrátora</h1>";
  echo "<form action='admini.php?s=admini&amp;a={$_GET["a"]}&amp;u=zh' method='POST'>";
    echo "<table>";
      echo "<tr><td>Staré heslo:</td><td><input type='password' name='old_heslo'></td></tr>";
      echo "<tr><td>Nové heslo:</td><td><input type='password' name='new_heslo'></td></tr>";
      echo "<tr><td>Nové heslo znovu:</td><td><input type='password' name='new_heslo_znovu'></td></tr>";
      echo "<tr><td></td><td><input type='submit' value='změnit heslo'></td></tr>";
    echo "</table>";
  echo "</form>";
}
//změna hesla
if(!empty($_GET["u"]) and $_GET["u"] == "zh" and !empty($_GET["a"]) and isset($_POST["old_heslo"])){
  if(!empty($_POST["old_heslo"]) and !empty($_POST["new_heslo"]) and !empty($_POST["new_heslo_znovu"])){
    $dotazAdmin = $db->query("SELECT heslo FROM admini WHERE id='{$_GET["a"]}'");
    $admin = $dotazAdmin->fetch_assoc();
    if(sha1(md5($_POST["old_heslo"])) == $admin["heslo"] and $_POST["new_heslo"] == $_POST["new_heslo_znovu"]){
      $heslo = sha1(md5($_POST["new_heslo"]));
      $db->query("UPDATE admini SET heslo='{$heslo}' WHERE id='{$_GET["a"]}'");
      header("Location: admini.php?s=admini&a={$_GET["a"]}");
    }
    else if(sha1(md5($_POST["old_heslo"])) != $admin["heslo"]){
      print_error($db,"Špatné staré heslo!");
    }
    else if($_POST["new_heslo"] != $_POST["new_heslo_znovu"]){
      print_error($db,"Nová hesla se neshodují, heslo nebylo změněno!");
    }
  }
  else{
    print_error($db,"Nevyplnili jste všechny položky, heslo nebylo změněno!");
  }
}
//smazání administrátora
if(!empty($_GET["u"]) and $_GET["u"] == "sa"){
  $dotazAktualniAdmin = $db->query("SELECT id FROM admini WHERE login='{$_SESSION["login"]}'");
  $aktualniAdmin = $dotazAktualniAdmin->fetch_assoc();
  if($aktualniAdmin["id"] == $_GET["a"]){
    print_error($db,"Nelze odstranit aktuálního administrátora!");
  }
  else {
    $db->query("DELETE FROM admini WHERE id='{$_GET["a"]}'");
    header("Location: admini.php?s=admini");
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
            <h1>Administrátoři</h1>
            <p class="info">V této sekci může administrátor měnit své údaje. Superadministrátor může přidávat nové administrátory a spravovat je.</p>
<?php
  $dotazAdmini = $db->query("SELECT id,login,posledni_prihlaseni FROM admini");
  if(empty($_GET["a"])){
    echo "<table class='sestava'>";
    echo "<tr><th>Login</th><th>Poslední přihlašení</th></tr>";
    while($admini = $dotazAdmini->fetch_assoc()){
      echo "<tr><td><a href='admini.php?s=admini&amp;a={$admini["id"]}'>{$admini["login"]}</a></td><td>{$admini["posledni_prihlaseni"]}</td></tr>";
    }
    echo "</table>";
  }
  else{
    $dotazAdmin = $db->query("SELECT login,posledni_prihlaseni,superadmin FROM admini WHERE id='{$_GET["a"]}'");
    $admin = $dotazAdmin->fetch_assoc();
    echo "<table>";
    echo "<tr><th>Login:</th><td>{$admin["login"]}</td></tr>";
    echo "<tr><th>Heslo:</th><td>skryté</td></tr>";
    echo "<tr><th>Superadmin:</th><td>".($admin["superadmin"] ? "ano" : " ne")."</td></tr>";
    echo "<tr><th>Poslední přihlášení:</th><td>{$admin["posledni_prihlaseni"]}</td></tr>";
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
