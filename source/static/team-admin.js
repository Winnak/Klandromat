document.getElementById("summary");
document.getElementById("btn-update");
document.getElementById("btn-submit");
var test = document.getElementById("klandringer");

var changes = {};

test.addEventListener("change", function(ev) {
    var a = ev.target.id.split("-", 2);
    var type = a[0];
    var id = a[1];
    switch (type) {
        case "p":
        case "va":
        case "vb":
            console.log("hello world");
            break;
    
        default:
            console.warn("Did not find an appropriate event type for " + type);
            return;
    }
});