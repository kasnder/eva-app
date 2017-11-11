<?php
defined('DIR') or die;

/**
 * Settings
 *
 * Center of administrative tasks. Manage the whole system.
 *
 * @package    Ops
 * @subpackage Ops_com_admin
 * @author     Konrad Kollnig <team@otto-pankok-schule.de>
 * @copyright  Copyright (C) 2013 Konrad Kollnig
 * @todo       Give it an own component?? (breaks the scheme)
 * @todo       Use the PHP Template Tags
 */

$environment = Environment::getInstance();
$eva         = Eva::getInstance();
$admin       = $_SESSION;        // store the admin user array
$body        = '';                // store html to show instead of the list of users etc.

// Set up $baseUrl (get the right protocol && host)
$baseUrl = Common::getUrl('/index.php?component=desktop&view=manage', true);

// Is there something to do?
$action = Common::getRequest('action');

switch ($action) {
    case 'resetUser':
        // Get user to reset
        $id = Common::getRequest('id');
        if ( ! is_numeric($id)) {
            break;
        }

        // Reset form and teacherTag
        $result = $eva->query('UPDATE users SET form = NULL, teacherTag = NULL WHERE accessLevel < 4 AND id = '.mysql_real_escape_string($id));
        if ( ! $result) {
            Common::redirect($baseUrl.'&success=false', false);
        }

        // Return to main page
        Common::redirect($baseUrl.'&success=true', false);
    case 'resetPwd':
        // Get id of user to reset
        $id = Common::getRequest('id');
        if ( ! is_numeric($id)) {
            break;
        }

        // Get username
        $result = $eva->query('SELECT username FROM users WHERE accessLevel < 4 AND id = '.mysql_real_escape_string($id));
        if ( ! $result || mysql_num_rows($result) != 1) {
            Common::redirect($baseUrl.'&success=false', false);
        }
        $username = $environment::getField($result);

        // Get and sync user account
        $user = $environment->getUser($username);
        if ( ! $user) {
            Common::redirect($baseUrl.'&success=false', false);
        }

        // Update pass with Config::DEFAULT_PASS
        if ( ! $environment->updatePass($user, Config::DEFAULT_PASS, 1)) {
            Common::redirect($baseUrl.'&success=false', false);
        }

        // Let all current tokens expire
        $eva->query('UPDATE tokens SET expired = 1 WHERE userid = '.mysql_real_escape_string($id));

        // Return to main page
        Common::redirect($baseUrl.'&success=true', false);
    case 'resetAll':
        // Truncate all tables except logs
        // Get all tables
        $sql    = "SHOW TABLES FROM ".Config::EVA_NAME;
        $result = $eva->query($sql);

        if ( ! $result) {
            Common::redirect($baseUrl.'&success=false', false);
        }

        // Finally truncate the tables except logs and users
        while ($row = mysql_fetch_row($result)) {
            $table = $row[0];
            if ($table != 'logs' && $table != 'users') {
                $eva->query("TRUNCATE TABLE $table");
            }
        }

        // Delete all users except admins
        $eva->query("DELETE FROM users WHERE accessLevel < 4");

        // Return to main page
        Common::redirect($baseUrl.'&success=true', false);
    case 'add': // Add users
        // Get user
        $lastname    = Common::getRequest('lastname');
        $firstname   = Common::getRequest('firstname');
        $accessLevel = Common::getRequest('accessLevel');

        // Check credentials --> add user
        if ($lastname == ''
            || $firstname == ''
            || $accessLevel == ''
            || ! $environment->createUser($lastname, $firstname, $accessLevel)
        ) {
            Common::redirect($baseUrl.'&success=false', false);
        }

        // Return to main page
        Common::redirect($baseUrl.'&success=true', false);
    case 'remove': // Remove user from app db
        // Get user to reset
        $id = Common::getRequest('id');
        if ( ! is_numeric($id)) {
            break;
        }

        // Reset form and teacherTag
        $result = $eva->query('DELETE FROM users WHERE accessLevel < 4 AND id = '.mysql_real_escape_string($id));
        if ( ! $result) {
            Common::redirect($baseUrl.'&success=false', false);
        }

        // Return to main page
        Common::redirect($baseUrl.'&success=true', false);
    case 'edit': // Edit user in app db
        // Get user to reset
        $id = Common::getRequest('id');
        if ( ! is_numeric($id)) {
            break;
        }

        // Get username
        $result = $eva->query('SELECT username FROM users WHERE accessLevel < 4 AND id = '.mysql_real_escape_string($id));
        if ( ! $result || mysql_num_rows($result) != 1) {
            Common::redirect($baseUrl.'&success=false', false);
        }
        $username = $environment::getField($result);

        // Get and sync user account
        $user = $environment->getUser($username);
        if ( ! $user) {
            Common::redirect($baseUrl.'&success=false', false);
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $lastname    = Common::getRequest('lastname');
            $firstname   = Common::getRequest('firstname');
            $accessLevel = Common::getRequest('accessLevel');

            // Check credentials --> add user
            if ($lastname == ''
                || $firstname == ''
                || intval($accessLevel) != $accessLevel
                || $accessLevel < 0
                || $accessLevel > 4
            ) {
                Common::redirect($baseUrl.'&success=false', false);
            }

            // Update user
            $result = $eva->query('UPDATE users SET accessLevel = '.intval($accessLevel).',
																		firstname = "'.mysql_real_escape_string($firstname).'",
																		lastname = "'.mysql_real_escape_string($lastname).'"
																	WHERE accessLevel < 4 AND id = '.mysql_real_escape_string($id));
            if ( ! $result) {
                Common::redirect($baseUrl.'&success=false', false);
            }

            // Return to main page
            Common::redirect($baseUrl.'&success=true', false);
        }

        // Output an editor form
        $body
            = '
			<!-- Heading -->
			<h3>Nutzereditor</h3>

			<!-- Editor form -->
			<form action="'.$baseUrl.'&action=edit&id='.intval($id).'" method="post" class="form" role="form" id="editForm">
				<!-- Show username -->
				<div class="form-group">
					<label for="username">Benutzername</label>
					<p id="username" class="form-control-static">'.Common::escapeStrings($user['username']).'</p>
				</div>

				<!-- Set firstname -->
				<div class="form-group">
					<label for="firstname">Vorname</label>
					<input id="firstname" name="firstname" type="text" value="'.Common::escapeStrings($user['firstname']).'" class="form-control" placeholder="Vorname">
				</div>

				<!-- Set lastname -->
				<div class="form-group">
					<label for="lastname">Nachname</label>
					<input id="lastname" name="lastname" type="text" value="'.Common::escapeStrings($user['lastname']).'" class="form-control" placeholder="Nachname">
				</div>

				<!-- Select accessLevel -->
				<div class="form-group">
					<label for="accessLevel">Zugangslevel</label>
					<select class="form-control" id="accessLevel" name="accessLevel">
						<option value="0" '.($user['accessLevel'] == 0 ? 'selected="selected"' : '').'>0 - Schüler(in)</option>
						<option value="1" '.($user['accessLevel'] == 1 ? 'selected="selected"' : '').'>1 - Lehrer(in)</option>
						<option value="2" '.($user['accessLevel'] == 2 ? 'selected="selected"' : '').'>2 - Erweitert</option>
						<option value="3" '.($user['accessLevel'] == 3 ? 'selected="selected"' : '').'>3 - Editor(in)</option>
						<option value="3" '.($user['accessLevel'] == 4 ? 'selected="selected"' : '').'>4 - Administrator(in)</option>
					</select>
				</div>

				<!-- Submit -->
				<div class="form-group">
					<button type="submit" class="btn btn-default">Speichern</button>
				</div>
			</form>

			<!-- Go home! -->
			<a href="'.$baseUrl.'">
				<button class="btn btn-link">
					<span class="glyphicon glyphicon-step-backward"></span> Zurück
				</button>
			</a>

			<!-- Delete user -->
			<a class="pull-right" href="'.$baseUrl.'&action=remove&id='.intval($id).'">
				<button class="btn btn-danger">
					<span class="glyphicon glyphicon-trash"></span> Löschen
				</button>
			</a>

			<!-- Reset password -->
			<a class="pull-right" href="'.$baseUrl.'&action=resetPwd&id='.intval($id).'">
				<button class="btn btn-default">
					<span class="glyphicon glyphicon-repeat"></span> Passwort
				</button>
			</a>

			<!-- Reset setting -->
			<a class="pull-right" href="'.$baseUrl.'&action=resetUser&id='.intval($id).'">
				<button class="btn btn-default">
					<span class="glyphicon glyphicon-remove"></span> Einstellungen
				</button>
			</a>';
        break;
}
?>
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php Common::secureEcho(Config::TITLE_SHORT); ?>: Benutzerliste</title>

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css">

    <!-- Favicon -->
    <link rel="icon" href="resources/icons/favicon.png" type="image/png"/>
</head>
<body style="padding:1em;">
<div class="page-header">
    <h1>
        <a href="<?php echo $baseUrl; ?>"><?php Common::secureEcho(Config::TITLE_SHORT); ?>: Benutzerliste</a>
        <small>
            Guten Tag, <?php Common::secureEcho($admin['firstname'].' '.$admin['lastname']); ?>!
        </small>
        <small>
            <a class="pull-right" href="index.php?component=desktop&amp;view=logout"><i>Ausloggen</i></a>
        </small>
    </h1>
</div>
<?php
$success = Common::getRequest('success');
if ($success):?>
    <div class="alert alert-success">Update erfolgreich!</div>
<?php elseif ( ! empty($success)): ?>
    <div class="alert alert-danger">Update fehlgeschlagen!</div>
<?php endif; ?>
<?php
// Only output a list of options and a list of all users, if there has not already been a body generated
if ($body != '') {
    echo $body.'</body></html>';
    exit;
}
?>

<!-- Form to add new user -->
<form action="<?php echo $baseUrl; ?>&action=add" method="post" style="margin-bottom: 20px;display:none;" class="form-inline" id="addForm">
    <!-- Set firstname -->
    <div class="form-group">
        <input id="firstname" name="firstname" type="text" class="form-control" placeholder="Vorname">
    </div>

    <!-- Set lastname -->
    <div class="form-group">
        <input id="lastname" name="lastname" type="text" class="form-control" placeholder="Nachname">
    </div>

    <!-- Set accessLevel -->
    <div class="form-group">
        <select class="form-control" id="accessLevel" name="accessLevel">
            <option value="0">0 - Schüler(in)</option>
            <option value="1">1 - Lehrer(in)</option>
            <option value="2">2 - Erweitert(in)</option>
            <option value="3">3 - Editor(in)</option>
            <option value="4">4 - Administrator(in)</option>
        </select>
    </div>

    <!-- Submit -->
    <div class="form-group">
        <button type="submit" class="btn btn-default">Erstellen</button>
    </div>
</form>

<!-- System stats -->
<ul class="list-group">
    <li class="list-group-item"><b>Statistiken</b></li>
    <li class="list-group-item">Benutzeranzahl<span class="badge"><?php echo mysql_result($eva->query('SELECT COUNT(*) FROM users'), 0); ?></span>
    </li>
    <li class="list-group-item">Letztes Planupdate<span class="badge"><?php Common::secureEcho($eva->getSetting('last_update')); ?></span></li>
</ul>

<!-- List of actions -->
<ul class="list-group">
    <li class="list-group-item"><b>Aktionen</b></li>
    <li class="list-group-item"><a onClick="document.getElementById('addForm').style.display = 'block';" href="javascript:void(0)">Benutzer
            hinzufügen</a></li>
    <li class="list-group-item"><a href="<?php echo $baseUrl; ?>&action=resetAll"
                                   onclick="return confirm('Achtung! Diese Entscheidung ist ENDGÜLTIG!')">Vertretungsplansystem komplett
            zurücksetzen</a><span class="alert-danger badge">ACHTUNG</span></li>
    <li class="list-group-item"><a
                href="<?php Common::secureEcho(Common::getUrl('/index.php?component=content&view=print&token='.urlencode($admin['token']),
                    true)); ?>">Zum Druck</a></li>
    <li class="list-group-item"><a href="<?php Common::secureEcho(Common::getUrl('/index.php?view=touch', true)); ?>">Zur App</a></li>
    <li class="list-group-item"><a href="<?php Common::secureEcho(Common::getUrl('/index.php?component=desktop&view=schedule', true)); ?>">Zum
            Plan</a></li>
</ul>

<!-- Add a list of all users except admins (for security reasons) -->
<h3>Nutzerliste</h3>
<?php

// Get all users
$users = $eva->query('SELECT * FROM users WHERE 1 ORDER BY lastname, firstname');

// Output all users
if ($users && mysql_num_rows($users) > 0) {
    // Output users in an unordered list
    echo '<table class="table table-striped">
					<thead>
						<tr>
							<th>#</th>
							<th>Nachname, Vorname</th>
							<th style="width:10%;text-align:center;">Einstellungen löschen</th>
							<th style="width:10%;text-align:center;">Passwort zurücksetzen</th>
							<th style="width:10%;text-align:center;">Benutzer löschen</th>
						</tr>
					</thead>
					<tbody>';
    while ($user = mysql_fetch_assoc($users)) {
        if ($user['accessLevel'] == 4) {
            echo '<tr>
							<td>
								<span class="badge">'.intval($user['id']).'</span>
							</td>
							<td>
								'.Common::escapeStrings($user['lastname']).', '.Common::escapeStrings($user['firstname']).'
								<small> '.Common::escapeStrings($user['username']).'</small>
							</td>
							<td style="text-align: center;"></td>
							<td style="text-align: center;"></td>
							<td style="text-align: center;"></td>
						</tr>';
        } else {
            echo '<tr>
							<td>
								<span class="badge">'.intval($user['id']).'</span>
							</td>
							<td>
								<a href="'.$baseUrl.'&action=edit&id='.intval($user['id']).'">
									'.Common::escapeStrings($user['lastname']).', '.Common::escapeStrings($user['firstname']).'
								</a>
								<small> '.Common::escapeStrings($user['username']).'</small>
							</td>
							<td style="text-align: center;">
								<a href="'.$baseUrl.'&action=resetUser&id='.intval($user['id']).'">
									<span class="glyphicon glyphicon-remove"></span>
								</a>
							</td>
							<td style="text-align: center;">
								<a href="'.$baseUrl.'&action=resetPwd&id='.intval($user['id']).'">
									<span class="glyphicon glyphicon-repeat"></span>
								</a>
							</td>
							<td style="text-align: center;">
								<a href="'.$baseUrl.'&action=remove&id='.intval($user['id']).'">
									<span class="glyphicon glyphicon-trash"></span>
								</a>
							</td>
						</tr>';
        }
        flush();
    }

    echo '</tbody></table>';
} else {
    echo '<p>Keine Freunde.. :(</p>';
}
?>
<!-- Footer -->
<ul class="pager">
    <li><a href="http://<?php Common::secureEcho(Config::HTTP_HOST); ?>/index.php?view=touch">Touch-Version</a></li>
    <li><a href="<?php Common::secureEcho(Config::IMPRINT_URL); ?>">Impressum</a></li>
</ul>
</body>
</html>