// var unchanged = { ... } // defined in team-admin.php
// var slug = "[url]"      // --||--
var summary = document.getElementById("summary");
var btnUpdate = document.getElementById("btn-update");
var btnSubmit = document.getElementById("btn-submit");

var changes = {};

function getVerdictMsg(verdict) {
    switch (verdict) {
        case 0:
            return "ikke beslutet";
        case 1:
            return "Klandrer vandt";
        case 2:
            return "Klandret vandt";
        case 3:
            return "uafgjort";
        default:
            console.error("Did not get a proper verdict (" + verdict + ") for " + text);
            return "!!!! DER SKETE EN FEJL ABORT !!!";
    }
}

function getPaidMsg(paid) {
    return paid ? "er betalt" : "mangler at betale";
}

function addToChangeList(id, desc) {
    var element = document.createElement("li");
    element.innerText = "Klandring #" + id + ": " + desc;
    summary.appendChild(element);
}

document.getElementById("klandringer").addEventListener("change", function(ev) {
    var a = ev.target.id.split("-", 2);
    var type = a[0];
    var id = a[1];
    var text = "";
    
    if (!changes.hasOwnProperty(id)) {
        changes[id] = {};
    }

    switch (type) {
        case "p":
            changes[id].paid = ev.target.checked ? 1 : 0;
            text += getPaidMsg(ev.target.checked);
            break;

        case "va":
        case "vb":
            var verdict = document.getElementById("va-" + id).checked;
            verdict += document.getElementById("vb-" + id).checked << 1;
            changes[id].verdict = verdict;
            text += getVerdictMsg(verdict);
            break;
    
        default:
            console.error("Did not find an appropriate event type for " + type);
            return;
    }
    btnUpdate.removeAttribute("disabled");
    btnSubmit.setAttribute("disabled", "");
    addToChangeList(id, text);
});

btnSubmit.addEventListener("click", function(ev) {
    if (btnSubmit.hasAttribute("disabled")) { 
        return;
    }

    btnSubmit.setAttribute("disabled", "");
    summary.innerHTML = "";

    for (var id in changes) {
        if (!changes[id].hasOwnProperty("verdict")) {
            changes[id].verdict = unchanged[id].verdict;
        }

        if (!changes[id].hasOwnProperty("paid")) {
            changes[id].paid = unchanged[id].paid;
        }
    }

    var http = new XMLHttpRequest();
    http.open("POST", "/" + slug + "/admin");
    http.setRequestHeader("Content-Type", "application/json");
    http.onreadystatechange = function () {
        if (http.readyState === 4) { // if we have recieved a response
            if (http.status === 200) { // if the response is OK
                location.reload(true);
            } else {
                alert("Failed to update");
                console.error("Unable to post ...");
            }
        }
    };
    http.send(JSON.stringify(changes));

    changes = {};
});

btnUpdate.addEventListener("click", function(ev) {
    if (btnUpdate.hasAttribute("disabled")) { 
        return;
    }

    btnUpdate.setAttribute("disabled", "");

    summary.innerHTML = "";

    var changed = 0; // temp for checking we are actually making any changes.
    for (var id in changes) {
        var maintain = false;
        
        if (changes[id].hasOwnProperty("verdict")) {
            var v = changes[id].verdict;
            if (unchanged[id].verdict !== v) {
                addToChangeList(id, getVerdictMsg(v));
                maintain = true;
                changed++;
            }
        }

        if (changes[id].hasOwnProperty("paid")) {
            var p = changes[id].paid;
            if (unchanged[id].paid !== p) {
                addToChangeList(id, getPaidMsg(p));
                maintain = true;
                changed++;
            }
        }
        
        if (!maintain) {
            delete changes[id];
        }
    }

    if (changed > 0) {
        btnSubmit.removeAttribute("disabled");        
    } else {
        btnSubmit.setAttribute("disabled", "");        
    }
});