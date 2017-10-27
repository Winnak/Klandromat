
var chkKlandrer = document.getElementById("va");
var chkKlandret = document.getElementById("vb");

function onChange(ev) {
    var verdict = (+chkKlandrer.checked) | ((+chkKlandret.checked) << 1);

    var http = new XMLHttpRequest();
    var params = "verdict=" + verdict;
    http.open("POST", window.location.href, true);
    
    http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    
    // TODO: feedback msg if the operation went unsuccessful.

    http.send(params);
}

chkKlandrer.addEventListener("change", onChange);

chkKlandret.addEventListener("change", onChange);