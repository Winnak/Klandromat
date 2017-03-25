<?php
// Super kill combo
session_unset(); 
session_destroy(); 

header("Location: /" . SITE_ROOT . "/" );
?> 