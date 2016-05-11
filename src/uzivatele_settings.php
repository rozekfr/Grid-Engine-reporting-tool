<?php
    echo "<div id='settings_content'>";
        echo "<fieldset>";
            echo "<legend>Nastavení období</legend>";
        echo "</fieldset>";

        echo "<fieldset>";
            echo "<legend>Nastavení sloupců do tabulky</legend>";
            echo "<table>";
                //první řádek: uživatel, skupina, pocet_uloh, pocet_tasku
                echo "<tr>";
                    echo "<td><input type='checkbox' id='uzivatel' class='columns' checked disabled><label for='uzivatel'>Uživatel</label></td>";
                    echo "<td><input type='checkbox' id='skupina' class='columns' checked disabled><label for='skupina'>Skupina</label></td>";
                    echo "<td><input type='checkbox' id='pocet_uloh' class='columns' checked><label for='pocet_uloh'>Počet úloh</label></td>";
                    echo "<td><input type='checkbox' id='pocet_tasku' class='columns' checked><label for='pocet_tasku'>Počet tasků</label></td>";
                echo "</tr>";
                //druhý řádek: realny_cas, cpu_cas, prum_cas_na_ulohu, prum_cas_na_tas
                echo "<tr>";
                    echo "<td><input type='checkbox' id='realny_cas' class='columns' checked><label for='realny_cas'>Reálný čas</label></td>";
                    echo "<td><input type='checkbox' id='cpu_cas' class='columns' checked><label for='cpu_cas'>CPU čas</label></td>";
                    echo "<td><input type='checkbox' id='prum_cas_na_ulohu' class='columns'><label for='prum_cas_na_ulohu'>Prům. čas úlohy</label></td>";
                    echo "<td><input type='checkbox' id='prum_cas_na_task' class='columns'><label for='prum_cas_na_task'>Prům. čas tasku</label></td>";
                echo "</tr>";

                //třetí řádek: efektivita, spotreba
                echo "<tr>";
                echo "<td><input type='checkbox' id='efektivita' class='columns'><label for='efektivita'>Efektivita</label></td>";
                echo "<td><input type='checkbox' id='spotreba' class='columns'><label for='spotreba'>Spotřeba</label></td>";
                echo "<td></td>";
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
    echo "<input type='button' id='ok' value='OK' onclick='update_stats(null,0,\"uzivatele_ajax\",\"users_stats\"); zavriNastaveni();'>";
?>