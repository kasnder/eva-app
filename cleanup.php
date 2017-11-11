<?php
/**
 * Clean up script
 *
 * @package    Ops
 * @author     Konrad Kollnig <team@otto-pankok-schule.de>
 * @copyright  Copyright (C) 2013 Konrad Kollnig
 */

defined('DIR') or die;

$eva = Eva::getInstance();
$lastOptimisation = $eva->getSetting('last_optimisation');

// Check if $lastOptimisation was more than Config::HYGIENE_INTERVAL days ago..
// isset($_GET['clean']) || 
//if (date("Y-m-d", strtotime("-".intval(Config::HYGIENE_INTERVAL)." days")) > $lastOptimisation) {
if (isset($_GET['forceCleanup'])) {
	// Vars
	$eva		 = Eva::getInstance();
	$environment = Environment::getInstance();
	
	/* Clean up.. */
	// Clean logs
	$eva->query('DELETE FROM logs WHERE id < (
									SELECT MIN( id ) 
									FROM (
										SELECT * 
										FROM  `logs` 
										WHERE 1 
										ORDER BY DATE DESC 
										LIMIT '.intval(Config::KEEP_X_LOGS).') 
									AS myselect)');
	
	// Sync (update/delete) all users
	// TODO does this function work --> deleting/updating..
	$result = $eva->query('SELECT * FROM users');
	
	while ($row = mysqli_fetch_assoc($result)) {
		$environment->getUser($row['username']);
	}

	//exit();
	
	// TODO Unset old tokens --> should be part of the sync function
	// Token lifetime, notice in the app about logging out..
	
	// Optimise DBs
	$sql = "SHOW TABLES FROM ".Config::EVA_NAME;
	$result = $eva->query($sql);

	if ($result) {
		while ($row = mysqli_fetch_row($result)) {
			$table = $row[0];
			if ($table != 'logs') $eva->query("OPTIMIZE TABLE $table");
		}
	}
	
	// Finally set updated flag
	$eva->setSetting('last_optimisation', date('Y-m-d'));
}

// Do custom cron..
if (method_exists('Config', 'customCron')) {
	Config::customCron();
}