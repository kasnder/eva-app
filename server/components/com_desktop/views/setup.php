<?php
defined('DIR') or die;

function isPasswordValid($password) {
    // Mindestens 8 Zeichen, je ein Groß- und Kleinbuchstabe und eine Ziffer.
    return preg_match('#^(?=^.{8,}$)(?=.*\d)(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$#', $password);
}

function isFormValid($form) {
	return preg_match('#^([5-9][a-z])$|^(1[0-2])$#', $form);
}

function needsPwdUpdate() {
    return isset($_SESSION['password2change']) && $_SESSION['password2change'] == true;
}

function needsFormUpdate() {
    return empty($_SESSION['form']) && $_SESSION['accessLevel'] < 2;
}

function performUpdate() {
    $eva = Eva::getInstance();
    $environment = Environment::getInstance();

    if (needsFormUpdate()) {
        $submittedForm = Common::getRequest('form');

        // Valid form submitted?
        if (!isFormValid($submittedForm)) return 'Ungültige Klasseneinstellung!';

        // Perform the update
        $lowerform = strtolower($submittedForm);
        $username = $_SESSION['username'];
        $eva->query("UPDATE users SET form = '".$eva->escapeString($lowerform)."' WHERE username = '".mysqli_real_escape_string(Eva::$db, $username)."'");

        if (mysqli_affected_rows(Eva::$db) != 1) return 'Update fehlgeschlagen.';
    }

    // Update password
    if (needsPwdUpdate()) {
        $submittedPass       = Common::getRequest('password');
        $submittedPassRepeat = Common::getRequest('repeatPassword');
        $submittedOldPass    = Common::getRequest('oldPassword');

        if ($submittedPass != $submittedPassRepeat) return 'Neue Passwörter nicht identisch.';

        // Validate the submitted password
        $user = $environment->authUser($submittedOldPass, $_SESSION['username']);
        if (!$user) return 'Überprüfung des alten Passworts fehlgeschlagen!';
        
        // Check submitted password
        if (!isPasswordValid($submittedPass)) return 'Das neue Passwort ist ungültig. Mindestens 8 Zeichen, je ein Groß- und Kleinbuchstabe, eine Ziffer und ein Sonderzeichen. Sorry.';
        
        // Update pass
        if (!$environment->updatePass($user, $submittedPass)) return 'Update fehlgeschlagen.';
    }

    Common::redirect('/index.php?component=desktop&view=schedule');
}

$error = null;
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $error = performUpdate();
}
?>
<!DOCTYPE HTML>
<html>
    <head>
        <meta charset="UTF-8">
    	<title><?php Common::secureEcho(Config::TITLE_SHORT);?>: Einrichtung</title>
    	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css">
    </head>
    	
    <body style="padding:1em;">
        <!-- Heading -->
        <?php if ($error): ?>
        <div>
            <p><b><?php echo($error); ?></b></p>
        </div>
        <?php endif; ?>

        <!-- Content -->
        <div style="margin-left:auto; margin-right:auto; max-width:500px;">
    		<img style="width: auto;height: 120px;display: block; margin-left: auto; margin-right: auto; margin-bottom: 1em;" src="resources/icons/iTunesArtwork.png">
    		<p class="lead">Account einrichten</p>
    		<form action="index.php?component=desktop&view=setup" method="post" class="form" id="form">
                <?php if(needsFormUpdate()): ?>
        			<div class="form-group">
        			<!-- Set form -->
        				<input id="form" name="form" type="text" class="form-control" placeholder="Klasse/Stufe (z.B 5a, 11)">
        			</div>
                <?php endif; ?>
                <?php if(needsPwdUpdate()): ?>
                    <div class="form-group">
                    <!-- Set form -->                    
                        <input id="oldPassword" name="oldPassword" type="password" class="form-control" placeholder="Altes Passwort">
                        <input id="repeatPassword" name="repeatPassword" type="password" class="form-control" placeholder="Neues Passwort">
                        <input id="password" name="password" type="password" class="form-control" placeholder="Neues Passwort wiederholen">
                    </div>
                <?php endif; ?>
    			<!-- Submit -->
    			<div class="form-group">
    				<button style="width: 100%;" type="submit" class="btn btn-primary">Abschließen</button>

    			</div>
    		</form>
    	</div>

        <!-- Footer -->
    	<ul class="pager">
    		<li><a href="http://<?php Common::secureEcho(Config::HTTP_HOST);?>/index.php?view=touch">Touch-Version</a></li>
    		<li><a href="<?php Common::secureEcho(Config::IMPRINT_URL);?>">Impressum</a></li>
    	</ul>
     </body>
 </html>