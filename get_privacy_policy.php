<?php
include_once('bootstrap.inc');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Content-type: application/json");
header('Content-Type: text/html; charset=UTF-8');

// START BOOTSTRAP DRUPAL
define('DRUPAL_ROOT', '/var/www/dev.buscarsalud/html');
$_SERVER['REMOTE_ADDR'] = "localhost"; // Necessary if running from command line
require_once DRUPAL_ROOT . '/includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
// END BOOTSTRAP DRUPAL

$node = node_load(2318990);

$terms = $node->body['und']['0']['safe_value'];
//$allowed_tags = array("h3", "p");
//$terms = drupal_html_to_text($terms, $allowed_tags);
//$terms = utf8_encode($terms);

// x cosa
echo $terms;
exit;
?>