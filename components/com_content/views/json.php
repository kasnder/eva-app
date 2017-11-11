<?php
defined('DIR') or die;
	
$type = 'json';

// Check size of data
if (!$data['schedule']) {
  echo '[]';
  exit;
}

// Generate JSON
$json = str_replace('"0"', '0', json_encode($data['schedule']));

// Output JSON
echo $json;