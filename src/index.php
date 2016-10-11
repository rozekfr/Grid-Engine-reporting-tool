<?php
	ini_set("default_charset", "UTF-8");
	header("Content-Type: text/html; UTF-8");
	(empty($_GET["m"]) ? $_GET["m"] = "cluster" : $_GET["m"] = $_GET["m"]);
	(empty($_GET["p"]) ? $_GET["p"] = "prehled" : $_GET["p"] = $_GET["p"]);
    include_once 'pripojeniDB.php';
?>
<!DOCTYPE HTML>
<html>
<head>
	<meta  charset="UTF-8">
	<link rel="shortcut icon" href="favicon.png">
	<link rel="stylesheet" type="text/css" href="css/styly.css">
	<script type="text/javascript" src="js/script.js"></script>
	<title>Sun Grid Engine - Reporting tool</title>
	<?php
		include 'header.php';
	?>
</head>
<body>
	<div id="black" onclick="close_divs()"></div>
    <div id="kal_od" class="cal"></div><div id="kal_do" class="cal"></div> <!-- kalendáře -->
    <div id='konfigurace'>
        <a href='javarscript:void(0)' class='zavri' onclick='zavriKonfiguraci();'>X</a>
    </div>
    <div id="info">
		<a href="javascript:void(0)" class='zavri' onclick="close_divs();">X</a>
		<div id="info_header"></div>
		<div id="info_content"></div>
	</div>
	<div id="settings">
        <a href="javascript:void(0)" class='zavri' onclick="close_divs();">X</a>
		<h2>Nastavení</h2>
		<?php
			include 'settings.php';
		?>
	</div>
	<div id="stranka">
		<div id="top">
			<h1>Sun Grid Engine - Reporting tool</h1>
		</div>
		<div id="menu">
		<?php
			include 'menu.php';
		?>
		</div>
		<div id="content">
			<div id="nastaveni">
				<div id='vlevo'>
					<?php
                        echo "<p>Zvolené období je: <b id='obdobi'></b>. ".(($_GET["m"] == "uzivatele" or ($_GET["m"] == "ulohy" and (empty($_GET["s"]) or $_GET["s"] == "statistiky" or $_GET["s"] == "efektivita"))) ? "Poslední aktualizace: <b id='last'></b>." : "")."</p>";
                    ?>
				</div>
				<div id='vpravo'>
					<img src="foto/setting.png" alt="nastavení" onclick="nastaveni();">
				</div>
			</div>
			<?php
				if(!empty($_GET["m"]) and $_GET["m"] == "prostredky"){
					echo "<div id='prostredky_podmenu'>";
						include 'prostredky_podmenu.php';
					echo "</div>";
				}
			?>
			<div id="text">
				<?php
					include 'content.php';
				?>
			</div>
		</div>
	</div>
</body>
</html>
