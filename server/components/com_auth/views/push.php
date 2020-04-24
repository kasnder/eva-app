<?php

/**
 * Push View
 *
 * Saves the ids for Google Cloud Messaging to the DB
 *
 * @package    Ops
 * @author     Konrad Kollnig <team@otto-pankok-schule.de>
 * @copyright  Copyright (C) 2013 Konrad Kollnig
 */

defined('DIR') or die;

// User logged in?
$success = ($data == true) ;
$type = 'json';

// Save id to db
if ($success && $data['token']) {
	// Check if regid already exists..
	$eva = Eva::getInstance();
	$result = $eva->query("SELECT COUNT(*) FROM push WHERE regid = '".Common::getRequest('regid')."' AND userid = ".intval($data['id']));
	$count = mysqli_result($result, 0);

	// Delete old regid -- could belong to a different user
	if ($count > 0) {
		$eva->query("DELETE FROM push WHERE regid = '".Common::getRequest('regid')."'");
	}

	// Add new regid with the correct userid
	$eva->query("INSERT INTO push (regid, userid) VALUES ('".Common::getRequest('regid')."', ".mysqli_real_escape_string($data['id']).")");
}

$output = array();
$output['success'] = $success;
echo json_encode($output);