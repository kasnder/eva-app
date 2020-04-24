<?php
defined('DIR') or die;

// Log the user out..
session_destroy();

// Go back to login page
Common::redirect("/index.php?component=desktop");