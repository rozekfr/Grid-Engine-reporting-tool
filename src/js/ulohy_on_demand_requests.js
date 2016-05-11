//načítání
var loading = "<div class='spinner'><div class='rect1'></div> <div class='rect2'></div> <div class='rect3'></div> <div class='rect4'> </div> <div class='rect5'></div></div>";

var timeout;
var timeout1;

/**
 * Podá žádost o zjištění stavu úlohy.
 * @param id_ulohy
 */
function get_info_request(id_ulohy){
    document.getElementById("info_header").innerHTML = "<h2>Informace o úloze "+id_ulohy+"</h2>";
    document.getElementById("info_content").innerHTML = "<p>Načítám informace, to může trvat až 15 sekund...</p>"+loading;
    document.getElementById("black").style.display = "block";
    document.getElementById("info").style.display = "block";

    center_info();

    //ajax
    if(window.ActiveXObject) {
        var httpRequest = new ActiveXObject("Microsoft.XMLHTTP");
    }  // IE
    else if(window.XMLHttpRequest) {
        var httpRequest = new XMLHttpRequest();
    } // ostatní prohlížeče
    httpRequest.open("GET","get_job_info_request.php?id="+id_ulohy);
    httpRequest.onreadystatechange = function(){   // po načtení pokračujeme na FCI
        if(httpRequest.readyState==4 && httpRequest.status==200) {
            get_info_response(id_ulohy);
        }
    }
    httpRequest.send(null);
}

/**
 * Vloží odpověď na žádost o důvod čekání do divu.
 * @param id_ulohy
 */
function get_info_response(id_ulohy){
    //ajax
    if(window.ActiveXObject) {
        var httpRequest = new ActiveXObject("Microsoft.XMLHTTP");
    }  // IE
    else if(window.XMLHttpRequest) {
        var httpRequest = new XMLHttpRequest();
    } // ostatní prohlížeče
    httpRequest.open("GET","get_job_info_response.php?id="+id_ulohy);
    httpRequest.onreadystatechange = function(){   // po načtení pokračujeme na FCI
        if(httpRequest.readyState==4 && httpRequest.status==200) {
            var response = httpRequest.responseText;
            if(response === ""){
                timeout1 = setTimeout(function () {
                    get_info_response(id_ulohy);
                }, 1000);
            }
            else{
                document.getElementById("info_content").innerHTML = response;
                clearTimeout(timeout1);
            }
        }
    }
    httpRequest.send(null);
}

/**
 * Podá žádost o zjištění blokujících úloh z resource listu.
 */
function resource_list_request(){
    var resource_list = document.getElementById("resource_list").value;
    document.getElementById("jobs_stats").innerHTML = "<p>Načítám informace, to může trvat až 15 sekund...</p>"+loading;
    //ajax
    if(window.ActiveXObject) {
        var httpRequest = new ActiveXObject("Microsoft.XMLHTTP");
    }  // IE
    else if(window.XMLHttpRequest) {
        var httpRequest = new XMLHttpRequest();
    } // ostatní prohlížeče
    httpRequest.open("GET","resource_list_request.php?rl="+resource_list);
    httpRequest.onreadystatechange = function(){   // po načtení pokračujeme na FCI
        if(httpRequest.readyState==4 && httpRequest.status==200) {
            var response = httpRequest.responseText;
            resource_list_response(response);
        }
    }
    httpRequest.send(null);
}

/**
 * Vloží výsledek žádosti o blokující úlohy do divu.
 * @param file Soubor, kterého se čtou data.
 */
function resource_list_response(file){
    //ajax
    if(window.ActiveXObject) {
        var httpRequest = new ActiveXObject("Microsoft.XMLHTTP");
    }  // IE
    else if(window.XMLHttpRequest) {
        var httpRequest = new XMLHttpRequest();
    } // ostatní prohlížeče
    httpRequest.open("GET","resource_list_response.php?file="+file);
    httpRequest.onreadystatechange = function(){   // po načtení pokračujeme na FCI
        if(httpRequest.readyState==4 && httpRequest.status==200) {
            var response = httpRequest.responseText;
            if(response === ""){
                timeout = setTimeout(function () {
                    resource_list_response(file);
                }, 1000);
            }
            else{
                document.getElementById("jobs_stats").innerHTML = response;
                clearTimeout(timeout);
            }
        }
    }
    httpRequest.send(null);
}