<?php
/**
 * EVa Core. Provides easy access to the EVa db.
 *
 * - Schedule
 * - Registry
 *
 * @package	Ops
 * @author	 Konrad Kollnig <team@otto-pankok-schule.de>
 * @copyright  Copyright (C) 2013 Konrad Kollnig
 */

defined('DIR') or die;

class Eva extends Database {
	/**
	 * Remove all existing instances
	 * @var object
	 */
	protected static $instance = null;

	/**
	 * Remove all existing dbs
	 * @var object
	 */
	public static $db = null;

	/**
	 * Create the only instance
	 * @access public
	 * @return static
	 */
	public static function getInstance()
	{
		if(static::$instance === null)
		{
			static::$instance = new static;
		}
		return static::$instance;
	}

	/**
	 * Initialise component
	 *
	 * Set up db connections + determine view.
	 *
	 * @param string $view
	 * @return void
	 */
	protected function __construct() {
		// DB connection
		if(!static::connect(Config::EVA_HOST,
				Config::EVA_USER,
				Config::EVA_PWD,
				Config::EVA_NAME)) die('Datenbankverbindung fehlgeschlagen.');
	}

	/**
	 * This is the hearth of this application. It generates the personalised SCHEDULE!
	 *
	 * @param array $user
	 * @param boolean $all For the plan correction program: include the deleted entries, too.
	 * @return array
	 */
	public static function generateSchedule($user, $all = 0)
	{
		// Process accessLevel
		$service = ($user['accessLevel'] >= 2) ? 1 : 0;
		if ($all == 1) $service = 2;
		if ($user['accessLevel'] == 4 && Common::getRequest('testing') == 1) $service = 3;

		/*
		 * Process service levels
		 *
		 */

		$coulums = 'id, a.datum, stunde, lehrerid, klasse, klassePatch, bemerkung, vertretung, fach, raum, aufsicht, wichtig';
		
		switch ($service) {
			case 1: // Higher user: Fetch everything that's in the future
				$where = 'WHERE loeschen = 0 AND DATEDIFF(datum , NOW()) >= 0';
				$coulums .= ', hinzufuegen, loeschen'; // TODO Check if this entry is really needed.. Should not!
				break;
			case 2: // Plan correction program
				$where = 'WHERE DATEDIFF(datum , NOW()) >= 0';
				$coulums .= ', hinzufuegen, loeschen';
				break;
			case 3: // Testing..
				$where = 'WHERE 1';
				break;
			default: // Teacher + Pupils: Get the latest two dates of the last upload and make sure that they are in the future
				// Get
				$where = 'JOIN (SELECT datum FROM schedule as b WHERE 1 GROUP BY datum LIMIT 0, 2) as c ON a.datum = c.datum WHERE loeschen = 0 AND DATEDIFF(a.datum , NOW()) >= 0';
				break;
		}

		//echo "SELECT $coulums FROM schedule AS a $where ORDER BY a.datum, stunde, (klasse+0), right(klasse, 1), lehrerid";

		// Do query
		$entries = mysqli_query(static::$db , "SELECT $coulums FROM schedule AS a $where ORDER BY a.datum, stunde, (klasse+0), right(klasse, 1), lehrerid");
		if (!$entries) return null;

		// Save all entries here
		$data = array();

		// Parse form
		$forms = explode(';', $user['form']);

		// Proces entries
		while($entry = mysqli_fetch_assoc($entries)) {
			/*
			 * Show all values.
			 * If set or admin.
			 */
			if (Config::DISABLE_SCHEDULE_FILTERS || $user['accessLevel'] >= 2) {
				$data[] = $entry;
				continue;
			}

			/*
			 * Only show a schedule to set up users
			 */
			if ((count($forms) == 0 && empty($user['teacherTag'])) || (isset($user['password2change']) && $user['password2change'] == true)) {
				continue;
			}

			// Finally check the actual entry with the user's data
			if (static::isValidEntry($entry, $user, $forms)) {
				$data[] = $entry;
				continue;
			}
		}

		return $data; // valid
	}

	private static function isValidEntry ($entry, $user, $forms) {		
		// [1] Klassencheck --> (#[0-9]{1,2}-[0-9]{1,2}#) --> klassenübergreifend // nur klasse --> (#[0-9][a-z])|([0-9]#i) // alle klassen --> alles andere
		// Filter invalid entries
		$classes = explode(" ", $entry['klasse']);
		$filteredClasses = array_filter($classes, function ($class) {
				return preg_match('#^([5-9][a-z])$|^([0-9]{1,2})$#i', $class);
			}
		);

		// Only allow one class for pupils
		if ($user['accessLevel'] == 0 && array_key_exists(0, $filteredClasses)) {
			$filteredClasses = array($filteredClasses[0]);
		}

		// Check class settings
		$matchesClass = count(array_intersect($forms, $filteredClasses)) >= 1;
		if ($matchesClass) return true;

		// Check teacher settings
		$matchesTeacher = $user['accessLevel'] == 1 // is teacher
			&& !empty($user['teacherTag'])  // account is set up
			&& ((isset($entry['lehrerid'])   && !empty($entry['lehrerid'])   && !empty($user['teacherTag']) && stripos($entry['lehrerid'],   $user['teacherTag']) !== false)
			|| (isset($entry['vertretung']) && !empty($entry['vertretung']) && !empty($user['teacherTag']) && stripos($entry['vertretung'], $user['teacherTag']) !== false));
		if ($matchesTeacher) return true;

		// Check overlapping events
		// [2] Overlapping entries?
		$isOverlapping = false;

		if (preg_match('#^[0-9]{1,2}-[0-9]{1,2}$#', $entry['klasse'])) {
			// Process forms
			foreach ($forms as $form) {
				// Split up
				$limits = explode("-", $entry['klasse']);

				// remove evererything but numbers
				$class = preg_replace("/[^0-9]/", "", $form);

				// check, if the user's form is between limits 0 and limits 1 then he does not need to see this entry
				if (((intval($class) >= intval($limits[0]))&&(intval($class) <= intval($limits[1])))) {
					$isOverlapping = true;
					break;
				}
			}
		}

		if ($isOverlapping) return true;

		// Is entry for everyone
		$forAll = empty($entry['klasse']) && !$entry['aufsicht'];
		if ($forAll) return true;

		/*
		 * Alle anderen Einträge, die nicht bestimmt werden können, ebenfalls anzeigen..
		*
		* - Keine Klasse
		* - Kein Array von Klassen
		* - Kein klassenübergreifender Eintrag
		* - Keine Aufsicht ==> Ausfall
		*/
		/*if (!preg_match('#^([5-9][a-z])$|^([0-9]{1,2})$#i', $schedule_form) && !($size > 1 && $valid) && !preg_match('#^[0-9]{1,2}-[0-9]{1,2}$#', $schedule_form) && !$row['aufsicht']) {
			$data[] = $row;
			continue;
		}*/

		return false;
	}

	/**
	 * Generate a note to display along with the schedule
	 *
	 * @param unknown $accessLevel
	 * @param string $oneLine Formatted output?
	 * @param string $date The date of the note. Normally now.
	 * @return string Result in html!
	 */
	public static function generateNote($accessLevel, $oneLine = true, $date = null) {
		// Check if there's a date
		$where = 'date = ';
		if ($date) {
			$where .= '"'.mysqli_real_escape_string(static::$db, $date).'"';
		} else {
			$where .= '"'.date('Y-m-d').'"';
		}

		// Missing teachers not for pupils
		if ($accessLevel == 0) {
			$where .= ' AND type < 2' ;
		}
		// Query to $result
		$result = mysqli_query(static::$db , 'SELECT * FROM notes WHERE '.$where.' ORDER BY type DESC');

		// Are there some notes to display?
		if (!mysqli_num_rows($result) > 0) return '';

		// Define $note
		$normalNotes = '';
		$otherNotes  = '';

		// Process result of query
		while($row = mysqli_fetch_assoc($result)) {
			/* Normal comments shall be displayed in their own line.
			 * 			 */
			if ($row['type'] == 0) {
				$normalNotes .= '<p>'.Common::escapeStrings($row['note']).'</p>';
				continue;
			}

			if ($oneLine) {
				// Have there already been notes stored in $otherNotes
				if (!empty($otherNotes)) {
					$otherNotes .= ', ';
				}

				$otherNotes .= Common::escapeStrings($row['note']);
			} else {
				$otherNotes .= '<p>'.Common::escapeStrings($row['note']).'</p>';
			}
		}

		if ($oneLine) $otherNotes = '<p style="display:inline;">'.$otherNotes.'</p>';
		return $otherNotes.$normalNotes;
	}

	/**
	 * Access a setting in the app registry
	 * @param string $setting Name of a setting
	 * @return string|NULL
	 */
	public static function getSetting($setting)
	{
		// get setting
		$query = "SELECT value FROM settings WHERE setting = '".mysqli_real_escape_string(static::$db, $setting)."'";
		$result = mysqli_query(static::$db, $query);

		// more than one result
		if (mysqli_num_rows($result) != 1) goto error;

		// select value
        	$value = Database::result($result, 0);

		return $value; // valid

		error:
		return;
	}

	/**
	 * Store a value in the app registry
	 * @param string $setting Name of the setting
	 * @param mixed $new_value New value for the setting
	 * @return boolean
	 */
	public static function setSetting($setting, $new_value)
	{
		// Has the setting already been created?
		// Ask the database
		$query = "SELECT value FROM settings WHERE setting = '".mysqli_real_escape_string(static::$db, $setting)."'";
		$result = mysqli_query(static::$db, $query);

		// Check number of entries
		$count = mysqli_num_rows($result);

		switch ($count) {
			case 0: // Setting hasn't been created
				$query = "INSERT INTO `settings` (`id`, `setting`, `value`) VALUES (NULL, '".mysqli_real_escape_string(static::$db, $setting)."', '".mysqli_real_escape_string(static::$db, $new_value)."');";
				break;
			case 1: // Setting has been created
				$query = "UPDATE `settings` SET `value` = '".mysqli_real_escape_string(static::$db, $new_value)."' WHERE `settings`.`setting` = '".mysqli_real_escape_string(static::$db, $setting)."';";
				$old_value = Database::result($result, 0);
				break;
			default:
				return false;
		}

		mysqli_query(static::$db, $query);

		if (mysqli_affected_rows(static::$db) == 1 || (isset($old_value) && $new_value == $old_value)) {
			return true; // valid
		} else {
			return false;
		};
	}

	/**
	 * Informs the user about schedule changes
	 * @return string
	 */
	public static function afterUpdate() {
		// Save update time tp db
		static::setSetting('last_update', date('d.m.Y H:i'));

		/* Push Section */
		// Send GCM Message
		// Message to send
		$message	  = "Neue Ausfälle zur ersten Stunde!";
		$tickerText   = "";
		$title = "Otto-Pankok-Schule";
		$contentText  = "";

		$apiKey = Config::GCM_KEY;

		// Get intelligent date
		// If time is 9am or later then send notifications about tomorrow
		if (date('H') > 8) {
			$date = date('Y-m-d', strtotime("tomorrow"));
		} else {
			$date = date('Y-m-d');
		}

		// Fetch recipients		
		$sql = "SELECT regid 
				FROM push p 
				WHERE #disabled = false AND 
					userid IN (
					SELECT id 
					FROM users u 
					WHERE EXISTS (
						SELECT 1
						FROM schedule s
						WHERE stunde = 1 # first stunde
							AND datum = '".mysqli_real_escape_string($date)."' # intelligent date
							AND NOT md5(CONCAT_WS('#',`datum`,`stunde`,`lehrerid`,`klassePatch`,`bemerkung`,`vertretung`,`fach`,`raum`,`aufsicht`)) IN ( # check deltas 
								SELECT delta
								FROM push_deltas d
							)
							AND (u.accessLevel >= 2 # admins
								OR (u.accessLevel < 2 # teachers and pupils
								AND		(u.`teacherTag` != '' AND u.`teacherTag` = s.`vertretung`)
									   	OR (bemerkung IN ('f.a.', 'EVA') 
										AND u.`form` LIKE CONCAT('%', s.klasse, '%') 
										OR u.`teacherTag` = s.`lehrerid`)										
								)
							)
					)
				)";
		$result = static::query($sql);
		if (!$result || mysqli_num_rows($result) == 0) return;

		// Save all registrationIds in an array
		while($row = mysqli_fetch_array($result))
		{
			$registrationIds[] = $row[0];
		}

		// Finally send notifications
		$response = static::sendNotification(
				$apiKey,
				$registrationIds,
				array('message' => $message, 'tickerText' => $tickerText, 'title' => $title, "contentText" => $contentText) );

		// Regenrate hash table
		$sql = "TRUNCATE push_deltas;
				INSERT IGNORE INTO push_deltas (delta) SELECT md5(CONCAT_WS('#',`datum`,`stunde`,`lehrerid`,`klassePatch`,`bemerkung`,`vertretung`,`fach`,`raum`,`aufsicht`)) AS rowhash FROM schedule;";
		static::query($sql);

		return $response;
	}

	public static function sendNotification( $apiKey, $registrationIdsArray, $messageData )
	{
		$headers = array("Content-Type:" . "application/json", "Authorization:" . "key=" . $apiKey);
		$data = array(
				'data' => $messageData,
				'registration_ids' => $registrationIdsArray
		);

		$ch = curl_init();

		curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch, CURLOPT_URL, "https://android.googleapis.com/gcm/send" );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode($data) );

		$response = curl_exec($ch);
		curl_close($ch);

		return $response;
	}
}