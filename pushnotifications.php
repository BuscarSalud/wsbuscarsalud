<?php

if($_POST['message']){
	
	$deviceToken = '7af64e1c ef852cf7 0d931e27 af240c95 ab178f3f 07949cd9 eaf66e0b cf355371';
	
	$message = stripslashes($_POST['message']);
	
	$payload = '{
								"aps" : 
									{
										"alert" : "'.$message.'",									
										"badge" : 1,
										"sound" : "bingbong.aiff"
									}
							}';
	$ctx = stream_context_create();
	stream_context_set_option($ctx, 'ssl', 'local_cert', 'ck.pem');
	stream_context_set_option($ctx, 'ssl', 'passphrase', 'no_pass_phrase_yet');
	$fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
	if(!$fp){
		print "Failed to connect $err $errstr";
		return;
	} else {
		print "Notification sent!";
	}
	
	$devArray = array();
	$devArray[] = $deviceToken;
	
	foreach($devArray as $deviceToken){
		$msg = chr(0) . pack("n", 32) . pack('H*', str_replace(' ', '', $deviceToken)) . pack("n",strlen($payload)) . $payload;
		print "sending message :" . $payload . "n";
		fwrite($fp, $msg);
	}
	fclose($fp);
							
	
}
?>

<form action="pushnotifications.php" method="post">
	<input type="text" name="message" maxlength="100">
	<input type="submit" value="Send Notification">
</form>