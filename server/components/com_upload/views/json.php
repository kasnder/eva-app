<?php
defined('DIR') or die;

// Get Eva
$eva = Eva::getInstance();

// Cleaning up after you, my dear!
$eva->query('TRUNCATE TABLE schedule;');

//convert json input to assoc array
$json = json_decode($data, true);

//import assoc array to db
for($i=0; $i<count($json); $i++)
{
		$sql = array();
		foreach($json[$i] as $key => $value){
				//$sql[] = (is_numeric($value)) ? "`$key` = $value" : "`$key` = '" . mysql_real_escape_string(utf8_decode($value)) . "'";
				$sql[] = (is_numeric($value)) ? "`".mysql_real_escape_string($key)."` = $value" : "`".mysql_real_escape_string($key)."` = '" . mysql_real_escape_string($value) . "'";
		}
		$sqlclause = implode(",", $sql);
		$eva->query("INSERT INTO schedule SET $sqlclause");
}

// Make sure we have klassePatch in the new format but with the correct numbers in klasse
$eva->query('UPDATE schedule SET klasse = "10" WHERE klasse = "EF";');
$eva->query('UPDATE schedule SET klasse = "11" WHERE klasse = "Q1";');
$eva->query('UPDATE schedule SET klasse = "12" WHERE klasse = "Q2";');

$eva->query('UPDATE schedule SET klassePatch = klasse;');
$eva->query('UPDATE schedule SET klassePatch = "EF" WHERE klasse = "10";');
$eva->query('UPDATE schedule SET klassePatch = "Q1" WHERE klasse = "11";');
$eva->query('UPDATE schedule SET klassePatch = "Q2" WHERE klasse = "12";');

/* SUCCESS */
$eva->afterUpdate();

// Generate response if everything went right
$output = array();
$output['success'] = 'success';
$output['last_update'] = $eva->getSetting('last_update');

// Now make your way, little JSON
$type = 'json';
echo json_encode($output);