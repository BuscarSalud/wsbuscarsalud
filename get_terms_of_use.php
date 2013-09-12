<?php
include_once('bootstrap.inc');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Content-type: application/json");
header('Content-Type: text/html; charset=UTF-8');


$node = node_load(2318989);

$terms = $node->body['und']['0']['safe_value'];
//$allowed_tags = array("h3", "p");
//$terms = drupal_html_to_text($terms, $allowed_tags);
//$terms = utf8_encode($terms);

echo $terms;
exit;
?>