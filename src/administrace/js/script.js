function login(){
  document.getElementById("login").style.marginTop = (window.innerHeight - document.getElementById("login").offsetHeight)/2+"px";
}
function zobrazUpravu(){
  document.getElementById("black").style.display = "block";
  document.getElementById("upravy").style.display = "block";
  document.getElementById("upravy").style.top = (window.innerHeight-document.getElementById("upravy").offsetHeight)/2+"px";
  document.getElementById("upravy").style.left = (window.innerWidth-document.getElementById("upravy").offsetWidth)/2+"px";
}
function zavriUpravu(){
  document.getElementById("upravy").style.display = "none";
  document.getElementById("black").style.display = "none";
}
