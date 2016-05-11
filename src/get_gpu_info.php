<?php
/**
 * Zjistí informace o grafických kartách ve všech uzlech a uloží do souboru gpu_info.
 */

    //získání všech uzlů
    $groups = file_get_contents("groups");
    $groups = explode("\n",$groups);
    foreach($groups as $group){
        if(!empty($group)) {
            $g = explode(":", $group);
            if($g[0] == "@allhosts"){
                $nodes = $g[1];
                $nodes = explode(",",$nodes);
            }
        }
    }

    //získání informací o GPU
    $gpu_info = "";
    foreach($nodes as $node){
        $gpu_info .= $node.":";
        $command = shell_exec("qconf -se $node");
        $info = explode("\n",$command);
        foreach($info as $row){
            if(!empty($row)) {
                $tmp_row = array();
                $cols = explode(" ",$row);
                foreach($cols as $col){
                    if(!empty($col)) {
                        array_push($tmp_row,$col);
                    }
                }
                $zapsano = false;
                if($tmp_row[0] == "complex_values"){
                    $col2 = $tmp_row[1];
                    $values = explode(",",$col2);
                    foreach($values as $value){
                        if(!empty($value)){
                            $value = explode("=",$value);
                            if($value[0] == "gpu"){
                                $gpu_info .= $value[1];
                                $zapsano = true;
                            }
                        }
                    }
                    if(!$zapsano){
                        $gpu_info .= "0";
                    }
                }
            }
        }
        $gpu_info .= "\n";
    }

    file_put_contents("gpu_info",$gpu_info);
?>