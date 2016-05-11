<?php
    echo "<div id='slots'>";
        echo "<h2>Využití clusteru z pohledu slotů</h2>";
        echo "<input type='button' value='reset zoom' onclick='slots();'>";
        echo "<div id='slots_graph'>";
            include 'loading.html';
        echo "</div>";
    echo "</div>";
    echo "<div id='jobs'>";
        echo "<h2>Využití clusteru z pohledu úloh</h2>";
        echo "<input type='button' value='reset zoom' onclick='jobs();'>";
        echo "<div id='jobs_graph'>";
            include 'loading.html';
        echo "</div>";
    echo "</div>";
?>