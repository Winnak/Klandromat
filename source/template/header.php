<!DOCTYPE html>
<html lang="da">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo $header_stuff["title"] ?></title>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link rel="stylesheet" href="/style.css">
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.2/html5shiv.js"></script>
<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<?php
if(isset($header_stuff["scripts"]))
{
    foreach ($header_stuff["scripts"] as $index => $script) {
        echo "<script src='$script'></script>";
    }
}

function idToName($id, $db) {
    $sql2 = "SELECT name FROM student WHERE id = " . $id . " LIMIT 1";
        $result2 = $db->query($sql2);
        if ($result2) {
            $row2 = $result2->fetch_assoc();
            return $row2["name"];
        } else {
            echo 'fuck';
        }
    }

?>
</head>
<body>
	<nav class="navbar navbar-inverse navbar-static-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="/">Klandr-o-Mat</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
          <?php 
          if (isset($_SESSION["oauth-success"])) { 
          	echo '
            <li><a href="/">Oversigt</a></li>
            <li><a href="/' . $_SESSION["auid"] . '/info">Din bruger</a></li>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dit hold<span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li><a href="/' . $_SESSION["auid"] . '/teamhistory">Vis historik</a></li>
                <li role="separator" class="divider"></li>
                <li class="dropdown-header">Administrér</li>
                <li><a href="#">Afgør klandringer</a></li>  <!-- KUN HVIS ADMIN -->
              </ul>
            </li>';}?>
          </ul>
          <?php 
          if (isset($_SESSION["oauth-success"])) { 
          	echo '
          <ul class="nav navbar-nav navbar-right">
                      <li><a href="/' . $_SESSION["auid"] . '/create">Opret klandring</a></li>
                      <li><a href="/logout"><i class="glyphicon glyphicon-log-out"></i> Log ud.</a></li>
          </ul>';}?>
        </div><!--/.nav-collapse -->
      </div>
    </nav>
<div class="container body-content">