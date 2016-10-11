<?php
//nastavení češtiny
ini_set("default_charset", "UTF-8");
header("Content-Type: text/html; UTF-8");
?>
<?php
session_start();
include '../pripojeniDB.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" href="css/styly.css" type="text/css">
        <script src="js/script.js" type="text/javascript"></script>
        <meta name="robots" content="noindex,nofollow">
        <title>Sun Grid Engine - Reporting tool | Administrace</title>
    </head>
    <body onload="login();">
      <div id="login">
<?php
if(!empty($_POST["login"]) and !empty($_POST["heslo"])){
  $dotazUzivatel = $db->query("SELECT id,login,heslo FROM admini WHERE login='{$_POST["login"]}'");
  $uzivatel = $dotazUzivatel->fetch_assoc();
  if($uzivatel["heslo"] == sha1(md5($_POST["heslo"]))){
    $_SESSION["login"] = $uzivatel["login"];
    $db->query("UPDATE admins SET posledni_prihlaseni=now() WHERE login='{$_POST["login"]}'");
    header("Location: hlavni.php?s=hlavni");
  }
  else echo "<script>alert('Špatně zadané přihlašovací údaje!');</script>";
}
echo "<h1>Přihlášení</h1>";
echo "<form action='index.php' method='POST'>";
  echo "<table>";
    echo "<tr><td>Login:</td><td><input type='text' name='login' value='".(!empty($_POST["login"]) ? "{$_POST["login"]}" : "")."'></td></tr>";
    echo "<tr><td>Heslo:</td><td><input type='password' name='heslo'></td></tr>";
    echo "<tr><td><a href='../index.php'>zpět k nástroji</a></td><td><input class='submit' type='submit' value='Přihlásit se'></td></tr>";
  echo "</table>";
echo "</form>";
?>          
      </div>
    </body>
</html>