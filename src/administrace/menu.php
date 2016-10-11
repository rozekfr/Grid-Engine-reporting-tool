<?php
    //informace
    if(!empty($_GET["s"]) and $_GET["s"] == "hlavni"){
      echo "<a href='hlavni.php?s=hlavni' class='aktivni'>Informace</a>";
    }
    else{
      echo "<a href='hlavni.php?s=hlavni'>Informace</a>";
    }
    //administrátoři
    if(!empty($_GET["s"]) and $_GET["s"] == "admini"){
      echo "<a href='admini.php?s=admini' class='aktivni'>Administrátoři</a>";
    }
    else{
      echo "<a href='admini.php?s=admini'>Administrátoři</a>";
    }
    //uživatelé
    if(!empty($_GET["s"]) and $_GET["s"] == "uzivatele"){
        echo "<a href='uzivatele.php?s=uzivatele' class='aktivni'>Uživatelé</a>";
    }
    else{
        echo "<a href='uzivatele.php?s=uzivatele'>Uživatelé</a>";
    }
	//skupiny uživatelů
	if(!empty($_GET["s"]) and $_GET["s"] == "skupiny"){
		echo "<a href='skupiny.php?s=skupiny' class='aktivni'>Skupiny uživatelů</a>";
	}
	else{
		echo "<a href='skupiny.php?s=skupiny'>Skupiny uživatelů</a>";
	}
    //přehled uzlů
    if(!empty($_GET["s"]) and $_GET["s"] == "uzly"){
        echo "<a href='uzly.php?s=uzly' class='aktivni'>Přehled uzlů</a>";
    }
    else{
        echo "<a href='uzly.php?s=uzly'>Přehled uzlů</a>";
    }
    //spotřeba uzlů
    if(!empty($_GET["s"]) and $_GET["s"] == "spotreba"){
        echo "<a href='spotreba.php?s=spotreba' class='aktivni'>Spotřeba uzlů</a>";
    }
    else{
        echo "<a href='spotreba.php?s=spotreba'>Spotřeba uzlů</a>";
    }
    //konfigurace uzlů
    if(!empty($_GET["s"]) and $_GET["s"] == "konfigurace"){
        echo "<a href='konfigurace.php?s=konfigurace' class='aktivni'>Konfigurace uzlů</a>";
    }
    else{
        echo "<a href='konfigurace.php?s=konfigurace'>Konfigurace uzlů</a>";
    }