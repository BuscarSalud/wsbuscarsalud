<?php
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__) . '/php_error_log.txt');
error_reporting(E_ALL);

// START BOOTSTRAP DRUPAL
require_once('bootstrap.path.inc');
$_SERVER['REMOTE_ADDR'] = "localhost"; // Necessary if running from command line
require_once DRUPAL_ROOT . '/includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
//include_once('buscarsalud_ws_database.inc.php');
// END BOOTSTRAP DRUPAL
?>