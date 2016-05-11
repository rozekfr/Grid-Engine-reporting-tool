<?php

//sloty
if(empty($_GET["m"]) or (!empty($_GET["m"]) and $_GET["m"] == "cluster")){
	echo "<a href='index.php?m=cluster' class='aktivni'>Cluster</a>";
}
else{
	echo "<a href='index.php?m=cluster'>Cluster</a>";
}

//uživatelé
if(!empty($_GET["m"]) and $_GET["m"] == "uzivatele"){
	echo "<a href='index.php?m=uzivatele' class='aktivni'>Uživatelé</a>";
}
else{
	echo "<a href='index.php?m=uzivatele'>Uživatelé</a>";
}

//úlohy
if(!empty($_GET["m"]) and $_GET["m"] == "ulohy"){
	echo "<a href='index.php?m=ulohy' class='aktivni'>Úlohy</a>";
}
else{
	echo "<a href='index.php?m=ulohy'>Úlohy</a>";
}

if(!empty($_GET["m"]) and $_GET["m"] == "ulohy"){
	echo "<div id='podmenu'>";
	if(empty($_GET["s"]) or (!empty($_GET["s"]) and $_GET["s"] == "statistiky")){
		echo "<a href='index.php?m=ulohy&amp;s=statistiky' class='aktivni'>Statistiky</a>";
	}
	else{
		echo "<a href='index.php?m=ulohy&amp;s=statistiky'>Statistiky</a>";
	}
    if(!empty($_GET["s"]) and $_GET["s"] == "efektivita"){
        echo "<a href='index.php?m=ulohy&amp;s=efektivita' class='aktivni'>Efektivita</a>";
    }
    else {
        echo "<a href='index.php?m=ulohy&amp;s=efektivita'>Efektivita</a>";
    }
	if(!empty($_GET["s"]) and $_GET["s"] == "cekajici"){
		echo "<a href='index.php?m=ulohy&amp;s=cekajici' class='aktivni'>Čekající</a>";
	}
	else{
		echo "<a href='index.php?m=ulohy&amp;s=cekajici'>Čekající</a>";
	}
    if(!empty($_GET["s"]) and $_GET["s"] == "blokujici"){
        echo "<a href='index.php?m=ulohy&amp;s=blokujici' class='aktivni'>Blokující</a>";
    }
    else{
        echo "<a href='index.php?m=ulohy&amp;s=blokujici'>Blokující</a>";
    }
	echo "</div>";
}

//prostředky
if(!empty($_GET["m"]) and $_GET["m"] == "prostredky"){
	echo "<a href='index.php?m=prostredky&amp;s=globals&amp;n=servers' class='aktivni'>Prostředky</a>";
}
else{
	echo "<a href='index.php?m=prostredky&amp;s=globals&amp;n=servers'>Prostředky</a>";
}

//podmenu prostředky
if(!empty($_GET["m"]) and $_GET["m"] == "prostredky"){
	echo "<div id='podmenu'>";
    //nastavení skupin a zobrazené podskupiny
    $groups = array("globals" => "Globální","nodes" => "Uzly","blocks" => "Bloky","classes" => "Učebny");
    $subgroups = array("globals" => "servers","nodes" => "allhosts","blocks" => "PCNxxx","classes" => "PCN103");
    foreach($groups as $group => $name){
        if(!empty($_GET["s"]) and $_GET["s"] == "$group"){
            echo "<a href='index.php?m=prostredky&amp;s=$group&amp;n={$subgroups[$group]}' class='aktivni'>$name</a>";
        }
        else{
            echo "<a href='index.php?m=prostredky&amp;s=$group&amp;n={$subgroups[$group]}'>$name</a>";
        }
    }
	echo "</div>";
}


//administrace
echo "<a href='administrace/index.php' id='administrace'>Administrace</a>";
?>
