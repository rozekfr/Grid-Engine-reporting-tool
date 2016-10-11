<?php
/**
 * Tento skript generuje obsah podmenu u prostředků.
 */
    if(!empty($_GET["s"])){
        $groups = file_get_contents("groups");
        $groups = explode("\n",$groups);
        $globals = array("servers" => "Dostupnost serverů","gpus" => "Dostupnost grafických karet");
        $nodes = array();
        $blocks = array();
        $classes = array();
        foreach($groups as $group){
            $matches = array();
            if(!empty($group)){
                $tmp = explode(":",$group);
                $group_name = $tmp[0];
                $group_name = trim($group_name,"@");
                if(preg_match_all("/^PC.+xxx$/",$group_name,$matches)){
                    array_push($blocks,$group_name);
                }
                else if(preg_match_all("/^PC/",$group_name, $matches)){
                    array_push($classes,$group_name);
                }
                else{
                    array_push($nodes,$group_name);
                }
            }
        }

        switch($_GET["s"]){
            case "globals":
                foreach($globals as $key => $g){
                    if(!empty($_GET["n"]) and $_GET["n"] == $key){
                        echo "<a href='index.php?m={$_GET["m"]}&amp;s={$_GET["s"]}&amp;n=$key' class='aktivni'>$g</a>";
                    }
                    else{
                        echo "<a href='index.php?m={$_GET["m"]}&amp;s={$_GET["s"]}&amp;n=$key'>$g</a>";
                    }
                }
                break;

            case 'nodes':
                foreach($nodes as $node){
                    if($node != "globals") {
	                    if (!empty($_GET["n"]) and $_GET["n"] == $node) {
		                    echo "<a href='index.php?m={$_GET["m"]}&amp;s={$_GET["s"]}&amp;n=$node' class='aktivni'>$node</a>";
	                    } else {
		                    echo "<a href='index.php?m={$_GET["m"]}&amp;s={$_GET["s"]}&amp;n=$node'>$node</a>";
	                    }
                    }
                }
                break;

            case 'blocks':
                foreach($blocks as $block){
                    if(!empty($_GET["n"]) and $_GET["n"] == $block){
                        echo "<a href='index.php?m={$_GET["m"]}&amp;s={$_GET["s"]}&amp;n=$block' class='aktivni'>$block</a>";
                    }
                    else{
                        echo "<a href='index.php?m={$_GET["m"]}&amp;s={$_GET["s"]}&amp;n=$block'>$block</a>";
                    }
                }
                break;
            
            case 'classes':
                foreach($classes as $class){
                    if(!empty($_GET["n"]) and $_GET["n"] == $class){
                        echo "<a href='index.php?m={$_GET["m"]}&amp;s={$_GET["s"]}&amp;n=$class' class='aktivni'>$class</a>";
                    }
                    else{
                        echo "<a href='index.php?m={$_GET["m"]}&amp;s={$_GET["s"]}&amp;n=$class'>$class</a>";
                    }
                }
                break;
        }
    }
?>