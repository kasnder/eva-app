<?php
defined('DIR') or die;

class Component extends ComponentTemplate {
	protected static $_defaultView = 'login';
	protected static $_requiresSsl = true;

	function launch () {
		$environment = Environment::getInstance();
		$eva = Eva::getInstance();

		if ($this->view == 'login') {
			// Get Gets
			$username = Common::getRequest('username');

			if ($username) {
				$password = Common::getRequest('password');
			} else {
				$password = Common::getRequest('token');
			}

			// Collect info about the user ^^
			$user = $environment->authUser($password, $username);
			if (!$user) return;

			// Add time of last update of the schedule in order to submit it with all the other values
			$user['last_update'] = $eva->getSetting('last_update');
			$user['note'] = $eva->generateNote($user['accessLevel']);

			return $user;
		} elseif ($this->view == 'update') {
			// Just pass environment in order to update the user's profile
			return;
		} elseif ($this->view == 'logout') {
			/* Delete token */
			// Token deletion deactivated?
			if (Config::PURGE_TOKEN_ON_LOGOUT == false) return true;

			// Fetch token
			$token = Common::getRequest('token');

			// Check user
			$user = $environment->authUser($token);
			if (!$user) return;

			// Destroy token && delete regid from server
			return ($environment->destroyToken($user['id']) && $eva->query('DELETE FROM push WHERE userid = '.mysqli_real_escape_string($user['id'])));
		} else {
			// Fetch token
			$token = Common::getRequest('token');

			// Return user info
			return $environment->authUser($token);
		}
	}
}
?>