<h3><?php echo $arguements["name"] ?></h3><a href="/<?php echo SITE_ROOT; ?>/logout">Logout</a> 
<br>
<?php 
if($_SESSION["auid"] == $arguements["auid"]) 
{ 
    require_once("template/user-edit.php"); 
} 
?>

<?php
var_dump($arguements);
?>
<br>