<?php
defined('DIR') or die;


?>
<!DOCTYPE HTML>
<html>
	<head>
	    <meta charset="UTF-8">
		<title><?php Common::secureEcho(Config::TITLE_SHORT);?>: Anmeldung</title>
		<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css">
	</head>
	<body style="padding:1em;">
		<!-- Heading -->
		<div>
			<?php // Check if somethin went wrong.
			switch (Common::getRequest('errorType')): 
			case 'auth'?>
				<p><b>Ungültige Anmeldedaten.</b></p>
			<?php break;?>
			<?php case 'config': ?>
				<p><b>Benutzer muss eingerichtet werden. Dazu bitte einmalig <a target="_blank" href="index.php?view=touch">hier</a> über ein unterstütztes Endgerät einloggen oder die Web-Administration(<?php Common::secureEcho(Config::CONTACT);?>) der Schule kontaktieren.</b></p>
			<?php endswitch;?>
			<!--  <div class="page-header">
				  <h1><?php Common::secureEcho(Config::TITLE_SHORT);?>: Anmeldung</h1>
			</div>-->
		</div>
		
		<!-- Content -->
		<div style="margin-left:auto; margin-right:auto; max-width:500px;">
			<img style="width: auto;height: 120px;display: block; margin-left: auto; margin-right: auto; margin-bottom: 1em;" src="resources/icons/iTunesArtwork.png">
			<p class="lead">Anmeldung // Über <a href="<?php Common::secureEcho(Config::ENVIRONMENT_URL);?>">Moodle</a></p>
			<!-- <p style="text-align:justify;">Herzlich willkommen beim elektronischen Vertretungsplan der <?php Common::secureEcho(Config::TITLE);?></p> -->
			<form action="index.php?component=desktop&amp;view=login" method="post" class="form" id="loginForm">
				<!-- Set username -->
				<div class="form-group">
					<input id=username value="<?php Common::secureEcho(Common::getRequest('username'));?>" name="username" type="text" class="form-control" placeholder="Benutzername">
					<input id="password" name="password" type="password" class="form-control" placeholder="Passwort">	
				</div>
	
				<!-- Submit -->
				<div class="form-group">
					<button style="width: 100%;" type="submit" class="btn btn-primary">Anmelden</button>
				</div>
			</form>
		</div>
		
		<!-- Footer -->
		<ul class="pager">
			<!--<li><a href="http://<?php Common::secureEcho(Config::HTTP_HOST);?>/index.php?view=touch">Touch-Version</a></li>-->
			<li><a href="<?php Common::secureEcho(Config::IMPRINT_URL);?>">Impressum</a></li>
		</ul>
	</body>
</html>