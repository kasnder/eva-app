<?php
/**
 * Configuration of the application
 *
 * This file stores all the configurations made by the user to fit his environment.
 *
 * @package    Ops
 * @author     Konrad Kollnig <team@otto-pankok-schule.de>
 * @copyright  Copyright (C) 2013 Konrad Kollnig
 * @todo       Find a solution for dynamic environments + importers.
 */

defined('DIR') or die;

final class Config
{

    // EVa config
    const EVA_HOST = '';

    const EVA_USER = '';

    const EVA_PWD = '';

    const EVA_NAME = '';

    // Environment (moodle) config
    const MOODLE_HOST = '';

    const MOODLE_USER = '';

    const MOODLE_PWD = '';

    const MOODLE_DB = '';

    const MOODLE_TEACHER_ROLE_ID = 2;            // Deprecated TODO do not use roles anymore

    // General configuration
    const TITLE = 'Otto-Pankok-Schule';

    const TITLE_SHORT = 'EVa';

    const HTTP_HOST = 'ops-app.de';

    const HTTPS_HOST = 'ssl-id.de/ops-app.de';

    const DEFAULT_COMPONENT = 'app';

    const ENVIRONMENT_URL = 'http://moodle.otto-pankok-schule.de/';

    const IMPRINT_URL = 'http://otto-pankok-schule.de/impressum/';

    const CONTACT = 'team@otto-pankok-schule.de';

    // Settings
    const DISABLE_SCHEDULE_FILTERS = false;        // DEBUGGING

    const DISABLE_SSL = false;                    // INSECURE

    const DEFAULT_PASS = '';            // INSECURE For password resets TODO RENAME!!

    const HYGIENE_INTERVAL = 7;                    // For the hygiene tasks (cleanup.php)

    const KEEP_X_LOGS = 10000;                    // For the hygiene tasks (cleanup.php)

    const PURGE_TOKEN_ON_LOGOUT = true;            // Destroy token on logout in the app: component=auth&view=logout

    const SERVER_VERSION = '1.01';                // To inform app users about updates - check within component=auth&view=login

    const SALT = '';    // To secure the stored passwords

    const GCM_KEY = '';    // API Key to Google Cloud Messaging (Push Notifications)

    const NEW_YEAR_DATE = '0801'; // mmdd August the 1st -- Reset every school year

    public static function customCron()
    {
        $eva = Eva::getInstance();

        // Yearly reset
        $lastReset = $eva->getSetting('last_reset');
        if (empty($lastReset) || time() - strtotime($lastReset) > 365 * 24 * 60 * 60) {
            $eva->query('UPDATE users SET form = NULL, teacherTag = NULL WHERE accessLevel < 2');
            $eva->setSetting('last_reset', date("Y").Config::NEW_YEAR_DATE); // Set to current year
        }
    }
}

// Localisation
// Needed scince PHP 5.1
date_default_timezone_set('Europe/Berlin');

setlocale(LC_TIME, "de_DE");

/**
 * Load our beloved environment. <3
 */
require_once DIR.'/config/environment.php';