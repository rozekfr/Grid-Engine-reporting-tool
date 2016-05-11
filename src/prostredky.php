<?php
	if(!empty($_GET["s"]) and $_GET["s"] == "globals"){
		if(!empty($_GET["n"]) and $_GET["n"] == "servers") {
			echo "<div id='matyldy'>";
				echo "<h2>Matyldy</h2>";
				echo "<input type='button' value='reset zoom' onclick='matyldy();'>";
				echo "<div id='matyldy_graph'>";
                    include "loading.html";
                echo "</div>";
			echo "</div>";
			echo "<div id='scratche'>";
				echo "<h2>Scratche</h2>";
				echo "<input type='button' value='reset zoom' onclick='scratche();'>";
				echo "<div id='scratche_graph'>";
                    include "loading.html";
                echo "</div>";
			echo "</div>";
		}
		else if(!empty($_GET["n"]) and $_GET["n"] == "gpus"){
			echo "<div id='gpus'>";
                echo "<h2>Dostupnost grafických karet</h2>";
                echo "<input type='button' value='reset zoom' onclick='gpus();'>";
                echo "<div id='gpus_graph'>";
                    include "loading.html";
                echo "</div>";
            echo "</div>";
		}
	}
?>

