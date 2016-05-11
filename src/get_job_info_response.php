<?php
    //kódování a české znaky
    ini_set("default_charset", "UTF-8");
    header("Content-Type: text/html; UTF-8");
?>
<?php
    if(!empty($_GET["id"])){
        if(file_exists("PJ_response_{$_GET["id"]}.txt")){
            $output = file_get_contents("PJ_response_{$_GET["id"]}.txt");
            if(!empty($output)){
                $output = explode("\n",$output);
                echo "<table>";
                $i = 0;
                foreach($output as $o){
                    if(!empty($o) and $i != 0){
                        $tmp = explode(":",$o);
                        if(isset($tmp[1])){
                            echo "<tr><th>{$tmp[0]}</th><td>{$tmp[1]}</td></tr>";
                        }
                        else{
                            echo "<tr><th></th><td>{$tmp[0]}</td></tr>";
                        }
                    }
                    $i++;
                }
                echo "</table>";
            }
            else{
                echo "<p>Omlouváme se, ale o této úloze nejsou žádné informace.</p>";
            }
        }
    }
?>