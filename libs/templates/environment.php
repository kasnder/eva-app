<?php
defined('DIR') or die;

/**
 * Environment template
 *
 * Makes sure the communication between the $eva and the other environment specific
 * components work.
 *
 * Admin stuff, etc.
 *
 * Definition of the accessLevels:
 * - 0: Student
 * - 1: Teacher
 * - 2: any connected hardware
 * - 3: Editor
 * - 4: Admin
 *
 * @package    Ops
 * @author     Konrad Kollnig <team@otto-pankok-schule.de>
 * @copyright  Copyright (C) 2013 Konrad Kollnig
 */
abstract class EnvironmentTemplate extends Database
{

    /**
     * Stores the instance (singleton)
     *
     * @access private
     * @var object
     */
    protected static $instance = null;

    /**
     * Checks whether the user who wants to enter is allowed to access some data.
     *
     * @param string $password Can be a token or a username password combination
     *
     * @return array
     */
    public static function authUser($password, $username = null)
    {
        if ($username) { // A username password combination is provided
            // Get user info.
            $user = static::getUser($username);
            if ( ! $user) {
                return;
            }

            // Check if it is a valid password
            if ( ! static::passwordValid($password, $user['password'])) {
                return;
            }

            // Make sure the user has a token
            if (empty($user['token']) || $user['expired'] == 1) {
                // Generate new token
                $token = static::generateToken();

                // Save to db
                $eva = Eva::getInstance();

                // Delete old tokens
                $eva->query('DELETE FROM tokens WHERE userid = '.mysql_real_escape_string($user['id']));

                // Insert new token
                $result = $eva->query('INSERT INTO tokens (token, created, userid)
								VALUES("'.mysql_real_escape_string($token).'", "'.time().'", '.mysql_real_escape_string($user['id']).')');
                if ( ! $result) {
                    return;
                }

                $user['token'] = $token;
            }

            return $user;
        } else { // A token is provided
            // Security check..
            if (strlen($password) != 32) {
                return;
            }

            // Get username
            $eva      = Eva::getInstance();
            $result   = $eva->query('SELECT username FROM users AS a, tokens AS b
									WHERE a.id = b.userid AND b.token = "'.mysql_real_escape_string($password).'"');
            $username = $eva->getField($result);

            // Is there a user with the submitted token?
            if ( ! $username) {
                return;
            }

            // Get user info
            $user = static::getUser($username);
            if ( ! $user) {
                return;
            }

            // TODO Find a good solution for this part
            if ($user['expired'] == 1) {
                echo '{"error":"Aktuelle Sitzung abgelaufen.","noretry":1}';
                exit;
            }

            return $user;
        }
    }

    /**
     *
     * @param string $username
     *
     * @return multitype:|NULL|array
     */
    public static function getUser($username)
    {
        // Check user if there is an entry in the eva db
        $eva    = Eva::getInstance();
        $result = $eva->query('SELECT *
								FROM users
								WHERE username = "'.mysql_real_escape_string($username).'"');
        if ($eva->lastNumRows() == 1) {
            $user = mysql_fetch_assoc($result);

            // Check if user is valid (uptodate, deleted, ..)
            if ($user['link'] >= 0) {
                $user = static::sync($user);
            }
            if ( ! $user) {
                return;
            }

            // Get token
            $result = $eva->query('SELECT *
									FROM tokens
									WHERE userid = '.mysql_real_escape_string($user['id']).' ORDER BY expired DESC');
            if ($result && $eva->lastNumRows() > 0) {
                $token = mysql_fetch_assoc($result);

                $user['token']   = $token['token'];
                $user['created'] = $token['created'];
                $user['expired'] = $token['expired'];
            } else {
                $user['token']   = null;
                $user['created'] = null;
                $user['expired'] = null;
            }

            return $user;
        };

        // User not in the eva db
        // So check it with a custom condition!
        $user = static::getUserFallback($username);
        if ( ! $user) {
            return;
        }

        // Add user to the eva db
        $eva    = Eva::getInstance();
        $result = $eva->query('INSERT INTO users  (`lastname` ,
													`firstname` ,
													`username` ,
													`password` ,
													`form` ,
													`teacherTag` ,
													`accessLevel`,
													`password2change`,
													`link`
													) VALUES (
														"'.mysql_real_escape_string($user['lastname']).'",
														"'.mysql_real_escape_string($user['firstname']).'",
														"'.mysql_real_escape_string($user['username']).'",
														"'.mysql_real_escape_string($user['password']).'",
														"'.mysql_real_escape_string($user['form']).'",
														"'.mysql_real_escape_string($user['teacherTag']).'",
														"'.mysql_real_escape_string($user['accessLevel']).'",
														"'.mysql_real_escape_string($user['password2change']).'",
														"'.mysql_real_escape_string($user['link']).'"
													)');

        if ( ! $result) {
            return;
        }

        return $user;
    }

    /**
     * Syncs both systems. Updates the user profile.
     *
     * There are certain situations when this becomes important.
     *
     * - Deletion
     * - Update
     *
     * @param array $user
     *
     * @return array
     */
    public static function sync($user)
    {
        return $user;
    }

    /**
     * Custom user getter
     *
     * @param string $password
     *
     * @return array|void
     */
    public static function getUserFallback($username)
    {
        return;
    }

    /**
     * Checks if a entered password matches the correct hash.
     *
     * @param string $password
     * @param string $hash
     *
     * @return boolean
     */
    public static function passwordValid($password, $hash)
    {
        return (sha1(Config::DEFAULT_PASS.$password) == $hash);
    }

    /**
     * Generate a unique random token.
     *
     * @return string
     */
    public static function generateToken()
    {
        do {
            //Generate a random string with all the chars in brackets with a lenght of 32 chars.
            $randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyz"), 0, 32);

            // Check in db if it has not already been used
            $eva = Eva::getInstance();
            $eva->query('SELECT token FROM tokens WHERE token = "'.mysql_real_escape_string($randomString).'"');
        } while ($eva->lastNumRows() != 0);

        return $randomString;
    }

    /**
     * Destroy token to logout.
     *
     * @param int $id User id
     *
     * @return string
     */
    public static function destroyToken($id)
    {
        $eva   = Eva::getInstance();
        $query = $eva->query('UPDATE tokens SET expired = 1 WHERE userid = "'.mysql_real_escape_string($id).'"');

        return ($query == true);
    }

    /**
     * Update parser
     *
     * Applies an update to the db. $update variable contains update.
     *
     * @param string $update Contains the schedule update.
     *
     * @return boolean
     * @todo   Implement standard importer
     */
    public static function importSchedule($update)
    {
    }

    /**
     * public static function to update the pass of an account
     *
     * @param array    $user
     * @param password $password Unencrypted pass
     *
     * @return boolean
     */
    public static function updatePass($user, $password, $password2change = 0)
    {
        // Make pass and user ready
        $hashedPass = static::encryptPassword($password);
        $link       = $user['link'];

        // Update external account
        if ($link != -1) {
            if ( ! static::updateExternalPass($user, $hashedPass, $password2change)) {
                return false;
            }
        }

        // Update internal account
        $eva    = Eva::getInstance();
        $result = $eva->query("UPDATE users SET password2change = ".$password2change.", password = '".mysql_real_escape_string($hashedPass)."'
									WHERE id = '".mysql_real_escape_string($user['id'])."'");
        if ( ! $result) {
            return false;
        }

        return true;
    }

    /**
     * Standard way to encrypt a password
     *
     * @param string $password
     *
     * @return string
     */
    public static function encryptPassword($password)
    {
        return sha1(Config::DEFAULT_PASS.$password);
    }

    /**
     * Overridable public static function to update pass of an external account
     *
     * @param array   $user            Stores information about the user that shall be updated.
     * @param string  $hashedPass      Stores the password that shall be saved in the database
     * @param boolean $password2change Shall the user be forced on his next login to change his password?
     *
     * @return boolean
     */
    public static function updateExternalPass($user, $hashedPass, $password2change)
    {
        return true;
    }

    /**
     * Create new user
     *
     * @param string $lastname
     * @param string $firstname
     * @param int    $accessLevel
     *
     * @return boolean
     * @todo Fehlermeldungen und Infos..
     */
    public static function createUser($lastname, $firstname, $accessLevel)
    {
        // $accessLevel valid?
        if (intval($accessLevel) != $accessLevel || $accessLevel < 0 || $accessLevel > 4) {
            return false;
        }

        // Check if user already exists
        // ELSE: Generate an unique username
        $username = $firstname.'.'.$lastname;
        $i        = 1;

        while (static::getUser($username)) {
            $i++;
            $username = $firstname.'.'.$lastname.'_'.$i;
        }

        // Add user to db
        $eva    = Eva::getInstance();
        $result = $eva->query('INSERT INTO users  (
														`lastname` ,
														`firstname` ,
														`username` ,
														`password` ,
														`form` ,
														`teacherTag` ,
														`accessLevel`,
														`password2change`,
														`link`
														) VALUES (
															"'.mysql_real_escape_string($lastname).'",
															"'.mysql_real_escape_string($firstname).'",
															LOWER("'.mysql_real_escape_string($username).'"),
															"'.mysql_real_escape_string(static::encryptPassword(Config::DEFAULT_PASS)).'",
															"",
															"",
															"'.intval($accessLevel).'",
															1,
															-1
														)');
        if ( ! $result) {
            return false;
        }

        return true;
    }
}