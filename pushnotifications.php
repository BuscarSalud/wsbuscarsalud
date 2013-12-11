<?php



if($_POST['message']){
	
	$alertBody = $_POST['message'];
	$deviceToken = '706c3cac 3f3c9001 82bff01c c1f2cde2 5d0ac389 370161ea f047f817 dcc9abfe'; //Cristian
	//$deviceToken = 'a6283212 de48052f d72f4f04 1c84ff0e cccea7fb 6d5b695c 418e1885 c7098357'; //Marcos
	//$deviceToken = '7af64e1c ef852cf7 0d931e27 af240c95 ab178f3f 07949cd9 eaf66e0b cf355371'; //Felix
	
	$payload['aps'] = array('alert' => $alertBody, 'badge' => 1, 'sound' => 'default');
	$payload['i'] = "otro";
  $payload = json_encode($payload);
	
	$ctx = stream_context_create();
	stream_context_set_option($ctx, 'ssl', 'local_cert', 'ck.pem');
	stream_context_set_option($ctx, 'ssl', 'passphrase', 'buscarsalud');
	$fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
	if(!$fp){
		print "Failed to connect" . $err . $errstr;
		return;
	} else {
		print "Notification sent!";
	}
	
	$devArray = array();
	$devArray[] = $deviceToken;
	
	foreach($devArray as $deviceToken){
		$msg = chr(0) . pack("n",32) . pack('H*', str_replace(' ', '', $deviceToken)) . pack("n",strlen($payload)) . $payload;
		print "sending message :" . $payload . "n";
		fwrite($fp, $msg);
	}
}
// Close the connection to the server
fclose($fp);
?>

<form action="pushnotifications.php" method="post">
	<input type="text" name="message" maxlength="100">
	<input type="submit" value="Send Notification">
</form>