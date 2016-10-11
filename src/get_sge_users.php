<?php
/**
 * Získá aktivní uživatele clusteru a uloží je do souboru users.
 */
	$command = shell_exec("qconf -su allusers");
	file_put_contents("users",$command);
?>