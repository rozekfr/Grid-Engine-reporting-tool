/**
 * Funkce načítající tabulku statistik.
 * @param object Objekt (sloupec), na který se kliklo pro řazení.
 * @param filtry Určuje, zda-li zachovat filtry nebo ne.
 * @param file Určuje, ze kterého souboru se vezmou data.
 * @param div_id Kam se výsledek vloží.
 */
function update_stats(object, filtry, file, div_id){

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
    httpRequest.open("GET",file+".php?select="+select+"&orderby="+order+"&where="+where+"&polozky="+pocetPolozek+"&stranka="+stranka);
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
 * @param update Pokud je true, aktualizuje tabulku, jinak ne.
 * @param file Určuje, z jakého souboru budou brána data.
 * @param div_id Určuje kam bude nová tabulka vložena.
 */
function check_filters(){
    var filtry = document.getElementsByClassName("filtr_input");
    var seznam = /^[^(<|>|<=|>=|=|!=|\s|,)]+(, ?[^<|>|<=|>=|=|!=|\s|,)]+)*$/;
    var operatory = /^(<|>|<=|>=|=|!=) [^( |AND|OR|<|>|<=|>=|=|!=|'|")]+(( AND | OR )(<|>|<=|>=|=|!=) [^( |AND|OR|<|>|<=|>=|=|!=|'|")]+)*$/;
    var ret = true;
    for(var i = 0; i < filtry.length; i++){
        if(filtry[i].value != "" && ret == true){
            if(operatory.test(filtry[i].value)){
                filtry[i].style.color = "black";
            }
            else if(seznam.test(filtry[i].value)){
                filtry[i].style.color = "black";
            }
            else{
                filtry[i].style.color = "red";
                ret = false;
            }
        }
    }
    return ret;
}