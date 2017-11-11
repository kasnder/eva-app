<?php
/**
 * Main library for the Ops Project.
 *
 * @package    Ops
 * @author     Konrad Kollnig <team@otto-pankok-schule.de>
 * @copyright  Copyright (C) 2013 Konrad Kollnig
 */

defined('DIR') or die;

/**
 * Load the eva controller
 **/
require_once(DIR.'/libs/eva.php');

/**
 * Load the environment view.
 */
require_once(DIR.'/libs/templates/environment.php');

/**
 * Load the component view.
 */
require_once(DIR.'/libs/templates/component.php');

/**
 * Provides important global functions
 *
 * These are
 * -
 *
 */
class Common
{

    /**
     * Prevent initialising
     *
     * @access private
     */
    private function __construct()
    {
    }

    public static function getContent($url)
    {
        // Init curl
        $http = curl_init();
        curl_setopt($http, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($http, CURLOPT_URL, $url);

        // Fetch data
        $result = curl_exec($http);

        // Check response code
        $http_status = curl_getinfo($http, CURLINFO_HTTP_CODE);
        if ($http_status != '200') {
            return null;
        }

        // Exit and return
        curl_close($http);

        return $result;
    }

    /**
     * Create a log containing all relevant information and save it in the eva database.
     *
     * @param unknown $db
     *
     * @return resource
     */
    public static function logUser($db, $post = null, $ip = null, $component = null, $view = null, $token = null)
    {
        // Fetch some information
        $post      = (empty($post) ? 'REQUEST: '.print_r($_REQUEST, true)."\ SERVER: ".print_r($_SERVER, true) : $post);
        $ip        = (empty($ip) ? self::getIp() : $ip);
        $token     = Common::getRequest('token');
        $component = Common::getRequest('component');
        $view      = Common::getRequest('view');

        $sql
                = "INSERT INTO `logs` (`id`, 
									`date`, 
									`ip`, 
									`component`, 
									`view`, 
									`token`, 
									`post`) VALUES (NULL, 
													NOW(), 
													'".mysql_real_escape_string($ip)."', 
													'".mysql_real_escape_string($component)."', 
													'".mysql_real_escape_string($view)."', 
													'".mysql_real_escape_string($token)."', 
													'".mysql_real_escape_string($post)."');";
        $result = mysql_query($sql, $db);

        return ($result);
    }

    /**
     * Determine the ip address of the user
     *
     * @return string
     */
    public static function getIp()
    {
        $Ip = '0.0.0.0';
        if (isset($_SERVER['HTTP_CLIENT_IP']) && $_SERVER['HTTP_CLIENT_IP'] != '') {
            $Ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != '') {
            $Ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] != '') {
            $Ip = $_SERVER['REMOTE_ADDR'];
        }
        if (($CommaPos = strpos($Ip, ',')) > 0) {
            $Ip = substr($Ip, 0, ($CommaPos - 1));
        }

        return $Ip;
    }

    /**
     * Shortcut to access a REQUEST value
     *
     * Return value of the specified REQUEST property
     *
     * @param string $request REQUEST property
     * @param string $required
     *
     * @return mixed
     */
    public static function getRequest($request)
    {
        // Check $_REQUEST[$request]
        $request = (isset($_REQUEST[$request]) && ! empty($_REQUEST[$request])) ? $_REQUEST[$request] : '';

        // Filter boolean
        $request = (filter_var($request, FILTER_VALIDATE_BOOLEAN)) ? true : $request;

        return $request;
    }

    /**
     *
     *
     * @param string $output
     *
     * @return void
     */
    public static function secureEcho($output)
    {
        echo self::escapeStrings($output);
    }

    /**
     * Shorthand function to avoid XSS on HTML
     *
     * @param unknown $text
     *
     * @return string
     */
    public static function escapeStrings($text)
    {
        return htmlspecialchars($text, ENT_QUOTES, "UTF-8");
    }

    /**
     * Redirect to a new url
     *
     * @param string  $url
     * @param boolean $forceSsl
     */
    public static function redirect($url, $addHost = true, $forceSsl = false)
    {
        // Add correct host + protocol?
        if ($addHost) {
            $url = self::getUrl($url, $forceSsl);
        }

        // Redirect
        header('Location: '.$url);
        exit;
    }

    /**
     * Redirect to a new url
     *
     * @param string  $url
     * @param boolean $forceSsl
     */
    public static function getUrl($url, $forceSsl = false)
    {
        if ( ! Config::DISABLE_SSL && ($_SERVER["SERVER_PORT"] == 443 || $forceSsl)) {
            return 'https://'.Config::HTTPS_HOST.$url;
        } else {
            return 'http://'.Config::HTTP_HOST.$url;
        }
    }

    /**
     * Prevent initialising
     *
     * @access private
     */
    private function __clone()
    {
    }
}

/**
 * Controller for db connections [Singleton]
 *
 * @see http://de.wikipedia.org/wiki/Singleton_(Entwurfsmuster)
 */
abstract class Database
{

    /**
     * Stores the db connection.
     *
     * @var stdClass
     */
    public static $db;

    /**
     * Stores the result of the last db query.
     *
     * @var resource
     */
    public static $lastResult;

    /**
     * Stores the instance (singleton)
     *
     * @access private
     * @var object
     */
    protected static $instance = null;

    /**
     * @param string $username
     *
     * @return void
     */
    protected function __construct()
    {
    }

    /**
     * Create the only instance
     *
     * @access public
     * @return object
     * @see    Eva
     */
    public static function getInstance()
    {
    }

    /**
     * Connect to a database
     *
     * @param string $host
     * @param string $user
     * @param string $pwd
     * @param string $dbName
     *
     * @return boolean
     */
    public static function connect($host, $user, $pwd, $dbName)
    {
        static::$db = mysql_connect($host, $user, $pwd);
        if ( ! static::$db) {
            return false;
        }
        if ( ! mysql_select_db($dbName, static::$db)) {
            return false;
        }
        if ( ! mysql_query("SET NAMES 'utf8'", static::$db)) {
            return false;
        }

        return true;
    }

    /**
     * @param string $query
     *
     * @return mixed
     */
    public static function query($query)
    {
        // Do the query and save it in $lastResult
        static::$lastResult = mysql_query($query, static::$db);

        // Return result
        return static::$lastResult;
    }

    /**
     * Return the number of rows of the last query
     *
     * @return number
     */
    public static function lastNumRows()
    {
        return mysql_num_rows(static::$lastResult);
    }

    /**
     * Function to receive one specific value from a query result
     *
     * @param resource $result
     * @param number   $field
     *
     * @return mixed
     */
    public static function getField($result, $field = 0)
    {
        // more than one result
        if (mysql_num_rows($result) != 1) {
            return;
        }

        // select field
        $value = mysql_result($result, $field);

        return $value;
    }

    /**
     * @access private
     */
    private function __clone()
    {
    }
}