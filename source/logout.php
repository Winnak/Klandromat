<?php
// Super kill combo
session_start();
session_unset(); 
session_destroy(); 

header("Location: /");
?>