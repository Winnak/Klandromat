<?php
foreach ($arguements as $key => $value) {
    echo $value . '.<br>';
}

echo "Logged in! <br>Hello ";
echo $_SESSION["auid"] . '.<br>';
echo '<a href="logout">Logout</a>';

?>