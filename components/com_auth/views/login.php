<?php
defined('DIR') or die;

$user = $data;
$type = 'json';

// TODO: no json header set on die(...)
// Invalid Login Data? - Error
if (!$user) {
	die('{error: "Authentifizierung fehlgeschlagen!"}');
}

if (!is_array($user)){
	$output = array();
	$output['success'] = false;
	$output['error'] = $user;

	// Start the output
	die(json_encode($output));
}

/* Prepare some tasty json */
// Array for the output
$note = array();

// Needs a new password?
if (isset($user['password2change']) && $user['password2change'] == true) {
	$note['password2change'] = true;
}

// Add token
$note['token']     = $user['token'];

// User info
$note['username']  = $user['username'];
$note['firstname'] = $user['firstname'];
$note['lastname']  = $user['lastname'];
$note['klasse']    = $user['form'];

// Info field
$note['info'] = '<p><i>'.$user['note'].'</i></p>';

// Process accessLevel..
$note['isTeacher'] = $user['accessLevel'];

switch ($user['accessLevel']) {
	case 1: { // teacher
		$note['teacherTag'] = $user['teacherTag'];
		break;
	}
	case 4: { // admin
		if (Common::getRequest('os') == 'nativeAndroid') { // Use real a hrefs for android
			$note['info'] .= '<p><a href="'.Common::escapeStrings(Common::getUrl('/index.php?component=desktop&username='.urlencode($user['username'])), true).'">Auf zur Administration!</a></p>';
		} else {
			$note['info'] .= '<p><a href="#" onClick="window.open('."'".Common::escapeStrings(Common::getUrl('/index.php?component=desktop&username='.urlencode($user['username'])), true)."','_blank','location=yes','closebuttoncaption=Return');".'">Auf zur Administration!</a></p>';
		}
		break;
	}
}

// Inform user about beta state
$note['info'] .= '<p>Diese Applikation dient lediglich zur Kontrolle des aushängenden Plans.</p>';

// Check if the user's app version is up-to-date
$jsonVersion = Common::getRequest('jsonVersion');
if ($jsonVersion != Config::SERVER_VERSION) {
	$note['info'] .= '<p><b>Bitte aktualisiere auf die neueste Version!</b></p>';
}

// last_update of schedule data
$note['last_update'] = '<b>'.$user['last_update'].'</b>';

/** Needs update? // First access? **/
if ((!isset($note['klasse']) || empty($note['klasse'])) && (!isset($note['teacherTag']) || empty($note['teacherTag'])) && $note['isTeacher'] < 2) {
	$note['gimmeInformation'] = true;
}

/*if ($note['isTeacher'] > 2) {
	$note['gimmeInformation'] = true;
	$note['password2change'] = true;
}*/

// Sencha stuff
$note['id'] = 0;
$note['success'] = true;

// Start the output
echo json_encode($note);