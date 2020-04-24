<?php
defined('DIR') or die;

class Component extends ComponentTemplate {
	protected static $_defaultView = 'json';
	protected static $_requiresSsl = true;

	function launch () {
		// Get token..
		$token = Common::getRequest('token');
		if (!$token) return;

		// Get user informantion by token
		$environment = Environment::getInstance();
		$user = $environment->authUser($token);
		if (!$user) return;

		// Get schedule
		$eva = Eva::getInstance();
		$this->_['schedule'] = $eva->generateSchedule($user);
		$this->_['user']     = $user;
		return $this->_;
	}
}