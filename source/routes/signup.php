Gendkendte ikke <?php 
if (isset($arguements["auid"])) {
    echo $arguements["auid"];
} else {
    echo "auidet";
}
?>
<br>
<br>
Hvis du mener dette er en fejl, så kan du komme til en holdets time og få det rettet.
<br>
<br><a href="/<?php 
if (isset($arguements["auid"])) {
    echo "/" . $_SESSION["auid"];
    echo "/\">Tilbage til din egen side</a>";
} else {
    echo "/logout\">Log ud</a>";
}
?>