<?php
defined('DIR') or die;

/**
 * Application component
 *
 * This component is the body of this application. This is what the user sees. So it's a GUI
 * for all the other components.
 * 
 * This file checks which component the user needs (app/desktop) and gives the user the right application.
 *
 * @package    Ops
 * @subpackage Ops_com_app
 * @author     Konrad Kollnig <team@otto-pankok-schule.de>
 * @copyright  Copyright (C) 2013 Konrad Kollnig
 */
class Component extends ComponentTemplate {
	protected static $_defaultView = 'notouch';
	protected static $_requiresSsl = false;
	
	/**
	 * Dectect outdated Browsers.
	 * 
	 * @see ComponentTemplate::getDefaultView()
	 * @return string
	 */
	function getDefaultView() {
		/**
		 * Import user agent detection script
		 */
		require_once 'useragentCheck.php';
		
		$uagent = new uagent_info;
		
		if ($uagent->DetectAndroid()) {
			return 'android';
		}
		
		/*if (!$uagent->DetectTierIphone() && !$uagent->DetectTierTablet()) {
			return 'notouch';
		}*/
		
		return static::$_defaultView;
	}

	/**
	 * Fire the component.
	 * 
	 * Prepare for the application views.
	 * 
	 * @see ComponentTemplate::launch()
	 */
	function launch() {
		// Fix for the default application caching
		// Give the app its own url (add view name to the url)
		if (Common::getRequest('view') == '') {
			Common::redirect('/index.php?view='.$this->view);
		}
	}
}