<?php
defined('DIR') or die;

$success = $data;
$type = 'json';

// Inform the user if he has been logged out successfully
// TODO Make it work in Sencha..
$output = array();
$output['success'] = ($success == true) ? true : false;
echo json_encode($output);