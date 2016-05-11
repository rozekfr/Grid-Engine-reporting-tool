//při načtení stránky vloží grafy
window.onload = function(){
    slots();
    jobs();
}

var loading = "<div class='spinner'><div class='rect1'></div> <div class='rect2'></div> <div class='rect3'></div> <div class='rect4'> </div> <div class='rect5'></div></div>";

var directory = "rrd_databaze";

/**
 * Funce vykreslující graf slotů
 */
function slots(){

    document.getElementById("slots_graph").innerHTML = loading;

    //nastaveni
    var obdobi = ["5 dní (rozlišení 1m)", "3 týdny (rozlišení 5m)", "2 měsíce (rozlišení 30m)", "10 let (rozlišení 2h)"];
    document.getElementById("obdobi").innerHTML = obdobi[document.getElementById("select_obdobi").value];
    var archiv = document.getElementById("select_obdobi").value;
    var vybrane = new Array();
    for(var i = 0; i < document.getElementsByClassName("slots_DS").length; i++) {
        if (document.getElementsByClassName("slots_DS")[i].checked) {
            vybrane.push(document.getElementsByClassName("slots_DS")[i].id);
        }
    }

    //graf
    var ds_graph_opts = {
        'vyuzite': {color: "#006600", stack: 'positive', label: "Využité"},
        'dostupne': {color: "#88FF88", stack: 'positive', label: "Dostupné"}
    };

    var graph_opts = {
        bars: {show: true, align:'center'},
        lines: {show: false},
        tooltip: false
    };

    var rrdflot_defaults = {
        graph_width: "800px",
        graph_height: "300px",
        graph_only: true,
        use_checked_DSs: true,
        checked_DSs: vybrane,
        use_rra: true,
        rra: archiv
    };

    // the rrdFlotAsync object creates and handles the graph
    var f1 = new rrdFlotAsync("slots_graph", directory+"/"+"slots.rrd", null, graph_opts, ds_graph_opts, rrdflot_defaults);
}

/**
 * Funkce vykreslující graf úloh
 */
function jobs() {

    document.getElementById("jobs_graph").innerHTML = loading;

    //nastavení
    var archiv = document.getElementById("select_obdobi").value;
    var vybrane = new Array();
    for(var i = 0; i < document.getElementsByClassName("jobs_DS").length; i++) {
        if (document.getElementsByClassName("jobs_DS")[i].checked) {
            vybrane.push(document.getElementsByClassName("jobs_DS")[i].id);
        }
    }

    //graf
    var ds_graph_opts = {
        'cekajici': {color: "#000066", stack: 'positive', label: "Čekající"},
        'provadene': {color: "#0099FF", stack: 'positive', label: "Prováděné"}
    };

    var graph_opts = {
        bars: {show: true, align:'center'},
        lines: {show: false},
        tooltip: false
    };

    var rrdflot_defaults = {
        graph_width: "800px",
        graph_height: "300px",
        graph_only: true,
        use_checked_DSs: true,
        checked_DSs: vybrane,
        use_rra: true,
        rra: archiv
    };

    // the rrdFlotAsync object creates and handles the graph
    var f1 = new rrdFlotAsync("jobs_graph", directory+"/"+"jobs.rrd", null, graph_opts, ds_graph_opts, rrdflot_defaults);
}