<?php

    /** Vrací skupinu
     * @param $group název skupiny
     * @param $g udává jestli je ve skupině další skupina
     * @return string Vrací celou skupinu.
     */
    function get_group($group,$g){
        $command = shell_exec("qconf -shgrp $group");
        $command = explode("hostlist",$command);
        $command = $command[1];
        $command = explode("\\\n",$command);
        $lines = array();
        foreach($command as $line){
            if(!empty($line)){
                array_push($lines,$line);
            }
        }
        $nodes = array();
        $output = "";
        foreach($lines as $line){
            $line = explode(" ",$line);
            if(!empty($line)){
                foreach ($line as $node){
                    if (!empty($node)){
                        array_push($nodes, $node);
                    }
                }
            }
        }
        for($i = 0; $i < count($nodes); $i++) {
            if(preg_match_all("/^@.*$/",$nodes[$i],$matches)){
                $output .= get_group($nodes[$i],1);
            }
            else{
                $tmp = explode(".fit.vutbr.cz",$nodes[$i]);
                $node = $tmp[0];
                if($g or $i != count($nodes) - 1){
                    $output .= ",".$node;
                    $g = 0;
                }
                else{
                    $output .= ",".$node;
                }
            }
        }
        return $output;
    }

    $group_file = "groups";

    //získání skupin
    $command = shell_exec("qconf -shgrpl");
    $groups = explode("\n",$command);
    $output = "";
    foreach($groups as $group){
        if(!empty($group)) {
            $output .= $group.":";
            $g = get_group($group,0);
            $output .= trim($g,",");
            $output .= "\n";
        }
    }

    //přidání globálních prostředků
    $qstat = file_get_contents("qstat");
    $qstat = preg_split("/.*---*/",$qstat);
    $qstat = $qstat[1];

    $matches = array();
    $globals = "";
    $lines = explode("\n",$qstat);
    foreach($lines as $line){
        if(!empty($line)){
            if(preg_match_all("/.*gc:.*/",$line,$matches)){
                $resource = explode(":",$line);
                $resource = $resource[1];
                $resource = explode("=",$resource);
                $resource = $resource[0];
                $globals .= $resource.",";
            }
        }
    }

    $globals = rtrim($globals,",");

    $globals = "@globals:".$globals;

    $output .= $globals;

    file_put_contents($group_file, $output);
?>