<?php
defined('DIR') or die;

/**
 * environment for a moodle database
 *
 * @package    Ops
 * @author     Konrad Kollnig <team@otto-pankok-schule.de>
 * @copyright  Copyright (C) 2013 Konrad Kollnig
 * @todo Check Moodle Webservice out again: http://www.rumours.co.nz/manuals/using_moodle_web_services.htm
 */

class Environment extends EnvironmentTemplate {
	/**
	 * Remove all existing dbs..
	 * @var stdClass
	 */
	public static $db;

	/**
	 * Remove all existing instances
	 * @var object
	 */
	protected static $instance = null;

	/**
	 * Access to the only instance
	 * @access public
	 * @return self
	 */
	public static function getInstance()
	{
		if(static::$instance === null)
		{
			static::$instance = new static;
		}
		return static::$instance;
	}

	protected function __construct() {
		if(!static::connect(Config::MOODLE_HOST, Config::MOODLE_USER, Config::MOODLE_PWD, Config::MOODLE_DB)) die('Datenbankverbindung fehlgeschlagen.');
	}

	public static function getUserFallback($username) {
		// Get user infos
		$query = "SELECT id, firstname, lastname, username, password, confirmed
					FROM mdl_user
					WHERE deleted = 0 AND
							confirmed = 1 AND
							username = '".mysqli_real_escape_string(static::$db, $username)."'";
		$result = static::query($query);
		if (mysqli_num_rows($result) != 1) return;

		// Parse query to assoc array
		$user     = mysqli_fetch_assoc($result);
		$moodleId = $user['id'];

		// password2change?
		$user['password2change'] = static::moodlePassword2Change($moodleId);

		// Check, whether teacher or pupil
		$status = static::moodleExtra('status', $moodleId);

		if ($status) {
			// Parse status
			switch ($status) {
				case 'Schuelerinnen':
					$user['accessLevel'] = 0;
					break;
				case 'Lehrerinnen':
					$user['accessLevel'] = 1;
					break;
				case 'Referendarinnen':
					$user['accessLevel'] = 1;
					break;
				case 'Verwaltung':
					$user['accessLevel'] = 2;
					break;
				default: // all other users do not get in here..
					return;
			}

		} else { // TODO Keep only for compatibility
			$query = "SELECT roleid FROM `mdl_role_assignments`
						WHERE  roleid = ".static::escapeString(Config::MOODLE_TEACHER_ROLE_ID)." AND
						userid = '".static::escapeString($moodleId)."'";
			$result = static::query($query);

			// Get accessLevel
			if (mysqli_num_rows($result) == 1) {
				$user['accessLevel'] = 1; // teacher
			} else {
				$user['accessLevel'] = 0; // pupil
			}
		}

		// Check, whether user is admin
		$editor = static::moodleExtra('evaeditor', $moodleId);
		if ($editor == '1') {
			$user['accessLevel'] = 3;
		}

		// Check, whether user is admin
		$admin = static::moodleExtra('evaadmin', $moodleId);
		if ($admin == '1') {
			$user['accessLevel'] = 4;
		}

		$user['form']		= static::moodleExtra('klasse', $moodleId);
		$user['teacherTag']	= static::moodleExtra('kuerzel', $moodleId);
		$user['link']		= $moodleId; // to link both accounts

		return $user;
	}

	public static function passwordValid ($password, $hash) {
		return ($hash == md5($password.Config::SALT));
	}

	/**
	 * Fetch a moodle custom field
	 * @param string $param
	 * @param int $moodleId
	 * @return mixed
	 */
	public static function moodleExtra ($param, $moodleId) { 
		// Get id of the $param
		$query = "SELECT id FROM `mdl_user_info_field`
					WHERE shortname = '".mysqli_real_escape_string(static::$db, $param)."'";
		$result = static::query($query);
		$paramId = static::getField($result);

		// Get data of the paramter for $moodleId
		$query = "SELECT data FROM `mdl_user_info_data`
					WHERE userid = '".mysqli_real_escape_string(static::$db, $moodleId)."' AND
							fieldid = '".mysqli_real_escape_string(static::$db, $paramId)."'";
		$result = static::query($query);

		if (mysqli_num_rows($result) == 1)  {
			return static::getField($result);
		} else {
			return null;
		}
	}
    
	// Check if moodle password is outdated
	public static function moodlePassword2Change ($moodleId) {
		$query = "SELECT value FROM `mdl_user_preferences`
					WHERE name = 'auth_forcepasswordchange' AND
						userid = '".mysqli_real_escape_string(static::$db, $moodleId)."'";
		$result = static::query($query);        
        
		if ($result->num_rows == 1)  {
			$result->data_seek(0); 
  			$datarow = $result->fetch_array(); 
   			return $datarow[0]; 
		} else {
			return false;
		}
	}

	// TODO Sync accessLevel??
	public static function sync($user) {
		/*
		 * Get user infos about moodle account
		 */
		$eva = Eva::getInstance();
		$environment = Environment::getInstance();

		// Fetch user from Moodle db
		$query  = "SELECT id, firstname, lastname, username, password FROM mdl_user
					WHERE id = ".intval($user['link'])." AND deleted = 0";

		$result = static::query($query);
		$rows   = $result->fetch_assoc();

		// Compare to the info on the app server.
		switch (static::lastNumRows()) {
			case 1:
				// Get moodle values
				$moodleId        = $rows['id'];

				$password2change = static::moodlePassword2Change($moodleId);
				$tag             = static::moodleExtra('kuerzel', $moodleId);
				$form            = static::moodleExtra('klasse',  $moodleId);

				// Compare moodle values to stored values
				if (!static::checkProperty('firstname'       , $user['firstname']       , $rows['firstname'], $moodleId)) return false;
				if (!static::checkProperty('lastname'        , $user['lastname']        , $rows['lastname'] , $moodleId)) return false;
				if (!static::checkProperty('password'        , $user['password']        , $rows['password'] , $moodleId)) return false;
				if (!static::checkProperty('password2change' , $user['password2change'] , $password2change  , $moodleId)) return false;

				// Only overwrite the value in the app db if moodle value set
				//if (!empty($form) && !static::checkProperty('form'            , $user['form']            , $form             , $moodleId)) return false;
				if (!empty($tag)  && !static::checkProperty('teacherTag'      , $user['teacherTag']      , $tag              , $moodleId)) return false;

				// Get new user info
				$result = $eva->query('SELECT * FROM users WHERE username = "'.mysqli_real_escape_string(static::$db, $user['username']).'"');
				$user = mysqli_fetch_assoc($result);

				return $user;
			default:  // User not in moodle db --> try to delete him
				if ($user['accessLevel'] < 2) {
					$eva->query('DELETE FROM users WHERE id = '.intval($user['id']));
				}

				return false;
		}
	}

	/**
	 * Sync specific value to the eva db
	 * @param string $name Name of the property that shall be checked
	 * @param mixed $baseValue Value in the app db
	 * @param mixed $correctValue Value in the moodle db
	 * @return boolean
	 */
	public static function checkProperty($name, $baseValue, $correctValue, $moodleId) {
		if ($baseValue == $correctValue || $correctValue === null) return TRUE;

		$eva = Eva::getInstance();
		$eva->query("UPDATE users SET ".mysqli_real_escape_string(static::$db, $name)." = '".mysqli_real_escape_string(static::$db, $correctValue)."' WHERE link = '".mysqli_real_escape_string(static::$db, $moodleId)."'");
		return (mysqli_affected_rows(static::$db) == 1);
	}

	public static function updateExternalPass($user, $hashedPass, $password2change) {
		// Get id for identification
		$moodleId = $user['link'];

		// Update password
		$result = static::query("UPDATE mdl_user SET password = '".static::escapeString($hashedPass)."'
										WHERE id = '".static::escapeString($moodleId)."'");
		if (!$result) return false;

		// Set password2change flag in db
		$query = "SELECT value FROM mdl_user_preferences WHERE name = 'auth_forcepasswordchange' AND userid = ".intval($moodleId);
		$result = static::query($query);

		// Check number of entries
		$count = static::lastNumRows();

		switch ($count) {
			case 0: // Flag hasn't been created
				$query = "INSERT INTO mdl_user_preferences (`userid`, `name`, `value`) VALUES (".intval($moodleId).", 'auth_forcepasswordchange', ".intval($password2change).")";
				break;
			case 1: // Flag has been created
				$query = "UPDATE mdl_user_preferences SET value = ".$password2change."
										WHERE name = 'auth_forcepasswordchange' AND userid = '".static::escapeString($moodleId)."'";
				$old_value = static::result($result, 0);
				break;
			default:
				return false;
		}

		static::query($query);

		if (mysqli_affected_rows(static::$db) == 1 || (isset($old_value) && $old_value == intval($password2change))) {
			return true;
		} else {
			return false;
		};
	}

	public static function encryptPassword($password) {
		return md5($password.Config::SALT);
	}

	public static function importSchedule($update) {
		// Get $eva
		$eva = Eva::getInstance();

		// Include the parser (too long..)
		include(DIR.'/config/parser.php');


		/* Apply plan corrections */
		// --> Delete deleted entries
		$eva->query('DELETE a FROM schedule as a
						INNER JOIN schedule as b ON a.lehrerid = b.lehrerid AND a.datum = b.datum AND a.stunde = b.stunde
						WHERE a.loeschen = 0 AND b.loeschen = 1');
		return true;
	}
}