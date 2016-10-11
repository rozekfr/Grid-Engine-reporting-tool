/**
 * Funkce načítající tabulku statistik.
 * @param object Objekt (sloupec), na který se kliklo pro řazení.
 * @param filtry Určuje, zda-li zachovat filtry nebo ne.
 * @param file Určuje, ze kterého souboru se vezmou data.
 * @param div_id Kam se výsledek vloží.
 */
function update_stats(object, filtry, file, div_id){

    //databáze
    var db = "";
    if(document.getElementById("select_obdobi")) {
        db = document.getElementById("select_obdobi").value;
    }

    //obdobi
    var odkdy = "";
    var dokdy = "";
    if(document.getElementById("datum_od") && document.getElementById("datum_do")){
        if(document.getElementById("datum_od").value != "" && document.getElementById("datum_do").value != ""){
            odkdy = document.getElementById("datum_od").value + " " + document.getElementById("time_od").value;
            dokdy = document.getElementById("datum_do").value + " " + document.getElementById("time_do").value;
            db = "";
        }
    }

    //LIMIT
    var pocetPolozek = 10;
    if(document.getElementById("pocet_polozek")){
        pocetPolozek = document.getElementById("pocet_polozek").value;
    }
    var stranka = 1;
    if(document.getElementById("strankovani") && object != null && object.id == "strankovani"){
        stranka = document.getElementById("strankovani").value;
    }

    //ORDER BY
    var order = document.getElementsByClassName("columns")[0].id + " ASC";
    if(object && object.id != "strankovani"){
        order = object.id;
        var src = object.src.split("/");
        var nazev = src[src.length - 1];
        var how = "ASC";
        if(nazev == "sestupne.png"){
            how = "ASC";
        }
        else if(nazev == "vzestupne.png"){
            how = "DESC";
        }

        order += " "+how;
    }
    else{
        var razeni = document.getElementsByClassName("razeni");
        if(razeni.length != 0){
            for(var i = 0; i < razeni.length; i++){
                var src = razeni[i].src.split("/");
                var how = src[src.length - 1].split(".")[0];
                if(how != "neradit"){
                    order = razeni[i].id;
                    if(how == "vzestupne"){
                        order += " ASC";
                    }
                    else{
                        order += " DESC";
                    }
                }
            }
        }
    }

    //WHERE
    if(filtry){
        if(!check_filters()){
            return false;
        }
        var filtry = document.getElementsByClassName("filtr_input");
        where = "";
        for(i = 0; i < filtry.length; i++){
            tmp = filtry[i].id.split("filtr_");
            nazev = tmp[1];
            where += nazev + ":" + filtry[i].value + "|";
        }

        if(where == ""){
            where = "1";
        }
        else{
            where = where.slice(0, -1);
        }
    }
    else{
        where = "1";
    }

    //select
    var columns = document.getElementsByClassName("columns");
    var select = "";
    for(i = 0; i < columns.length; i++){
        if(columns[i].checked){
            select += columns[i].id+",";
        }
    }

    select = select.slice(0, -1);

    //načítání
    document.getElementById(div_id).innerHTML = "<tr><td>Načítám statistiky...</td><tr><td>"+loading+"</td></tr>";

    //ajax
    if(window.ActiveXObject) {
        var httpRequest = new ActiveXObject("Microsoft.XMLHTTP");
    }  // IE
    else if(window.XMLHttpRequest) {
        var httpRequest = new XMLHttpRequest();
    } // ostatní prohlížeče
    httpRequest.open("GET",file+".php?db="+db+"&od="+odkdy+"&do="+dokdy+"&select="+select+"&orderby="+order+"&where="+where+"&polozky="+pocetPolozek+"&stranka="+stranka);
    httpRequest.onreadystatechange = function(){   // po načtení pokračujeme na FCI
        if(httpRequest.readyState==4 && httpRequest.status==200) {
            var request = httpRequest.responseText;
            document.getElementById(div_id).innerHTML = request;
        }
    }
    httpRequest.send(null);
}

/**
 * Funkce kontrolující správné zadání filtrů.
 */
function check_filters(){
    var filtry = document.getElementsByClassName("filtr_input");
    var seznam = /^[^(<|>|<=|>=|=|!=|\s|,)]+(, ?[^<|>|<=|>=|=|!=|\s|,)]+)*$/;
    var operatory = /^(<|>|<=|>=|=|!=) [^( |AND|OR|<|>|<=|>=|=|!=|'|")]+(( AND | OR )(<|>|<=|>=|=|!=) [^( |AND|OR|<|>|<=|>=|=|!=|'|")]+)*$/;
    var ret = true;
    for(var i = 0; i < filtry.length; i++){
        if(filtry[i].value != "" && ret == true){
            if(filtry[i].id == "filtr_skupina"){
                var seznam_skupin = /^[^(<|>|<=|>=|=|!=|,)]+(, ?[^<|>|<=|>=|=|!=|,)]+)*$/;
                if(seznam_skupin.test(filtry[i].value)){
                    filtry[i].style.color = "black";
                }
                else{
                    filtry[i].style.color = "red";
                    ret = false;
                }
            }
            else {
                if (operatory.test(filtry[i].value)) {
                    filtry[i].style.color = "black";
                }
                else if (seznam.test(filtry[i].value)) {
                    filtry[i].style.color = "black";
                }
                else {
                    filtry[i].style.color = "red";
                    ret = false;
                }
            }
        }
    }
    return ret;
}

/**
 * Vloží období do informačního panelu.
 */
function put_obdobi(odkdy, dokdy){
    var obdobi = document.getElementById("obdobi");
    var last = document.getElementById("last");
    var lastupdate = document.getElementById("last_update").value;

    if(odkdy && dokdy){
        obdobi.innerHTML = odkdy + " - " + dokdy;
    }
    else {
        var selectObdobi = document.getElementById("select_obdobi").value;
        var obdobiText = "";

        switch (selectObdobi) {
            case 'sge_rt_stats_ulohy':
                obdobiText = "všechna dostupná data";
                break;

            case 'sge_rt_stats_ulohy_posledni_tyden':
                obdobiText = "poslední dostupný týden";
                break;

            case 'sge_rt_stats_ulohy_posledni_mesic':
                obdobiText = "poslední dostupný měsíc";
                break;


            case 'sge_rt_stats_uzivatele':
                obdobiText = "všechna dostupná data";
                break;

            case 'sge_rt_stats_uzivatele_posledni_tyden':
                obdobiText = "poslední dostupný týden";
                break;

            case 'sge_rt_stats_uzivatele_posledni_mesic':
                obdobiText = "poslední dostupný měsíc";
                break;

            default:
                obdobiText = selectObdobi.replace("_", " ");
                break;
        }
        obdobi.innerHTML = obdobiText;
    }
    last.innerHTML = lastupdate;
}

/**
 * Zjistí poslední datum aktualizace tabulky.
 */
function obdobi_ajax(){
    var db;
    if(document.getElementById("datum_od").value != "" && document.getElementById("datum_do").value != ""){
        db = "sge_rt_stats_ulohy";
        var odkdy = document.getElementById("datum_od").value + " " + document.getElementById("time_od").value;
        var dokdy = document.getElementById("datum_do").value + " " + document.getElementById("time_do").value;
    }
    else{
        db = document.getElementById("select_obdobi").value;
    }
    //ajax
    if(window.ActiveXObject) {
        var httpRequest = new ActiveXObject("Microsoft.XMLHTTP");
    }  // IE
    else if(window.XMLHttpRequest) {
        var httpRequest = new XMLHttpRequest();
    } // ostatní prohlížeče
    httpRequest.open("GET","last_update_ajax.php?db="+db);
    httpRequest.onreadystatechange = function(){   // po načtení pokračujeme na FCI
        if(httpRequest.readyState==4 && httpRequest.status==200) {
            var request = httpRequest.responseText;
            document.getElementById("last_update").value = request;
            put_obdobi(odkdy,dokdy);
        }
    }
    httpRequest.send(null);
}

/**
 * Vymaže inputy jiného období.
 */
function clear_obdobi(){
    document.getElementById("time_od").value = "00:00";
    document.getElementById("time_do").value = "00:00";
    document.getElementById("datum_od").value = "";
    document.getElementById("datum_do").value = "";
}