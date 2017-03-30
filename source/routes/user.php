<h3><?php echo $arguements["name"] ?></h3><a href="/<?php echo SITE_ROOT; ?>/logout">Logout</a> 
<br>
<?php 
$db = new mysqli(MYSQL_PROVIDER, MYSQL_USER, MYSQL_PASS, MYSQL_DB);
$db->set_charset("utf8");
$id = $arguements["id"];
$sql = "SELECT * FROM `klandring` WHERE (`from` = $id OR `to` = $id)";
$result = $db->query($sql);

$losings = 0;
$klandring_table = "";

if ($result->num_rows > 0) {
    $klandring_table .= "<table class='table'><thead><tr><th></th><th></th><th></th><th></th><th></th><th></th></tr></thead><tbody>";
    while($row = $result->fetch_assoc()) {
            $klandring_table .= "<tr><td>" . $row["verdictdate"] 
                                . "</td><td>" . $row["title"] 
                                . "</td><td>" . $row["from"] 
                                . "</td><td>" . $row["to"] 
                                . "</td><td>" . $row["verdict"]
                                . "</td><td>" . ($row["paid"] == 1 ? "betalt" : "ikke betalt") . "</td></tr>";
        if ($row["verdict"] == 3) {
            $losings += 5 * (1 - $row["paid"]);
        } else if ($row["to"] == $id) {
            if($row["verdict"] == 1) {
                $losings += 5 * (1 - $row["paid"]);
            }
        } else if($row["from"] == $id) {
            if($row["verdict"] == 2) {
                $losings += 5 * (1 - $row["paid"]);
            }
        }
    }
    $klandring_table .= "</tbody></table>";    
}
?>
<div class="panel panel-default">
    <div class="panel-heading">Klandringer</div>
    <div class="panel-body">
        <p>Du skylder <?php echo $losings ?>kr.</p>
    </div>
    <?php echo $klandring_table; ?>
</div>

<?php
var_dump($arguements);
?>
<br>