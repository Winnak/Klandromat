<?php if($arguements["auid"] == $_SESSION["auid"]) : ?>
<?php endif; ?>
<?php
$id = $arguements["id"];
$sql = "SELECT * FROM `klandring` WHERE (verdict != 0)";
$result = $db->query($sql);

$losings = 0;
$klandring_table = "";

if ($result->num_rows > 0) {
    $klandring_table .= "<table class='table table-hover'><thead><tr><th></th><th>Titel</th><th>Klandrer</th><th>Klandret</th><th>Status</th></tr></thead><tbody>";
    while ($row = $result->fetch_assoc()) {
        if ($row["verdict"] === 0 && $row["from"] !== $id) {
            // Hide upcomming klandringer for the accused.
            continue;
        }
        $from = idToName($row["from"],$db);
        $to = idToName($row["to"],$db);

        $klandring_table .= "<tr onclick=\"window.document.location='/" . $arguements["auid"] . "/show/". $row["id"]."'\"><td>" . $row["verdictdate"] . "</td><td>" . $row["title"] . "</td>";
        switch ($row["verdict"]) {
            default:
            case 0:
                $klandring_table .= "<td><div class='tie'>$from</div></td><td><div class='tie'>$to</div></td>";
                break;
            case 1:
                $klandring_table .= "<td><div class='loss'>$ $from</div></td><td><div class='win'>$to</div></td>";
                break;
            case 2:
                $klandring_table .= "<td><div class='win'>$from</div></td><td><div class='loss'>$to $</div></td>";
                break;
            case 3:
                $klandring_table .= "<td><div class='tie'>$ $from</div></td><td><div class='tie'>$to $</div></td>";
                break;
        }

        $klandring_table .= "<td>" . ($row["paid"] == 1 ? "betalt" : "ikke betalt") . "</td></tr>";

        if ($row["verdict"] == 3) {
            $losings += 5 * (1 - $row["paid"]);
        } elseif ($row["to"] == $id) {
            if ($row["verdict"] == 1) {
                $losings += 5 * (1 - $row["paid"]);
            }
        } elseif ($row["from"] == $id) {
            if ($row["verdict"] == 2) {
                $losings += 5 * (1 - $row["paid"]);
            }
        }
    }
    $klandring_table .= "</tbody></table>";
    
    $result->free();
}
?>
<div class="panel panel-default">
    <div class="panel-heading">Klandringshistorik for "LAV HOLD"</div>
    <?php echo $klandring_table; ?>
</div>
<br>