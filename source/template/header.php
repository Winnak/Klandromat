<!DOCTYPE html>
<html lang="da">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $header_stuff["title"] ?> &#x1F4B8; Klandromat</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <link rel="stylesheet" href="/style.css">
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.2/html5shiv.js"></script>
  <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
  <![endif]-->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<?php
if(isset($header_stuff["scripts"])) {
    foreach ($header_stuff["scripts"] as $script) {
        echo "<script src='$script'></script>";
    }
}
?>
</head>
<body>
  <nav class="navbar navbar-inverse navbar-static-top">
    <div class="container">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false"
          aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
        <a class="navbar-brand" href="/">Klandr-o-Mat</a>
      </div>
      <div id="navbar" class="navbar-collapse collapse">
        <ul class="nav navbar-nav">
          <?php if (isset($_SESSION["oauth-success"])) : ?>
            <li><a href="/">Oversigt</a></li>
            <li><a href="/<?= $_SESSION["auid"] ?>">Din bruger</a></li>
            <?php foreach ($_SESSION["teams"] as $team) : ?>
            <li><a href="/<?= $team["slug"] ?>"><?= $team["name"] ?></a></li>
            <?php endforeach ?>
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li><a href="/klandring/create">Opret klandring</a></li>
            <li><a href="/logout"><i class="glyphicon glyphicon-log-out"></i> Log ud.</a></li>
          <?php endif; ?>
        </ul>
      </div>
      <!--/.nav-collapse -->
    </div>
  </nav>
  <div class="container body-content">