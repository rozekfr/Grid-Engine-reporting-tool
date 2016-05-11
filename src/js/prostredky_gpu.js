//při načtení stránky vloží graf
window.onload = function(){
    gpus();
}

var loading = "<div class='spinner'><div class='rect1'></div> <div class='rect2'></div> <div class='rect3'></div> <div class='rect4'> </div> <div class='rect5'></div></div>";

var directory = "rrd_databaze";
/**
 * Funce vykreslující graf dostupnosti grafických karet
 */
function gpus(){

    document.getElementById("gpus_graph").innerHTML = loading;

    //nastaveni
    var obdobi = ["5 dní (rozlišení 1min)", "3 týdny (rozlišení 5min)", "2 měsíce (rozlišení 30min)", "10 let (rozlišení 2h)"];
    document.getElementById("obdobi").innerHTML = obdobi[document.getElementById("select_obdobi").value];
    var archiv = document.getElementById("select_obdobi").value;
    var vybrane = new Array();
    for(var i = 0; i < document.getElementsByClassName("gpu_DS").length; i++) {
        if (document.getElementsByClassName("gpu_DS")[i].checked) {
            vybrane.push(document.getElementsByClassName("gpu_DS")[i].id);
        }
    }

    //graf
    var ds_graph_opts = {
        'obsazene': {color: "#550E8E", stack: 'positive', label: "Obsazené"},
        'dostupne': {color: "#9986EC", stack: 'positive', label: "Dostupné"}

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
    var f1 = new rrdFlotAsync("gpus_graph", directory+"/"+"GPUs.rrd", null, graph_opts, ds_graph_opts, rrdflot_defaults);
}