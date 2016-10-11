<?php
//nastavení češtiny
ini_set("default_charset", "UTF-8");
header("Content-Type: text/html; UTF-8");
?>
<?php
/**
 * Tento skript naimportuje všechny uzly do MySQL databáze, pokud tam nejsou.
 */

    include 'pripojeniDB.php';

    //získání všech uzlů a globálních prostředků
    $groups = file_get_contents("groups");
    $groups = explode("\n",$groups);
    $nodes = array();
    foreach($groups as $group){
        if(!empty($group)) {
            $g = explode(":", $group);
            if($g[0] == "@allhosts"){
                $group_nodes = $g[1];
                $group_nodes = explode(",",$group_nodes);
                $nodes = array_merge($nodes,$group_nodes);
            }
            if($g[0] == "@globals"){
                $group_nodes = $g[1];
                $group_nodes = explode(",",$group_nodes);
                $nodes = array_merge($nodes,$group_nodes);
            }
        }
    }

    foreach($nodes as $node){
        //přídá pouze pokud tam nejsou
        $ret = $db->query("INSERT INTO uzly (nazev) SELECT * FROM (SELECT '$node') AS tmp WHERE NOT EXISTS (SELECT nazev FROM uzly WHERE nazev = '$node') LIMIT 1;");
	    if(!$ret){
		    echo "<p>Chyba při importu!</p>";
		    $db->error;
		    exit();
	    }
    }

	echo "<p>Úspěšně naimportováno.</p>";
?>