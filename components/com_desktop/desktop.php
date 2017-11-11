<?php
defined('DIR') or die;

/**
 * Application component
 *
 * @package        Ops
 * @subpackage     Ops_com_app
 * @author         Konrad Kollnig <team@otto-pankok-schule.de>
 * @copyright      Copyright (C) 2013 Konrad Kollnig
 * @todo           Template for the HTML Code
 * @todo           Always check XSS
 */
class Component extends ComponentTemplate
{

    protected static $_defaultView = 'login';

    protected static $_requiresSsl = true;

    /**
     * @see ComponentTemplate::getDefaultView()
     * @return string
     */
    function getDefaultView()
    {
        /* Auth user. */
        // Check if there has been already started a session
        if (session_id() == '') {
            session_start();
        }

        // Check the user
        if ($this->validSession($_SESSION)) {
            // Fetch the latest user profile
            $environment          = Environment::getInstance();
            $_SESSION             = $environment->getUser($_SESSION['username']);
            $_SESSION['loggedin'] = true;
            $_SESSION['ip']       = Common::getIp();

            // Admin?
            if ($_SESSION['accessLevel'] == 4) {
                return 'manage';
            }
        }

        return static::$_defaultView;
    }

    /**
     * Check if a session is valid
     *
     * @param array $session Basically the $_SESSION array
     *
     * @return boolean
     */
    function validSession($session)
    {
        return (isset($session['loggedin']) && $session['loggedin'] && isset($session['ip']) && Common::getIp() == $session['ip']);
    }

    /**
     * Fire the component.
     *
     * @see ComponentTemplate::launch()
     */
    function launch()
    {
        /* Auth user. */
        // Check if some has already started a user session = logged in
        if (session_id() == '') {
            session_start();
        }

        // Fetch latest user profile
        if ($this->validSession($_SESSION)) {
            $environment          = Environment::getInstance();
            $_SESSION             = $environment->getUser($_SESSION['username']);
            $_SESSION['loggedin'] = true;
            $_SESSION['ip']       = Common::getIp();
        }

        // Is there a logged in user who accesses this the login page?
        if ($this->view == 'login' && $this->validSession($_SESSION)) {
            if ($_SESSION['accessLevel'] == 4) {
                $this->view == 'manage';
            } else {
                $this->view == 'schedule';
            }
        }

        // Show login screen if not loggedin || invalid ip
        if ($this->view != 'login' && ! $this->validSession($_SESSION)) {
            // Destry old session
            if (session_id() != '') {
                session_destroy();
                session_start();
            }

            // Redirect to login page
            Common::redirect('/index.php?component=desktop&view=login');
        }

        /* Get ready for each view */
        $accessLevel = (isset($_SESSION['accessLevel']) ? intval($_SESSION['accessLevel']) : '');

        switch ($this->view) {
            // Try to login?
            case "login":
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    // Check username and password
                    $username = Common::getRequest('username');
                    $password = Common::getRequest('password');

                    if ( ! empty($username) && ! empty($password)) {
                        // Auth user
                        $environment = Environment::getInstance();
                        $user        = $environment->authUser($password, $username);

                        // Check user --> exists + set up
                        if ( ! $user) {
                            Common::redirect('/index.php?component=desktop&view=login&errorType=auth');
                        }
                        if (isset($user['password2change']) && $user['password2change'] == true) {
                            Common::redirect('/index.php?component=desktop&view=login&errorType=password');
                        }
                        if (empty($user['form']) && empty($user['teacherTag']) && $user['accessLevel'] < 2) {
                            Common::redirect('/index.php?component=desktop&view=login&errorType=config');
                        }

                        // Finally log the user in!
                        $_SESSION             = $user;
                        $_SESSION['loggedin'] = true;
                        $_SESSION['ip']       = Common::getIp();

                        // Schedule for normal users; controlcenter for admins
                        if ($_SESSION['accessLevel'] == 4) {
                            $view = 'manage';
                        } else {
                            $view = 'schedule';
                        }

                        Common::redirect('/index.php?component=desktop&view='.$view);
                    }
                }
                break;

            // Try to reach the controlcenter?
            case "manage":
                if ($accessLevel != 4) {
                    $this->view = 'login';
                }

                return;
                break;

            // Try to fetch the schedule?
            case "schedule":
                $eva = Eva::getInstance();

                return $eva->generateSchedule($_SESSION);
                break;
        }
    }
}