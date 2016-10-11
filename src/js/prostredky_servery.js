//při načtení stránky vloží grafy
window.onload = function(){
    matyldy();
    scratche();
}

var loading = "<div class='spinner'><div class='rect1'></div> <div class='rect2'></div> <div class='rect3'></div> <div class='rect4'> </div> <div class='rect5'></div></div>";

var directory = "rrd_databaze";

/**
 * Funce vykreslující graf matyld
 */
function matyldy(){

    document.getElementById("matyldy_graph").innerHTML = loading;

    //nastaveni
    var obdobi = ["5 dní (rozlišení 1m)", "3 týdny (rozlišení 5m)", "2 měsíce (rozlišení 30m)", "10 let (rozlišení 2h)"];
    document.getElementById("obdobi").innerHTML = obdobi[document.getElementById("select_obdobi").value];
    var archiv = Number(document.getElementById("select_obdobi").value);
    var vybrane = new Array();
    var soubory = new Array();
    for(var i = 0; i < document.getElementsByClassName("matyldy_AVG").length; i++) {
        if (document.getElementsByClassName("matyldy_AVG")[i].checked) {
            vybrane.push(document.getElementsByClassName("matyldy_AVG")[i].id);
            tmp = new Array();
            tmp.push(document.getElementsByClassName("matyldy_AVG")[i].id);
            tmp.push(directory+"/"+document.getElementsByClassName("matyldy_AVG")[i].id+".rrd");
            soubory.push(tmp);
        }
    }
    for(var i = 0; i < document.getElementsByClassName("matyldy_MAX").length; i++) {
        if (document.getElementsByClassName("matyldy_MAX")[i].checked) {
            vybrane.push(document.getElementsByClassName("matyldy_MAX")[i].id);
            tmp = new Array();
            tmp.push(document.getElementsByClassName("matyldy_MAX")[i].id);
            tmp.push(directory+"/"+document.getElementsByClassName("matyldy_MAX")[i].id+".rrd");
            soubory.push(tmp);
        }
    }

    //graf
    var graph_opts = {
        legend: {noColumns: 6},
        lines: {show: true},
        tooltip: false
    };

    var rrd_graph_options = {
        'matylda1':{lines: {show: true, lineWidth: 0, fill: true, zero: true}, stack:'none', color: '#B1120B'},
        'matylda2':{lines: {show: true, lineWidth: 0, fill: true, zero: true}, stack:'none', color: '#5E6278'},
        'matylda3':{lines: {show: true, lineWidth: 0, fill: true, zero: true}, stack:'none', color: '#E3E030'},
        'matylda4':{lines: {show: true, lineWidth: 0, fill: true, zero: true}, stack:'none', color: '#160AB1'},
        'matylda5':{lines: {show: true, lineWidth: 0, fill: true, zero: true}, stack:'none', color: '#0BB1DD'},
        'matylda6':{lines: {show: true, lineWidth: 0, fill: true, zero: true}, stack:'none', color: '#6BF128'},
        'matylda1_MAX':{lines: {show: true, lineWidth: 2, fill: false, zero: true}, stack:'none', color: '#B1120B'},
        'matylda2_MAX':{lines: {show: true, lineWidth: 2, fill: false, zero: true}, stack:'none', color: '#5E6278'},
        'matylda3_MAX':{lines: {show: true, lineWidth: 2, fill: false, zero: true}, stack:'none', color: '#E3E030'},
        'matylda4_MAX':{lines: {show: true, lineWidth: 2, fill: false, zero: true}, stack:'none', color: '#160AB1'},
        'matylda5_MAX':{lines: {show: true, lineWidth: 2, fill: false, zero: true}, stack:'none', color: '#0BB1DD'},
        'matylda6_MAX':{lines: {show: true, lineWidth: 2, fill: false, zero: true}, stack:'none', color: '#6BF128'}
    };

    var rrdflot_defaults = {
        graph_width: "800px",
        graph_height: "300px",
        graph_only: true,
        use_checked_RRDs: true,
        checked_RRDs: vybrane,
        use_rra: true,
        rra: archiv
    };

    // the rrdFlotAsync object creates and handles the graph
    var f1 = new rrdFlotMatrixAsync("matyldy_graph", soubory, null, null, graph_opts, rrd_graph_options, rrdflot_defaults);
}

/**
 * Funce vykreslující graf scratche
 */
function scratche(){

    document.getElementById("scratche_graph").innerHTML = loading;

    //nastaveni
    var obdobi = ["5 dní (rozlišení 1min)", "3 týdny (rozlišení 5min)", "2 měsíce (rozlišení 30min)", "10 let (rozlišení 2h)"];
    document.getElementById("obdobi").innerHTML = obdobi[document.getElementById("select_obdobi").value];
    var archiv = Number(document.getElementById("select_obdobi").value);
    var vybrane = new Array();
    var soubory = new Array();
    for(var i = 0; i < document.getElementsByClassName("scratche_AVG").length; i++) {
        if (document.getElementsByClassName("scratche_AVG")[i].checked) {
            vybrane.push(document.getElementsByClassName("scratche_AVG")[i].id);
            tmp = new Array();
            tmp.push(document.getElementsByClassName("scratche_AVG")[i].id);
            tmp.push(directory+"/"+document.getElementsByClassName("scratche_AVG")[i].id+".rrd");
            soubory.push(tmp);
        }
    }
    for(var i = 0; i < document.getElementsByClassName("scratche_MAX").length; i++) {
        if (document.getElementsByClassName("scratche_MAX")[i].checked) {
            vybrane.push(document.getElementsByClassName("scratche_MAX")[i].id);
            tmp = new Array();
            tmp.push(document.getElementsByClassName("scratche_MAX")[i].id);
            tmp.push(directory+"/"+document.getElementsByClassName("scratche_MAX")[i].id+".rrd");
            soubory.push(tmp);
        }
    }

    //graf
    var graph_opts = {
        legend: {noColumns: 6},
        lines: {show: true},
        tooltip: false
    };

    var rrd_graph_options = {
        'scratch1':{lines: {show: true, lineWidth: 0, fill: true, zero: true}, stack:'none', color: '#B1120B'},
        'scratch2':{lines: {show: true, lineWidth: 0, fill: true, zero: true}, stack:'none', color: '#5E6278'},
        'scratch3':{lines: {show: true, lineWidth: 0, fill: true, zero: true}, stack:'none', color: '#E3E030'},
        'scratch4':{lines: {show: true, lineWidth: 0, fill: true, zero: true}, stack:'none', color: '#160AB1'},
        'scratch5':{lines: {show: true, lineWidth: 0, fill: true, zero: true}, stack:'none', color: '#0BB1DD'},
        'scratch6':{lines: {show: true, lineWidth: 0, fill: true, zero: true}, stack:'none', color: '#6BF128'},
        'scratch1_MAX':{lines: {show: true, lineWidth: 2, fill: false, zero: true}, stack:'none', color: '#B1120B'},
        'scratch2_MAX':{lines: {show: true, lineWidth: 2, fill: false, zero: true}, stack:'none', color: '#5E6278'},
        'scratch3_MAX':{lines: {show: true, lineWidth: 2, fill: false, zero: true}, stack:'none', color: '#E3E030'},
        'scratch4_MAX':{lines: {show: true, lineWidth: 2, fill: false, zero: true}, stack:'none', color: '#160AB1'},
        'scratch5_MAX':{lines: {show: true, lineWidth: 2, fill: false, zero: true}, stack:'none', color: '#0BB1DD'},
        'scratch6_MAX':{lines: {show: true, lineWidth: 2, fill: false, zero: true}, stack:'none', color: '#6BF128'}
    };

    var rrdflot_defaults = {
        graph_width: "800px",
        graph_height: "300px",
        graph_only: true,
        use_checked_RRDs: true,
        checked_RRDs: vybrane,
        use_rra: true,
        rra: archiv
    };

    // the rrdFlotAsync object creates and handles the graph
    var f1 = new rrdFlotMatrixAsync("scratche_graph", soubory, null, null, graph_opts, rrd_graph_options, rrdflot_defaults);
}

/**
 * Vybírá nebo odebírá všechny řady ve sloupci MAX nebo AVG u matyld a scratchů
 * @param object Objekt, který událost vyvolal.
 */
function global_resource_selector(object){
    var series = document.getElementsByClassName(object.id);
    if(object.checked){
        for(i = 0; i < series.length; i++){
            series[i].checked = true;
        }
    }
    else{
        for(i = 0; i < series.length; i++){
            series[i].checked = false;
        }
    }
}