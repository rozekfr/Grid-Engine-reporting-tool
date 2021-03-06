<?php
    //statistiky
    if(empty($_GET["s"]) or (!empty($_GET["s"]) and $_GET["s"] == "statistiky")){
        echo "<div id='settings_content'>";
            echo "<div class='vlevo'>";
	        echo "<fieldset>";
            echo "<legend>Výběr období</legend>";
            echo "<label for='select_obdobi'>Vyberte období: </label>";
	        echo "<select id='select_obdobi' onchange='obdobi_ajax();clear_obdobi()'>";
	            $dotazTabulky = $db->query("SELECT TABLE_NAME AS tab,SUBSTRING(TABLE_NAME,20) as nazev FROM information_schema.tables WHERE table_name LIKE 'sge_rt_stats_ulohy%' ORDER BY CASE tab WHEN 'sge_rt_stats_ulohy_posledni_tyden' THEN 1  WHEN 'sge_rt_stats_ulohy_posledni_mesic' THEN 2 ELSE tab END ASC");
	            while($tabulky = $dotazTabulky->fetch_assoc()){
		            switch($tabulky["nazev"]){
			            case '':
				            $nazev = "všechna dostupná data";
				            break;

			            case 'posledni_tyden':
				            $nazev = "poslední dostupný týden";
				            break;

			            case 'posledni_mesic':
				            $nazev = "poslední dostupný měsíc";
				            break;


			            default:
				            $nazev = str_replace("_"," ",$tabulky["nazev"]);
		            }
		            echo "<option value='{$tabulky["tab"]}'>$nazev</option>";
	            }
	        echo "</select>";
	        echo "<input type='hidden' id='last_update' value=''>";
            echo "</fieldset>";
	        echo "</div>";
			echo "<div class='vpravo'>";
	            echo "<fieldset>";
	            echo "<legend>Jiné období:</legend>";
	            echo "<table class='obdobi'>";
	            echo "<tr><th>Od</th><th>Do</th></tr>";
				echo "<tr><td><input type='button' onclick='aktDat(event,\"datum_od\",\"kal_od\");' class='kalendar_button' value='kalendář'><br><input type='text' name='datum' class='vystupKal' id='datum_od' readonly><br><input type='time' id='time_od' value='00:00'></td><td><input type='button' onclick='aktDat(event,\"datum_do\",\"kal_do\");' class='kalendar_button' value='kalendář'><br><input type='text' name='datum' class='vystupKal' id='datum_do' readonly><br><input type='time' id='time_do' value='00:00'></td></tr>";
	            echo "<tr><td></td><td><input type='button' class='vymaz' value='vymaž' onclick='clear_obdobi();'></td></tr>";
	            echo "</table>";
	            echo "</fieldset>";
	        echo "</div>";
            echo "<div>";
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

                    //druhý řádek: počet tasků, počet GPU, efektivita, alokovaná paměť
                    echo "<tr>";
                        echo "<td><input type='checkbox' id='pocet_tasku' class='columns' checked><label for='pocet_tasku'>Počet tasků</label></td>";
	                    echo "<td><input type='checkbox' id='pocet_gpu' class='columns'><label for='pocet_gpu'>Počet GPU</label></td>";
	                    echo "<td><input type='checkbox' id='efektivita' class='columns'><label for='efektivita'>Efektivita</label></td>";
	                    echo "<td><input type='checkbox' id='spotreba' class='columns' checked><label for='spotreba'>Spotřeba</label></td>";
                    echo "</tr>";

                    //třetí řádek: pruměrný čas na task, Alokovaná paměť, Využitá paměť, Max. využitá paměť
                    echo "<tr>";
				    echo "<td><input type='checkbox' id='prum_cas_na_task' class='columns'><label for='prum_cas_na_task'>Prům. čas tasku</label></td>";
                    echo "<td><input type='checkbox' id='alokovana_pamet_MB' class='columns'><label for='alokovana_pamet_MB'>Alokovaná paměť</label></td>";
                    echo "<td><input type='checkbox' id='vyuzita_pamet_MB' class='columns'><label for='vyuzita_pamet_MB'>Využitá paměť</label></td>";
                    echo "<td><input type='checkbox' id='max_vyuzita_pamet_MB' class='columns'><label for='max_vyuzita_pamet_MB'>Max. využitá paměť</label></td>";
                    echo "</tr>";
                echo "</table>";
            echo "</fieldset>";
	        echo "</div>";
            //nápověda
	        echo "<div>";
            echo "<fieldset>";
			    echo "<legend>Nápověda</legend>";
			    echo "<h3>Řazení:</h3>";
			    echo "<p>Šipky u sloupců slouží k řazení (šipka nahoru - vzestupně, šipka dolu - sestupně, čára - žádné řazení).</p>";
			    echo "<h3>Filtry:</h3>";
			    echo "<p>Filtr je možné zadat pro každý sloupec. Povolené operátory jsou: <b>&lt;, &gt;, &lt;=, &gt;=, =, !=</b>. Do filtru je možné napsat i více podmínek pomocí <b>AND</b>, nebo <b>OR</b>. Podmínka může vypadat například takto: <b>&gt;= 5 AND &lt;= 10</b>. Je nutné dodržet mezery mezi operátory. Pokud zadáte filtr ve více sloupcích, je mezi nimi vztah \"a zároveň\" (AND).</p>";
			    echo "<h3>Hodnoty:</h3>";
			    echo "<p><b>Celá čísla</b> se zadavají klasicky.</p>";
			    echo "<p><b>Desetinná čísla</b> je možné psát s tečkou i čárkou.</p>";
			    echo "<p><b>Řetězce</b> se zadávají bez uvozovek.</p>";
			    echo "<p>U hodnot <b>paměti</b> uvádějte jednotku (kB, MB, GB, TB) pro porovnání bez mezery (bez jednotky je hodnota v MB).</p>";
			    echo "<p>U hodnot <b>spotřeby</b> uvádějte jednotku (mWh, Wh, kWh) pro porovnání bez mezery (bez jednotky je hodnota ve W).</p>";
			    echo "<p><b>Čas</b> uvádějte pomocí roků, dnů, hodin, minut a sekund bez mezer následovně: 1r20d8h0m0s nebo zkráceně 1r20d8h (bez jednotky je hodnota v sekundách).</p>";
            echo "</fieldset>";
	        echo "</div>";
        echo "</div>";
        echo "<input type='button' id='ok' value='OK' onclick='obdobi_ajax(); update_stats(null,0,\"ulohy_ajax\",\"jobs_stats\"); zavriNastaveni();'>";
    }

    //efektivita
    else if(!empty($_GET["s"]) and $_GET["s"] == "efektivita"){
        echo "<div id='settings_content'>";
		    echo "<div class='vlevo'>";
		    echo "<fieldset>";
		    echo "<legend>Výběr období</legend>";
		    echo "<label for='select_obdobi'>Vyberte období: </label>";
		    echo "<select id='select_obdobi' onchange='obdobi_ajax();clear_obdobi()'>";
		    $dotazTabulky = $db->query("SELECT TABLE_NAME AS tab,SUBSTRING(TABLE_NAME,20) as nazev FROM information_schema.tables WHERE table_name LIKE 'sge_rt_stats_ulohy%' ORDER BY CASE tab WHEN 'sge_rt_stats_ulohy_posledni_tyden' THEN 1  WHEN 'sge_rt_stats_ulohy_posledni_mesic' THEN 2 ELSE tab END ASC");
		    while($tabulky = $dotazTabulky->fetch_assoc()){
			    switch($tabulky["nazev"]){
				    case '':
					    $nazev = "všechna dostupná data";
					    break;

				    case 'posledni_tyden':
					    $nazev = "poslední dostupný týden";
					    break;

				    case 'posledni_mesic':
					    $nazev = "poslední dostupný měsíc";
					    break;


				    default:
					    $nazev = str_replace("_"," ",$tabulky["nazev"]);
			    }
			    echo "<option value='{$tabulky["tab"]}'>$nazev</option>";
		    }
		    echo "</select>";
		    echo "<input type='hidden' id='last_update' value=''>";
		    echo "</fieldset>";
		    echo "</div>";
		    echo "<div class='vpravo'>";
		    echo "<fieldset>";
			    echo "<legend>Jiné období:</legend>";
			    echo "<table class='obdobi'>";
				    echo "<tr><th>Od</th><th>Do</th></tr>";
				    echo "<tr><td><input type='button' onclick='aktDat(event,\"datum_od\",\"kal_od\");' class='kalendar_button' value='kalendář'><br><input type='text' name='datum' class='vystupKal' id='datum_od' readonly><br><input type='time' id='time_od' value='00:00'></td><td><input type='button' onclick='aktDat(event,\"datum_do\",\"kal_do\");' class='kalendar_button' value='kalendář'><br><input type='text' name='datum' class='vystupKal' id='datum_do' readonly><br><input type='time' id='time_do' value='00:00'></td></tr>";
				    echo "<tr><td></td><td><input type='button' class='vymaz' value='vymaž' onclick='clear_obdobi();'></td></tr>";
			    echo "</table>";
		    echo "</fieldset>";
		    echo "</div>";
			echo "<div>";
            echo "<fieldset>";
                echo "<legend>Nastavení sloupců do tabulky</legend>";
                echo "<table>";
                    //první řádek: id úlohy, uživatel, efektivita, alokovaná paměť
                    echo "<tr>";
                        echo "<td><input type='checkbox' id='id_ulohy' class='columns' checked disabled><label for='id_ulohy'>ID úlohy</label></td>";
                        echo "<td><input type='checkbox' id='uzivatel' class='columns' checked disabled><label for='uzivatel'>Uživatel</label></td>";
                        echo "<td><input type='checkbox' id='efektivita' class='columns' checked><label for='efektivita'>Efektivita výpočtu</label></td>";
                        echo "<td><input type='checkbox' id='alokovana_pamet_MB' class='columns' checked><label for='alokovana_pamet_MB'>Efektivita paměti</label></td>";
                    echo "</tr>";
                echo "</table>";
            echo "</fieldset>";
	        echo "</div>";

	        //nápověda
	        echo "<div>";
            echo "<fieldset>";
			    echo "<legend>Nápověda</legend>";
			    echo "<h3>Řazení:</h3>";
			    echo "<p>Šipky u sloupců slouží k řazení (šipka nahoru - vzestupně, šipka dolu - sestupně, čára - žádné řazení).</p>";
			    echo "<h3>Filtry:</h3>";
			    echo "<p>Filtr je možné zadat pro každý sloupec. Povolené operátory jsou: <b>&lt;, &gt;, &lt;=, &gt;=, =, !=</b>. Do filtru je možné napsat i více podmínek pomocí <b>AND</b>, nebo <b>OR</b>. Podmínka může vypadat například takto: <b>&gt;= 5 AND &lt;= 10</b>. Je nutné dodržet mezery mezi operátory. Pokud zadáte filtr ve více sloupcích, je mezi nimi vztah \"a zároveň\" (AND).</p>";
			    echo "<h3>Hodnoty:</h3>";
			    echo "<p><b>Celá čísla</b> se zadavají klasicky.</p>";
			    echo "<p><b>Desetinná čísla</b> je možné psát s tečkou i čárkou.</p>";
			    echo "<p><b>Řetězce</b> se zadávají bez uvozovek.</p>";
			    echo "<p>U hodnot <b>paměti</b> uvádějte jednotku (kB, MB, GB, TB) pro porovnání bez mezery (bez jednotky je hodnota v MB).</p>";
			    echo "<p>U hodnot <b>spotřeby</b> uvádějte jednotku (mWh, Wh, kWh) pro porovnání bez mezery (bez jednotky je hodnota ve W).</p>";
			    echo "<p><b>Čas</b> uvádějte pomocí roků, dnů, hodin, minut a sekund bez mezer následovně: 1r20d8h0m0s nebo zkráceně 1r20d8h (bez jednotky je hodnota v sekundách).</p>";
            echo "</fieldset>";
	        echo "</div>";
        echo "</div>";
        echo "<input type='button' id='ok' value='OK' onclick='obdobi_ajax(); update_stats(null,0,\"ulohy_efektivita_ajax\",\"jobs_stats\"); zavriNastaveni();'>";
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
		    echo "<h3>Řazení:</h3>";
		    echo "<p>Šipky u sloupců slouží k řazení (šipka nahoru - vzestupně, šipka dolu - sestupně, čára - žádné řazení).</p>";
		    echo "<h3>Filtry:</h3>";
		    echo "<p>Filtr je možné zadat pro každý sloupec. Povolené operátory jsou: <b>&lt;, &gt;, &lt;=, &gt;=, =, !=</b>. Do filtru je možné napsat i více podmínek pomocí <b>AND</b>, nebo <b>OR</b>. Podmínka může vypadat například takto: <b>&gt;= 5 AND &lt;= 10</b>. Je nutné dodržet mezery mezi operátory. Pokud zadáte filtr ve více sloupcích, je mezi nimi vztah \"a zároveň\" (AND).</p>";
		    echo "<h3>Hodnoty:</h3>";
		    echo "<p><b>Celá čísla</b> se zadavají klasicky.</p>";
		    echo "<p><b>Desetinná čísla</b> je možné psát s tečkou i čárkou.</p>";
		    echo "<p><b>Řetězce</b> se zadávají bez uvozovek.</p>";
		    echo "<p>U hodnot <b>paměti</b> uvádějte jednotku (kB, MB, GB, TB) pro porovnání bez mezery (bez jednotky je hodnota v MB).</p>";
		    echo "<p>U hodnot <b>spotřeby</b> uvádějte jednotku (mWh, Wh, kWh) pro porovnání bez mezery (bez jednotky je hodnota ve W).</p>";
		    echo "<p><b>Čas</b> uvádějte pomocí roků, dnů, hodin, minut a sekund bez mezer následovně: 1r20d8h0m0s nebo zkráceně 1r20d8h (bez jednotky je hodnota v sekundách).</p>";
        echo "</fieldset>";
        echo "</div>";
        echo "<input type='button' id='ok' value='OK' onclick='update_stats(null,0,\"ulohy_cekajici_ajax\",\"jobs_stats\"); zavriNastaveni();'>";
    }
?>