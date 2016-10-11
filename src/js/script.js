function nastaveni(){
	var nastaveni = document.getElementById("settings");
	var cerno = document.getElementById("black");
	nastaveni.style.display = "block";
	cerno.style.display = "block";
	var vyskaNastaveni = nastaveni.offsetHeight;
	var sirkaNastaveni = nastaveni.offsetWidth;
	nastaveni.style.left = Math.floor((window.innerWidth - sirkaNastaveni)/2) + "px";
	nastaveni.style.top = Math.floor((window.innerHeight - vyskaNastaveni)/2) + "px";
}
function zavriNastaveni(){
	var nastaveni = document.getElementById("settings");
	var cerno = document.getElementById("black");
	nastaveni.style.display = "none";
	cerno.style.display = "none";
}

function close_divs(){
    if(document.getElementById("settings").style.display == "block"){
        document.getElementById("settings").style.display = "none";
    }
    if(document.getElementById("info").style.display == "block"){
        document.getElementById("info").style.display = "none";
        if(timeout) {
            clearTimeout(timeout);
        }
    }
    if(document.getElementById("konfigurace").style.display == "block"){
        document.getElementById("konfigurace").style.display = "none";
    }
    document.getElementById("black").style.display = "none";
}

function center_info(){
	var sirkaOkna = window.innerWidth;
	var vyskaOkna = window.innerHeight;

	var info = document.getElementById("info");

	info.style.marginLeft = (sirkaOkna - info.offsetWidth)/2 + "px";
	info.style.marginTop = (vyskaOkna - info.offsetHeight)/2 + "px";
}