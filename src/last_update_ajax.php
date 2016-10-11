<?php

	include_once 'pripojeniDB.php';

	$dotazLastUpdate = $db->query("SELECT update_time FROM information_schema.tables WHERE TABLE_NAME = '{$_GET["db"]}'");
	$lastUpdate = $dotazLastUpdate->fetch_assoc();

	echo "{$lastUpdate["update_time"]}";


?>