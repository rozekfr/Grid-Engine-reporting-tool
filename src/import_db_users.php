<?php
//nastavení češtiny
ini_set("default_charset", "UTF-8");
header("Content-Type: text/html; UTF-8");
?>
<?php

	include_once 'pripojeniDB.php';

	$sge_active = file_get_contents("users");
	$sge_active = explode("entries",$sge_active);
	$sge_active = explode(",",$sge_active[1]);
	$active_users = array();
	foreach($sge_active as $sa){
		if(!empty($sa)){
			$sa = trim($sa," \t\n\r\0\x0B\\");
			array_push($active_users,$sa);
		}
	}

	$etcpasswd = file_get_contents("passwd");
	$etcpasswd = explode("\n",$etcpasswd);

	$users = array();
	foreach($etcpasswd as $user){
		if(!empty($user)) {
			$columns = explode(":", $user);
			//pouze uživatele - mají domovský adresář
			if(preg_match_all("/.*homes.*/",$columns[5],$matches)){
				$login = $columns[0];
				$name = explode(",", $columns[4]);
				$skupina = (isset($name[1]) ? $name[1] : "");
				$name = (isset($name[0]) ? $name[0] : "");
				$user = array("login" => $login, "jmeno" => $name, "skupina" => $skupina);
				array_push($users, $user);
			}
		}
	}

	foreach($users as $user){
		//přídá pouze pokud tam nejsou
		if(array_search($user["login"],$active_users)){
			$sge = 1;
			$active = 1;
		}
		else{
			$sge = 0;
			$active = 0;
		}
		$ret = $db->query("INSERT INTO uzivatele (uzivatel, jmeno, vychozi_skupina, sge, aktivita) SELECT '{$user["login"]}','{$user["jmeno"]}','{$user["skupina"]}','$sge','$active' FROM DUAL WHERE NOT EXISTS (SELECT uzivatel FROM uzivatele WHERE uzivatel='{$user["login"]}');");
		if(!$ret){
			echo $db->error;
			echo "<p>Chyba při importu!</p>";
			exit();
		}
	}

	echo "<p>Úspěšně naimportováno.</p>";
?>

