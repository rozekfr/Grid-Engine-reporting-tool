<?php
/**
 * Tento skript generuje nastavení pro stránku prostředky.
 */
    if(!empty($_GET["s"]) and !empty($_GET["n"])){
        echo "<div id='settings_content'>";
        //globální prostředky
        if($_GET["s"] == "globals"){
            //Vytíženost serverů
            if($_GET["n"] == "servers"){
                //období
                echo "<fieldset>";
                echo "<legend>Volba období</legend>";
                    echo "<label for='select_obdobi'>Zvolte období:</label>";
                    echo "<select id='select_obdobi' onchange='matyldy(); scratche()'>";
                        echo "<option value='0' selected>5 dní (rozlišení 1min)</option>";
                        echo "<option value='1'>3 týdny (rozlišení 5min)</option>";
                        echo "<option value='2'>2 měsíce (rozlišení 30min)</option>";
                        echo "<option value='3'>10 let (rozlišení 2h)</option>";
                    echo "</select>";
                echo "</fieldset>";

                echo "<div class='vlevo'>";
                    echo "<fieldset>";
                        echo "<legend>Matyldy</legend>";
                        echo "<table>";
                            echo "<tr><th>Průměrné hodnoty</th><th>Maximální hodnoty</th></tr>";
                            echo "<tr><td><input type='checkbox' id='matyldy_AVG' checked onchange='global_resource_selector(this)'><label for='matyldy_AVG'><i>vybrat vše</i></td><td><input type='checkbox' id='matyldy_MAX' checked onchange='global_resource_selector(this)'><label for='matyldy_MAX'><i>vybrat vše</i></td></tr>";
                            echo "<tr><td><input type='checkbox' class='matyldy_AVG' id='matylda1' checked><label for='matylda1'>matylda1</label></td><td><input type='checkbox' class='matyldy_MAX' id='matylda1_MAX' checked><label for='matylda1_MAX'>matylda1_MAX</label></td></tr>";
                            echo "<tr><td><input type='checkbox' class='matyldy_AVG' id='matylda2'><label for='matylda2'>matylda2</label></td><td><input type='checkbox' class='matyldy_MAX' id='matylda2_MAX'><label for='matylda2_MAX'>matylda2_MAX</label></td></tr>";
                            echo "<tr><td><input type='checkbox' class='matyldy_AVG' id='matylda3'><label for='matylda3'>matylda3</label></td><td><input type='checkbox' class='matyldy_MAX' id='matylda3_MAX'><label for='matylda3_MAX'>matylda3_MAX</label></td></tr>";
                            echo "<tr><td><input type='checkbox' class='matyldy_AVG' id='matylda4'><label for='matylda4'>matylda4</label></td><td><input type='checkbox' class='matyldy_MAX' id='matylda4_MAX'><label for='matylda4_MAX'>matylda4_MAX</label></td></tr>";
                            echo "<tr><td><input type='checkbox' class='matyldy_AVG' id='matylda5'><label for='matylda5'>matylda5</label></td><td><input type='checkbox' class='matyldy_MAX' id='matylda5_MAX'><label for='matylda5_MAX'>matylda5_MAX</label></td></tr>";
                            echo "<tr><td><input type='checkbox' class='matyldy_AVG' id='matylda6'><label for='matylda6'>matylda6</label></td><td><input type='checkbox' class='matyldy_MAX' id='matylda6_MAX'><label for='matylda6_MAX'>matylda6_MAX</label></td></tr>";
                        echo "</table>";
                    echo "</fieldset>";
                echo "</div>";
                echo "<div class='vpravo'>";
                    echo "<fieldset>";
                    echo "<legend>Scratche</legend>";
                        echo "<table>";
                            echo "<tr><th>Průměrné hodnoty</th><th>Maximální hodnoty</th></tr>";
                            echo "<tr><td><input type='checkbox' id='scratche_AVG' checked onchange='global_resource_selector(this)'><label for='scratche_AVG'><i>vybrat vše</i></td><td><input type='checkbox' id='scratche_MAX' checked onchange='global_resource_selector(this)'><label for='scratche_MAX'><i>vybrat vše</i></td></tr>";
                            echo "<tr><td><input type='checkbox' class='scratche_AVG' id='scratch1' checked><label for='scratch1'>scratch1</label></td><td><input type='checkbox' class='scratche_MAX' id='scratch1_MAX' checked><label for='scratch1_MAX'>scratch1_MAX</label></td></tr>";
                            echo "<tr><td><input type='checkbox' class='scratche_AVG' id='scratch2'><label for='scratch2'>scratch2</label></td><td><input type='checkbox' class='scratche_MAX' id='scratch2_MAX'><label for='scratch2_MAX'>scratch2_MAX</label></td></tr>";
                            echo "<tr><td><input type='checkbox' class='scratche_AVG' id='scratch3'><label for='scratch3'>scratch3</label></td><td><input type='checkbox' class='scratche_MAX' id='scratch3_MAX'><label for='scratch3_MAX'>scratch3_MAX</label></td></tr>";
                            echo "<tr><td><input type='checkbox' class='scratche_AVG' id='scratch4'><label for='scratch4'>scratch4</label></td><td><input type='checkbox' class='scratche_MAX' id='scratch4_MAX'><label for='scratch4_MAX'>scratch4_MAX</label></td></tr>";
                            echo "<tr><td><input type='checkbox' class='scratche_AVG' id='scratch5'><label for='scratch5'>scratch5</label></td><td><input type='checkbox' class='scratche_MAX' id='scratch5_MAX'><label for='scratch5_MAX'>scratch5_MAX</label></td></tr>";
                            echo "<tr><td><input type='checkbox' class='scratche_AVG' id='scratch6'><label for='scratch6'>scratch6</label></td><td><input type='checkbox' class='scratche_MAX' id='scratch6_MAX'><label for='scratch6_MAX'>scratch6_MAX</label></td></tr>";
                        echo "</table>";
                    echo "</fieldset>";
                echo "</div>";
                echo "<input type='button' id='ok' value='OK' onclick='matyldy(); scratche(); zavriNastaveni();'>";
            }
            else if($_GET["n"] == "gpus"){
                //období
                echo "<fieldset>";
                    echo "<legend>Volba období</legend>";
                        echo "<label for='select_obdobi'>Zvolte období:</label>";
                        echo "<select id='select_obdobi' onchange='gpus();'>";
                            echo "<option value='0' selected>5 dní (rozlišení 1min)</option>";
                            echo "<option value='1'>3 týdny (rozlišení 5min)</option>";
                            echo "<option value='2'>2 měsíce (rozlišení 30min)</option>";
                            echo "<option value='3'>10 let (rozlišení 2h)</option>";
                    echo "</select>";
                echo "</fieldset>";

                //řady
                echo "<fieldset>";
                    echo "<legend>Nastavení grafu GPU</legend>";
                    echo "<table>";
                    echo "<tr><td><input type='checkbox' class='gpu_DS' id='obsazene' checked><label for='vyuzite'>Využíté sloty</label></td></tr>";
                    echo "<tr><td><input type='checkbox' class='gpu_DS' id='dostupne' checked><label for='dostupne'>Dostupné sloty</label></td></tr>";
                    echo "</table>";
                echo "</fieldset>";
                echo "<input type='button' id='ok' value='OK' onclick='gpus(); zavriNastaveni();'>";
            }
        }
        //pro ostatní - kromě globálních
        else{
            //období
            echo "<fieldset>";
                echo "<legend>Volba období</legend>";
                echo "<label for='select_obdobi'>Zvolte období:</label>";
                echo "<select id='select_obdobi' onchange='nodes();'>";
                    echo "<option value='0' selected>5 dní (rozlišení 1min)</option>";
                    echo "<option value='1'>3 týdny (rozlišení 5min)</option>";
                    echo "<option value='2'>2 měsíce (rozlišení 30min)</option>";
                    echo "<option value='3'>10 let (rozlišení 2h)</option>";
                echo "</select>";
            echo "</fieldset>";
            //datové řady
            echo "<div>";
                echo "<div class='vlevo'>";
                    echo "<fieldset>";
                    echo "<legend>Sloupcový graf:</legend>";
                    echo "<input type='radio' id='graf_sloupcovy' name='graf' checked onchange='graf_changer();'><label for='graf_sloupcovy'><b>Sloupcový graf</b></label>";
                        echo "<table>";
                        echo "<tr><td><input type='radio' class='DS_sloup' name='sloup' id='slots' checked><label for='slots'>Sloty</label></td></tr>";
                        echo "<tr><td><input type='radio' class='DS_sloup' name='sloup' id='gpu'><label for='gpu'>GPU</label></td></tr>";

                        echo "</table>";
                    echo "</fieldset>";
                echo "</div>";
                echo "<div class='vpravo'>";
                    echo "<fieldset>";
                        echo "<legend>Spojnicový graf:</legend>";
                        echo "<input type='radio' id='graf_spojnicovy' name='graf' onchange='graf_changer();'><label for='graf_spojnicovy'><b>Spojnicový graf</b></label>";
                        echo "<table>";
                            echo "<tr><td><input type='checkbox' class='DS_spoj' id='cpu' checked disabled><label for='cpu'>CPU [%]</label></td></tr>";
                            echo "<tr><td><input type='checkbox' class='DS_spoj' id='mem_used' disabled><label for='mem_used'>Velikost využité paměti v [GB]</label></td></tr>";
            echo "<tr><td><input type='checkbox' class='DS_spoj' id='mem_total' disabled><label for='mem_total'>Celková velikost paměti v [GB]</label></td></tr>";
                            echo "<tr><td><input type='checkbox' class='DS_spoj' id='disk_free' disabled><label for='disk_free'>Velikost volného místa na disku v [GB]</label></td></tr>";
                        echo "</table>";
                    echo "</fieldset>";
                echo "</div>";
            echo "</div>";
            $groups = file_get_contents("groups");
            $groups = explode("\n",$groups);
            $groups_array = array();
            foreach($groups as $group){
                if(!empty($group)){
                    $tmp_array = array();
                    $tmp = explode(":",$group);
                    $tmp_array["name"] = trim($tmp[0],"@");
                    $tmp_array["nodes"] = $tmp[1];
                    $groups_array[$tmp_array["name"]] = $tmp_array;
                }
            }

            //uzly

            $group = $groups_array[$_GET["n"]];
            $nodes = explode(",",$group["nodes"]);
            echo "<fieldset>";
                echo "<legend>Vyberte uzly, které chcete zobrazit do grafů:</legend>";
                echo "<table>";
                $counter = 0;
                $count = 6;
                foreach($nodes as $node){
                    $checked = ($counter < 4);
                    if($counter % $count == 0){
                        echo "<tr>";
                        echo "<td><input type='checkbox' class='nodes' id='$node' ".($checked ? "checked" : "")."><label for='$node'>$node</label>";
                    }
                    else if($counter % $count == $count - 1){
                        echo "<td><input type='checkbox' class='nodes' id='$node' ".($checked ? "checked" : "")."><label for='$node'>$node</label>";
                        echo "</tr>";
                    }
                    else{
                        echo "<td><input type='checkbox' class='nodes' id='$node' ".($checked ? "checked" : "")."><label for='$node'>$node</label>";
                    }
                    $counter++;
                }
                echo "</table>";
            echo "</fieldset>";
            echo "<input type='button' id='ok' value='OK' onclick='nodes(); zavriNastaveni();'>";
        }
        echo "</div>";
    }
?>