<!DOCTYPE html>
<head>
	<meta charset='UTF-8'/>
	<title>Szachy</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="favicon.ico" rel="icon" type="image/x-icon"/>
	
	<!-- jQuery -->
	<script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
	
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

	<!-- Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
	
	<!-- Main CSS -->
	<link rel="stylesheet" href="css/style.css">
	
	<!-- Main JS -->
	<script src="js/utilities.js"></script>
</head>

<body>
    <nav id="nav" class="navbar navbar-inverse navbar-fixed-top" alt="nawigacja">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Nawigacja</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">Nawigacja</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
			<?php
				if (!isset($_SESSION['userid'])) {
			?>
				<li id=page0><a href="http://<?php echo $site; ?>" alt="strona glowna">Strona główna</a></li>
				<li id=page1><a href="http://<?php echo $site; ?>?tab=browse" alt="ogladaj mecze">Oglądaj mecze</a></li>
				<li id=page2><a href="http://<?php echo $site; ?>?tab=login" alt="logowanie">Logowanie</a></li>
				<li id=page3><a href="http://<?php echo $site; ?>?tab=register" alt="rejestracja">Rejestracja</a></li>
			<?php
				} else {
			?>
				<li id=page0><a href="http://<?php echo $site; ?>" alt="strona glowna">Strona główna</a></li>
				<li id=page1><a href="http://<?php echo $site; ?>?tab=browse" alt="zagraj lub ogladaj">Zagraj lub oglądaj</a></li>
				<li id=page2><a href="#" alt="twoj login">Zalogowany jako: <strong><?php echo $_SESSION['username']; ?></strong></a></li>
				<li id=page3><a href="http://<?php echo $site; ?>?tab=login&logout=1" alt="wyloguj">Wyloguj</a></li>
			<?php
				}
			?>
          </ul>
        </div>
      </div>
    </nav>