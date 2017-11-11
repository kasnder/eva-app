<?php
defined('DIR') or die;

// Some other vars
$environment = Environment::getInstance();
$type        = 'json';

// Check token
$token = Common::getRequest('token');
if ( ! $token) {
    echoError('Fehlender Parameter (token).');
}

// Check user
$user = $environment->authUser($token);
if ( ! $user) {
    echoError('Authentifizierung fehlgeschlagen.');
}

// What to do?
$updateForm = Common::getRequest('updateForm');
$updateTag  = Common::getRequest('updateTag');
$updatePass = Common::getRequest('updatePass');

// What to set?
$submittedForm    = Common::getRequest('form');
$submittedTag     = Common::getRequest('tag');
$submittedPass    = Common::getRequest('password');
$submittedOldPass = Common::getRequest('oldPassword');

// Some other vars
$eva      = Eva::getInstance();
$username = $user['username'];

/* Try2update */
// Update form
if ($updateForm == 'true') {
    // Check if form has not already been set
    if (isFormValid($user['form'])) {
        echoError('Benutzer bereits eingerichtet!');
    }

    // Valid form submitted?
    if ( ! isFormValid($submittedForm)) {
        echoError('Ungültige Klasseneinstellung!');
    }

    // Perform the update
    $eva->query("UPDATE users SET form = '".mysql_real_escape_string(strtolower($submittedForm))."'
								WHERE username = '".mysql_real_escape_string($username)."'");

    if (mysql_affected_rows(Eva::$db) != 1) {
        echoError('Update fehlgeschlagen.');
    }
}

// Update tag
if ($updateTag == 'true' && $user['accessLevel'] > 0) {
    // Tag already set?
    if ($user['teacherTag'] != '') {
        echoError('Lehrerkürzel bereits gesetzt!');
    }

    // Tag submitted?
    if ($submittedTag == '') {
        echoError('Kein Kürzel angegeben.');
    }

    // Perform the update
    $eva->query("UPDATE users SET teacherTag = '".mysql_real_escape_string($submittedTag)."'
								WHERE username = '".mysql_real_escape_string($username)."'");
    if (mysql_affected_rows(Eva::$db) != 1) {
        echoError('Update fehlgeschlagen.');
    }
}

// Update password
if ($updatePass == 'true') {
    // Validate the submitted password
    $user = $environment->authUser($submittedOldPass, $user['username']);
    if ( ! $user) {
        echoError('Überprüfung des alten Passwortes fehlgeschlagen!');
    }

    // Check submitted password
    if ( ! isPasswordValid($submittedPass)) {
        echoError('Das neue Passwort ist ungültig. Mindestens 8 Zeichen, je ein Groß- und Kleinbuchstabe, eine Ziffer und ein Sonderzeichen. Sorry.');
    }

    // Update pass
    if ( ! $environment->updatePass($user, $submittedPass)) {
        echoError('Update fehlgeschlagen.');
    }
}

echo '{"success": true}';

function isFormValid($form)
{
    return preg_match('#^([5-9][a-z])$|^(1[0-2])$#', $form);
}

function isPasswordValid($password)
{
    // Mindestens 8 Zeichen, je ein Groß- und Kleinbuchstabe und eine Ziffer.
    return preg_match('#^(?=^.{8,}$)(?=.*\d)(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$#', $password);
}

/**
 * End with a Sencha compatible error message
 *
 * @param string $message
 *
 * @return void
 */
function echoError($message)
{
    $output            = [];
    $output['success'] = false;
    $output['error']   = $message;
    echo json_encode($output);
    exit;
}