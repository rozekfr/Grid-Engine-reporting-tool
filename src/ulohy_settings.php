<?php
    //statistiky
    if(empty($_GET["s"]) or (!empty($_GET["s"]) and $_GET["s"] == "statistiky")){
        echo "<div id='settings_content'>";
            echo "<fieldset>";
            echo "<legend>Nastavení období</legend>";
            echo "</fieldset>";

            echo "<fieldset>";
                echo "<legend>Nastavení sloupců do tabulky</legend>";
                echo "<table>";
                    //první řádek: id úlohy, uživatel, reálný čas, cpu čas
                    echo "<tr>";
                    echo "<td><input type='checkbox' id='id_ulohy' class='columns' checked disabled><label for='id_ulohy'>ID úlohy</label></td>";
                    echo "<td><input type='checkbox' id='uzivatel' class='columns' checked disabled><label for='uzivael'>Uživatel</label></td>";
                    echo "<td><input type='checkbox' id='realny_cas' class='columns' checked><label for='realny_cas'>Reálný čas</label></td>";
                    echo "<td><input type='checkbox' id='cpu_cas' class='columns' checked><label for='cpu_cas'>CPU čas</label></td>";
                    echo "</tr>";

                    //druhý řádek: počet tasků, efektivita, pruměrný čas na task, alokovaná paměť
                    echo "<tr>";
                        echo "<td><input type='checkbox' id='pocet_tasku' class='columns' checked><label for='pocet_tasku'>Počet tasků</label></td>";
                        echo "<td><input type='checkbox' id='efektivita' class='columns'><label for='efektivita'>Efektivita</label></td>";
                        echo "<td><input type='checkbox' id='prum_cas_na_task' class='columns'><label for='prum_cas_na_task'>Prům. čas tasku</label></td>";
                        echo "<td></td>";
                    echo "</tr>";

                    //třetí řádek: Využitá paměť
                    echo "<tr>";
                    echo "<td><input type='checkbox' id='alokovana_pamet_MB' class='columns' checked><label for='alokovana_pamet_MB'>Alokovaná paměť</label></td>";
                    echo "<td><input type='checkbox' id='vyuzita_pamet_MB' class='columns'><label for='vyuzita_pamet_MB'>Využitá paměť</label></td>";
                    echo "<td><input type='checkbox' id='max_vyuzita_pamet_MB' class='columns'><label for='max_vyuzita_pamet_MB'>Max. využitá paměť</label></td>";
                    echo "<td></td>";
                    echo "</tr>";
                echo "</table>";
            echo "</fieldset>";
            //nápověda
            echo "<fieldset>";
                echo "<legend>Nápověda</legend>";
                echo "<p><b>Řazení:</b><br>";
                echo "Šipky u sloupců slouží k řazení (šipka nahoru - vzestupně, šipka dolu - sestupně, čára - žádné řazení).</p>";
                echo "<p><b>Filtry:</b><br>";
                echo "Filtr je možné zadat pro každý sloupec. Povolené operátory jsou: <b>&lt;, &gt;, &lt;=, &gt;=, =, !=</b>. Do filtru je možné napsat i více podmínek pomocí <b>AND</b>, nebo <b>OR</b>. Podmínka může vypadat například takto: <b>&gt;= 5 AND &lt;= 10</b>. Je nutné dodržet mezery mezi operátory. Pokud zadáte filtr ve více sloupcích, je mezi nimi vztah \"a zároveň\" (AND).</p>";
            echo "</fieldset>";
        echo "</div>";
        echo "<input type='button' id='ok' value='OK' onclick='update_stats(null,0,\"ulohy_ajax\",\"jobs_stats\"); zavriNastaveni();'>";
    }

    //efektivita
    else if(!empty($_GET["s"]) and $_GET["s"] == "efektivita"){
        echo "<div id='settings_content'>";
            echo "<fieldset>";
                echo "<legend>Nastavení období</legend>";
            echo "</fieldset>";

            echo "<fieldset>";
                echo "<legend>Nastavení sloupců do tabulky</legend>";
                echo "<table>";
                    //první řádek: id úlohy, uživatel, efektivita, alokovaná paměť
                    echo "<tr>";
                        echo "<td><input type='checkbox' id='id_ulohy' class='columns' checked disabled><label for='id_ulohy'>ID úlohy</label></td>";
                        echo "<td><input type='checkbox' id='uzivatel' class='columns' checked disabled><label for='uzivael'>Uživatel</label></td>";
                        echo "<td><input type='checkbox' id='efektivita' class='columns' checked><label for='efektivita'>Efektivita výpočtu</label></td>";
                        echo "<td><input type='checkbox' id='alokovana_pamet_MB' class='columns' checked><label for='alokovana_pamet_MB'>Efektivita paměti</label></td>";
                    echo "</tr>";
                echo "</table>";
            echo "</fieldset>";
            //nápověda
            echo "<fieldset>";
                echo "<legend>Nápověda</legend>";
                echo "<p><b>Řazení:</b><br>";
                echo "Šipky u sloupců slouží k řazení (šipka nahoru - vzestupně, šipka dolu - sestupně, čára - žádné řazení).</p>";
                echo "<p><b>Filtry:</b><br>";
                echo "Filtr je možné zadat pro každý sloupec. Povolené operátory jsou: <b>&lt;, &gt;, &lt;=, &gt;=, =, !=</b>. Do filtru je možné napsat i více podmínek pomocí <b>AND</b>, nebo <b>OR</b>. Podmínka může vypadat například takto: <b>&gt;= 5 AND &lt;= 10</b>. Je nutné dodržet mezery mezi operátory. Pokud zadáte filtr ve více sloupcích, je mezi nimi vztah \"a zároveň\" (AND).</p>";
            echo "</fieldset>";
        echo "</div>";
        echo "<input type='button' id='ok' value='OK' onclick='update_stats(null,0,\"ulohy_efektivita_ajax\",\"jobs_stats\"); zavriNastaveni();'>";
    }

    //čekající
    else if(!empty($_GET["s"]) and $_GET["s"] == "cekajici") {
        echo "<div id='settings_content'>";
        echo "<fieldset>";
            echo "<legend>Nastavení sloupců do tabulky</legend>";
            echo "<table>";
            //první řádek: id úlohy, uživatel, efektivita, alokovaná paměť
            echo "<tr>";
                echo "<td><input type='checkbox' id='id_ulohy' class='columns' checked disabled><label for='id_ulohy'>ID úlohy</label></td>";
                echo "<td><input type='checkbox' id='uzivatel' class='columns' checked disabled><label for='uzivael'>Uživatel</label></td>";
                echo "<td><input type='checkbox' id='cas_odeslani' class='columns' checked><label for='cas_odeslani'>Čas odeslání</label></td>";
                echo "<td><input type='checkbox' id='stav' class='columns' checked><label for='stav'>Stav</label></td>";
                echo "<td><input type='checkbox' id='pocet_tasku' class='columns' checked><label for='pocet_tasku'>Počet tasků</label></td>";
                echo "</tr>";
            echo "</table>";
        echo "</fieldset>";
        //nápověda
        echo "<fieldset>";
            echo "<legend>Nápověda</legend>";
            echo "<p><b>Řazení:</b><br>";
            echo "Šipky u sloupců slouží k řazení (šipka nahoru - vzestupně, šipka dolu - sestupně, čára - žádné řazení).</p>";
            echo "<p><b>Filtry:</b><br>";
            echo "Filtr je možné zadat pro každý sloupec. Povolené operátory jsou: <b>&lt;, &gt;, &lt;=, &gt;=, =, !=</b>. Do filtru je možné napsat i více podmínek pomocí <b>AND</b>, nebo <b>OR</b>. Podmínka může vypadat například takto: <b>&gt;= 5 AND &lt;= 10</b>. Je nutné dodržet mezery mezi operátory. Pokud zadáte filtr ve více sloupcích, je mezi nimi vztah \"a zároveň\" (AND).</p>";
        echo "</fieldset>";
        echo "</div>";
        echo "<input type='button' id='ok' value='OK' onclick='update_stats(null,0,\"ulohy_cekajici_ajax\",\"jobs_stats\"); zavriNastaveni();'>";
    }
?>