<?php
/**
 * Multi purpose parser
 *
 * Uses the environment specific importer
 *
 * @package    Ops
 * @subpackage Ops_com_upload
 * @author     Konrad Kollnig <team@otto-pankok-schule.de>
 * @copyright  Copyright (C) 2013 Konrad Kollnig
 */

defined('DIR') or die;

// Set up some required vars..
$type = 'html';
$eva  = Eva::getInstance();

// Cleaning up after you, my dear!
$eva->query('DELETE FROM schedule WHERE hinzufuegen = 0 AND loeschen = 0;');
$eva->query('TRUNCATE TABLE notes;');

// Import the submitted data
$environment = Environment::getInstance();
if ( ! $environment->importSchedule($data)) {
    exit;
}

/* SUCCESS */
$eva->afterUpdate();

echo 'true';