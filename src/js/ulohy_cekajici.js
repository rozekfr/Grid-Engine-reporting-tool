//spustí události při načtení okna
window.onload = function(){
    update_stats(null,0,"ulohy_cekajici_ajax","jobs_stats");
    document.getElementById("obdobi").innerHTML = "aktuální";
};

//načítání
var loading = "<div class='spinner'><div class='rect1'></div> <div class='rect2'></div> <div class='rect3'></div> <div class='rect4'> </div> <div class='rect5'></div></div>";