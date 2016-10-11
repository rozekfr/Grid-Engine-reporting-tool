<?php
    //administrátoři
    if(!empty($_GET["s"]) and $_GET["s"] == "admini"){
        $dotazAktualniAdmin = $db->query("SELECT id,superadmin FROM admini WHERE login='{$_SESSION["login"]}'");
        $aktualniAdmin = $dotazAktualniAdmin->fetch_assoc();
        if(empty($_GET["a"])){
            if($aktualniAdmin["superadmin"]) {
                echo "<a href='admini.php?s=admini&amp;u=pa'>přidat administrátora</a>";
            }
        }
        else if(!empty($_GET["a"])){
            echo "<a href='admini.php?s=admini'>zpět</a>";
            if($aktualniAdmin["id"] == $_GET["a"]){
                echo "<a href='admini.php?s=admini&amp;a={$_GET["a"]}&amp;u=zh'>změnit heslo</a>";
            }
            if($aktualniAdmin["superadmin"] and $aktualniAdmin["id"] != $_GET["a"]) {
                echo "<a href='admini.php?s=admini&amp;a={$_GET["a"]}&amp;u=ua'>upravit administrátora</a>";
                echo "<a href='admini.php?s=admini&amp;a={$_GET["a"]}&amp;u=sa' onclick='return confirm(\"Opravdu chcete smazat tohoto administrátora?\");'>smazat administrátora</a>";
            }
        }
    }
    //uzivatele
    if(!empty($_GET["s"]) and $_GET["s"] == "uzivatele"){
        if(!empty($_GET["uzivatel"]) and !empty($_GET["detail"])){
            echo "<a href='uzivatele.php?s={$_GET["s"]}'>zpět</a>";
        }
    }
    //skupiny uživatelů
	if(!empty($_GET["s"]) and $_GET["s"] == "skupiny"){
		if(empty($_GET["skupina"])){
			echo "<a href='skupiny.php?s={$_GET["s"]}&amp;u=vs'>vytvořit skupinu</a>";
		}
		else{
			echo "<a href='skupiny.php?s={$_GET["s"]}'>Zpět</a>";
            echo "<a href='skupiny.php?s={$_GET["s"]}&amp;skupina={$_GET["skupina"]}&amp;u=us'>upravit skupinu</a>";
			echo "<a href='skupiny.php?s={$_GET["s"]}&amp;skupina={$_GET["skupina"]}&amp;u=ss' onclick='return confirm(\"Opravdu chcete smazat tuto skupinu?\")'>smazat skupinu</a>";
		}
	}
    //spotreba
    if(!empty($_GET["s"]) and $_GET["s"] == "spotreba"){
        if(!empty($_GET["k"])){
            echo "<a href='spotreba.php?s=spotreba'>zpět</a>";
            echo "<a href='spotreba.php?s=spotreba&amp;k={$_GET["k"]}&amp;u=us'>upravit spotřebu</a>";
            echo "<a href='spotreba.php?s=spotreba&amp;k={$_GET["k"]}&amp;u=ss'>smazat spotřebu</a>";
        }
    }
    //konfigurace
    if(!empty($_GET["s"]) and $_GET["s"] == "konfigurace"){
        if(empty($_GET["k"])) {
            echo "<a href='konfigurace.php?s=konfigurace&amp;u=pk'>přidat konfiguraci</a>";
        }
        else if(!empty($_GET["k"])){
            echo "<a href='konfigurace.php?s=konfigurace'>zpět</a>";
            echo "<a href='konfigurace.php?s=konfigurace&amp;k={$_GET["k"]}&amp;u=uk'>upravit konfiguraci</a>";
            echo "<a href='konfigurace.php?s=konfigurace&amp;k={$_GET["k"]}&amp;u=sk' onclick='return confirm(\"Opravdu chcete smazat tuto konfiguraci?\")'>smazat konfiguraci</a>";
        }
    }