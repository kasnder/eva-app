<?php
defined('DIR') or die;

class Component extends ComponentTemplate {
	protected static $_defaultView = 'turbo';
	protected static $_requiredSsl = true;

	function launch () {
		// Auth user as admin
		$token = Common::getRequest('token');


		$environment = Environment::getInstance();
		$user = $environment->authUser($token);
		if ($user['accessLevel'] < 3) exit;

		// Log relevant information for security reasons
		$eva = Eva::getInstance();
		Common::logUser(Eva::$db);

		// Did we got some data, Captain?
		$input = Common::getRequest('input');
		//$input = file_get_contents(DIR.'/plan.csv');

		// Require some data (max. 1MB)
		if (!$input || strlen($input) > 1048576) exit;
		return $input;
	}
}