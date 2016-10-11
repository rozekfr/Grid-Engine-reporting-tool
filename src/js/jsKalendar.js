//kalendar
function aktDat(event,out,div){
    if (document.getElementById(out).value == "0000-00-00" || document.getElementById(out).value == "") {
        datum = new Date();
        rok = datum.getFullYear();
        mesic = datum.getMonth();
    }
    else{
        rok = Number(document.getElementById(out).value.split("-")[0]);
        mesic = Number(document.getElementById(out).value.split("-")[1]) - 1;
    }
    kalendar(div, out, rok, mesic);
}
function kalendar(div, out, rok, mesic){
    rok = Number(rok);
    mesic = Number(mesic);
    //vytvoření divu
    if (!document.getElementById(div)) {
        var kalendar = document.createElement("div");
        kalendar.setAttribute("id", div);
        kalendar.setAttribute("class", "cal");
        document.getElementsByTagName("body")[0].appendChild(kalendar);
    }

    //vytvoření obsahu
    var nazvyMesicu = new Array("leden", "únor", "březen", "duben", "květen", "červen", "červenec", "srpen", "září­", "říjen", "listopad", "prosinec");
    var nazvyDnu = new Array("","PO","ÚT","ST","ČT","PÁ","SO","NE");
    var vybraneDatum = new Date(rok,mesic,1);
    var prvniDen = vybraneDatum.getDay();
    var vybraneDatum2 = new Date(rok,mesic+1,0);
    if (prvniDen == 0) {
        prvniDen = 7;
    }
    var pocetDni=vybraneDatum2.getDate();
    var kalendar = "";
    var pocitadlo = 0;
    kalendar += "<table><tr><th><a href='javascript: void(0);' onclick='predRok(\"" + div + "\",\"" + out + "\"," + rok + "," + mesic + ")'>&lt;&lt;</a></th><th colspan='5'>" + rok + "</th><th><a href='javascript: void(0);' onclick='dalRok(\"" + div + "\",\"" + out + "\"," + rok + "," + mesic + ")'>&gt;&gt;</a></th></tr><tr><th><a href='javascript: void(0);' onclick='predMes(\"" + div + "\",\"" + out + "\"," + rok + "," + mesic + ")'>&lt;&lt;</a></th><th colspan='5'>" + nazvyMesicu[mesic] + "</th><th><a href='javascript: void(0);' onclick='dalMes(\"" + div + "\",\"" + out + "\"," + rok + "," + mesic + ")'>&gt;&gt;</a></th></tr><tr>";
    for(i=1;i<=7;i++){
        kalendar += "<th>"+nazvyDnu[i]+"</th>";
    }
    kalendar+="</tr><tr>";
    for(i = 1;i < prvniDen;i++){
        kalendar += "<td class='jine'></td>";
        pocitadlo++;
    }
    for (i = 1;i <= pocetDni;i++){
        if (pocitadlo == 7) {
            kalendar += "</tr><tr>";
            pocitadlo=0;
        }
        kalendar += "<td class='dny' onclick='vyplnDatum(\"" + div + "\",\"" + out + "\"," + rok + "," + mesic + "," + i + "); zavriKalendar(\"" + div + "\",\"" + out + "\")'>" + i + "</td>";
        pocitadlo++;
    }
    for(i=pocitadlo;i<7;i++){
        kalendar += "<td class='jine'></td>";
    }
    kalendar+="</tr></table>";
    
    document.getElementById(div).innerHTML = kalendar;
    if(document.getElementById(div).style.display != "block"){
        otevriKalendar(div,out);
    }
}

function predRok(div, out, rok, mesic){
    rok = rok - 1; 
    kalendar(div, out, rok, mesic);
}

function dalRok(div, out, rok, mesic){
    rok = rok + 1;
    kalendar(div, out, rok, mesic);
}

function predMes(div, out, rok, mesic){
    mesic=mesic-1;
    if (mesic == -1){
        mesic = 11;
        rok = rok - 1;
    }
    kalendar(div, out, rok, mesic);
}

function dalMes(div, out, rok, mesic){
    mesic = mesic + 1;
    if (mesic == 12) {
        mesic = 0;
        rok = rok + 1;
    }
    kalendar(div, out,rok, mesic);
}

function vyplnDatum(div, out, rok, mesic, den) {

    mesic = mesic + 1;

    if (den < 10) {
        den = "0" + den;
    }

    if(mesic < 10){
        mesic = "0" + mesic;
    }
    document.getElementById(out).value = rok + "-" + mesic + "-" + den;
}

function otevriKalendar(div, object) {
    if (document.getElementById(object).parentNode.firstChild.getAttribute("type") == "button") {
        var button = document.getElementById(object).parentNode.firstChild;
    }
    else {
        var button = document.getElementById(object).parentNode.lastChild;
    }
    elClone = button.cloneNode(true);
    button.parentNode.replaceChild(elClone, button);
    elClone.addEventListener("click", function () { zavriKalendar(div, object); });
    elClone.value = "zavři kalendář";
    document.getElementById(div).style.display = "block";
    _move();
    document.getElementById(div).style.left = mysX - document.getElementById(div).offsetWidth/2 + "px";
    document.getElementById(div).style.top = mysY + 30 + "px";
}

function zavriKalendar(div, object) {
    if (document.getElementById(object).parentNode.firstChild.getAttribute("type") == "button") {
        var button = document.getElementById(object).parentNode.firstChild;
    }
    else {
        var button = document.getElementById(object).parentNode.lastChild;
    }
    elClone = button.cloneNode(true);
    button.parentNode.replaceChild(elClone, button)
    elClone.addEventListener("click", function () { otevriKalendar(div,object); });
    elClone.value = "kalendář";
    document.getElementById(div).style.display = "none";
}
//konec kalendar

//pozice divu
var mysX;
var mysY;
function _move(e){
    var d, b;
    if (!e) {
        var e = window.event;
    } //IE mouse event 

    if (e.pageX || e.pageY) //other 
    {
        mysX = e.pageX;
        mysY = e.pageY;
    }
    else if (e.clientX || e.clientY) //IE 
    {
        d = document;
        d = d.documentElement ? d.documentElement : d.body;
        mysX = e.clientX + d.scrollLeft;
        mysY = e.clientY + d.scrollTop;
    }
}