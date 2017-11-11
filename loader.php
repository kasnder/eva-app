<?php
/**
 * Loader
 * 
 * The loading script for the whole framework. Includes all libraries, components and views.
 * 
 * - Libraries are in the <i>libs</i> directory and provide important funtions for all other parts of the application.
 * - Components name the part of the application where the data for the output is generated
 * - views style the output. There can be many ways the same data has to be processed for the user.
 *
 * @package    Ops
 * @author     Konrad Kollnig <team@otto-pankok-schule.de>
 * @copyright  Copyright (C) 2013 Konrad Kollnig
 */
defined('DIR') or die;

//file_put_contents(DIR.'/debug.log', "REQUEST:".print_r($_REQUEST, true).print_r($_SERVER, true)."\n \n", FILE_APPEND);

/*
 * 1. Initialise application
 */
// Load all libs.
require_once(DIR.'/libs/main.php');

// Load the environment specific configuration file
require_once(DIR.'/config/base.php');

// Determine what shall be loaded
$component = Common::getRequest('component');
$view      = Common::getRequest('view');

/* 
 * 2. Load component to fetch data.
 */
// TODO make this part shorter
// Load the component
$componentDir = DIR.'/components/com_'.$component.'/';

if (!is_dir($componentDir)) {
	$component = Config::DEFAULT_COMPONENT;
	$componentDir = DIR.'/components/com_'.$component.'/';
}

// Test upload
/*if (Common::getRequest('upload')) {
	$data = file_get_contents(DIR.'/data.csv');
	include(DIR.'/components/com_upload/views/parser.php');
	exit;
}*/

require_once($componentDir.$component.'.php');

// Initialise the component
$component = new Component($view);

// Let the component do its work
$data = $component->launch();

// Log relevant information for security reasons
/*$eva = Eva::getInstance();
Common::logUser(Eva::$db);*/

/*
 * 3. Server Hygiene: Clean up the server (optimise tables, remove old tokens, etc.)
*/
require(DIR.'/cleanup.php');

/*
 * 4. Finally render the results by applying the view.
*/
$component->render($data);