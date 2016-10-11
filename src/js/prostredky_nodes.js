window.onload = function(){
    nodes();
}

var loading = "<div class='spinner'><div class='rect1'></div> <div class='rect2'></div> <div class='rect3'></div> <div class='rect4'> </div> <div class='rect5'></div></div>";

var directory = "rrd_databaze";


/**
 * Povoluje nebo zakazuje inputy podle zvoleného grafu
 */
function graf_changer(){
    var graf = (document.getElementById("graf_spojnicovy").checked ? "spojnicovy" : "sloupcovy");
    var spojnicove = document.getElementsByClassName("DS_spoj");
    var sloupcove = document.getElementsByClassName("DS_sloup");
    if(graf == "spojnicovy"){
        for(i = 0; i < sloupcove.length; i++){
            sloupcove[i].setAttribute("disabled","true");
        }
        for(i = 0; i < spojnicove.length; i++) {
            spojnicove[i].removeAttribute("disabled");
        }
    }
    else{
        for(i = 0; i < spojnicove.length; i++){
            spojnicove[i].setAttribute("disabled","true");
        }
        for(i = 0; i < sloupcove.length; i++) {
            sloupcove[i].removeAttribute("disabled");
        }
    }
}

/**
 * Funkce vykreslující všechny grafy.
 */
function nodes(){
    var uzly = document.getElementsByClassName("nodes");
    var output = "";
    var vybrane_uzly = new Array();
    for(var i = 0; i < uzly.length; i++) {
        if (uzly[i].checked){
            output += "<div id='"+uzly[i].id+"' class='node_graph'>";
                output += "<h2>"+uzly[i].id+"</h2>";
                output += "<a href='javascript:void(0);' class='konfigurace' alt='konfigurace' onclick='zobrazKonfiguraci(\""+uzly[i].id+"\")'></a>";
                output += "<input type='button' value='reset zoom' onclick='do_graph(\""+uzly[i].id+"\");'>";
                output += "<div id='"+uzly[i].id+"_graph'>";
                output += loading;
                output += "</div>";
            output += "</div>";
            vybrane_uzly.push(uzly[i].id);
        }
    }

    document.getElementById("text").innerHTML = output;

    for(var i = 0; i < vybrane_uzly.length; i++){
        do_graph(vybrane_uzly[i]);
    }
}

function do_graph(id){
    //nastaveni

    document.getElementById(id+"_graph").innerHTML = loading;

    var obdobi = ["5 dní (rozlišení 1min)", "3 týdny (rozlišení 5min)", "2 měsíce (rozlišení 30min)", "10 let (rozlišení 2h)"];
    document.getElementById("obdobi").innerHTML = obdobi[document.getElementById("select_obdobi").value];
    var archiv = document.getElementById("select_obdobi").value;

    var rady;
    var i;
    var vybrane_rady = [];

    var graf = (document.getElementById("graf_spojnicovy").checked ? "spojnicovy" : "sloupcovy");

    if(graf == "spojnicovy") {
        rady = document.getElementsByClassName("DS_spoj");
        for (i = 0; i < rady.length; i++) {
            if (rady[i].checked) {
                vybrane_rady.push(rady[i].id);
            }
        }
    }
    else{
        rady = document.getElementsByClassName("DS_sloup");
        for (i = 0; i < rady.length; i++) {
            if (rady[i].checked) {
                if(rady[i].id == "gpu"){
                    vybrane_rady.push("gpu_used");
                    vybrane_rady.push("gpu_total");
                }
                else {
                    vybrane_rady.push("used_slots");
                    vybrane_rady.push("free_slots");
                }
            }
        }
    }

    //graf
    var ds_graph_opts = {
        'gpu_total': {color: "#9986EC", stack: 'positive', label: "dostupné GPU"},
        'gpu_used': {color: "#550E8E", stack: 'positive', label: "využité GPU"},
        'used_slots': {color: "#006600", stack: 'positive', label: "Využité sloty"},
        'free_slots': {color: "#88FF88", stack: 'positive', label: "Dostupné sloty"},
        'cpu': {lines: {fill: true}, color: "#C00919", label: "CPU [%]"},
        'mem_used': {lines: {lineWidth: 0,fill: true}, color:"#42A1DB", label: "Využitá paměť [GB]"},
        'mem_total': {lines: {fill: false}, color:"#42A1DB", label: "Celková paměť [GB]"},
        'disk_free': {lines: {fill: true}, color: "#D7FB07", label: "Volný disk [GB]"}
    };

    var graph_opts;
    if(graf == "spojnicovy"){
        graph_opts = {
            legend: {noColumns: 3},
            lines: {show: true},
            bars: {show: false},
            tooltip: false
        };
    }
    else{
        graph_opts = {
            legend: {noColumns: 3},
            lines: {show: false},
            bars: {show: true},
            tooltip: false
        };
    }

    var rrdflot_defaults = {
        graph_width: "400px",
        graph_height: "200px",
        graph_only: true,
        use_checked_DSs: true,
        checked_DSs: vybrane_rady,
        use_rra: true,
        rra: archiv
    };

    // the rrdFlotAsync object creates and handles the graph
    var f1 = new rrdFlotAsync(id+"_graph", directory+"/"+id+".rrd", null, graph_opts, ds_graph_opts, rrdflot_defaults);
}

/**
 * Zobrazuje informace o konfiguraci k danému uzlu.
 * @param uzel Uzel, u kterého se zjišťuje konfigurace.
 */
function zobrazKonfiguraci(uzel){
    document.getElementById("konfigurace").innerHTML = "<h2>Konfigurace uzlu "+uzel+"</h2>";
    document.getElementById("konfigurace").innerHTML += "<a href='javascript:void(0)' class='zavri' onclick='zavriKonfiguraci()'>X</a>";
    document.getElementById("konfigurace").innerHTML += "<p>Načítám informace...</p>"+loading;
    document.getElementById("black").style.display = "block";
    document.getElementById("konfigurace").style.display = "block";
    centerKonfigurace();

    //ajax
    var httpRequest;
    if(window.ActiveXObject) {
        httpRequest = new ActiveXObject("Microsoft.XMLHTTP");
    }  // IE
    else if(window.XMLHttpRequest) {
        httpRequest = new XMLHttpRequest();
    } // ostatní prohlížeče
    httpRequest.open("GET","konfigurace_ajax.php?uzel="+uzel);
    httpRequest.onreadystatechange = function(){   // po načtení pokračujeme na FCI
        if(httpRequest.readyState==4 && httpRequest.status==200) {
            document.getElementById("konfigurace").innerHTML = "<a href='javascript:void(0)' class='zavri' onclick='zavriKonfiguraci()'>X</a>";
            document.getElementById("konfigurace").innerHTML += httpRequest.responseText;
            centerKonfigurace();
        }
    }
    httpRequest.send(null);
}

/**
 * Centruje div konfigurace.
 */
function centerKonfigurace(){
    var konfigurace = document.getElementById("konfigurace");
    var sirkaKonfigurace = konfigurace.offsetWidth + 4;
    var vyskaKonfigurace = konfigurace.offsetHeight + 4;

    konfigurace.style.marginLeft = (window.innerWidth - sirkaKonfigurace)/2 + "px";
    konfigurace.style.marginTop = (window.innerHeight - vyskaKonfigurace)/2 + "px";
}

function zavriKonfiguraci(){
    var konfigurace = document.getElementById("konfigurace");
    var cerno = document.getElementById("black");

    konfigurace.style.display = "none";
    cerno.style.display = "none";
}