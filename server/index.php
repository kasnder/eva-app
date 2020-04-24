<?php

/**
 * Preloader
 * 
 * The base script for the whole framework. Set up the basic params. Needed for debugging!
 *
 * @package    Ops
 * @author     Konrad Kollnig <team@otto-pankok-schule.de>
 * @copyright  Copyright (C) 2013 Konrad Kollnig
 */

/*
 * TODO: MVC-Structure
 */

define('DEBUGGING_ON', true);

// Apply debugging parameters before starting the application
if (DEBUGGING_ON == TRUE) {
	ini_set('display_errors',  1);
	//ini_set('error_reporting', E_ALL | E_NOTICE /*| E_STRICT*/);
	ini_set('error_reporting', E_ERROR | E_WARNING/* | E_PARSE*/);

	// Error handling function
	function mailError($errno, $errstr, $errfile, $errline)
	{
		$to      = 'h3bitplays@gmail.com';
		$subject = 'EVa - Error';
		$message = "$errno, $errstr, $errfile, $errline";
		$headers = "From: OPS-App <team@otto-pankok-schule.de>";
	
		mail($to, $subject, $message, $headers);
	
		/* Execute PHP internal error handler */
		return false;
	}
	
	set_error_handler("mailError"); // HUBIT: VIELLEICHT SOLLTEST DU DEN HIER AUSMACHEN ;)
} else {
	ini_set('display_errors',  0);
}

// Log errors to file
ini_set('log_errors',  1);
ini_set('error_log', '/home/strato/www/ot/www.otto-pankok-schule.de/htdocs/app/logs/errorlog.txt');

/**
 * Absolute location of the root directory of the application. Make the inclusion of scripts
 * safer and avoid direct access to the script files.
 * 
 * @var string
 */
define('DIR', dirname(__FILE__));

/**
 * Initially start the application.
 */
require_once('loader.php');