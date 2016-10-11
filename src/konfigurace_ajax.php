<?php
ini_set("default_charset", "UTF-8");
header("Content-Type: text/html; UTF-8");
?>
<?php

	include_once 'pripojeniDB.php';

	if(!empty($_GET["uzel"])){
		$dotazKonfigurace = $db->query("SELECT * FROM uzly JOIN konfigurace ON uzly.id_konfigurace=konfigurace.nazev WHERE uzly.nazev='{$_GET["uzel"]}'");

		echo "<h2>Konfigurace uzlu {$_GET["uzel"]}</h2>";
		if($dotazKonfigurace->num_rows == 0){
			echo "<p>U toho uzlu nebyla specifikována jeho konfigurace.</p>";
		}
		else {
			$konfigurace = $dotazKonfigurace->fetch_assoc();
			echo "<table>";
			echo "<tr><th>Základní deska</th><td>{$konfigurace["mb"]}</td></tr>";
			echo "<tr><th>Procesor</th><td>{$konfigurace["cpu"]}</td></tr>";
			echo "<tr><th>Operační paměť</th><td>{$konfigurace["ram"]}</td></tr>";
			echo "<tr><th>Pevný disk</th><td>{$konfigurace["hdd"]}</td></tr>";
			echo "<tr><th>Grafická karta</th><td>{$konfigurace["gpu"]}</td></tr>";
			echo "<tr><th>Síťová karta</th><td>{$konfigurace["net"]}</td></tr>";
			echo "<tr><th>Příkon uzlu</th><td>".(!is_null($konfigurace["prikon_uzlu"]) ? "{$konfigurace["prikon_uzlu"]} W" : "")."</td></tr>";
			echo "<tr><th>Příkon grafické karty</th><td>".(!is_null($konfigurace["prikon_gpu"]) ? "{$konfigurace["prikon_gpu"]} W" : "")."</td></tr>";
			echo "<tr><th>Další informace</th><td>{$konfigurace["info"]}</td></tr>";
			echo "</table>";
		}
	}

?>