<?php
    //blokující
    if(!empty($_GET["s"]) and ($_GET["s"] == "blokujici")){
        echo "<div id='resource'>";
            echo "<label for='resource_list'>Zde zadejte resource list:</label>";
            echo "<textarea id='resource_list'></textarea>";
            echo "<input type='button' value='odeslat' onclick='resource_list_request();'>";
        echo "</div>";
        echo "<h2>Seznam blokujících úloh</h2>";
        echo "<div id='jobs_stats'>";
            echo "<p class='info'>Seznam je prázdný, nezvolili jste resource list.</p>";
        echo "</div>";
    }
    //ostatní podstránky
    else{
        echo "<div id='jobs_stats'></div>";
    }
?>