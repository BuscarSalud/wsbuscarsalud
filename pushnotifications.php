<?php

if($_POST['message']){
	
	$deviceToken = '7af64e1c ef852cf7 0d931e27 af240c95 ab178f3f 07949cd9 eaf66e0b cf355371';
	
	$alertBody = stripslashes($_POST['message']);
	$badge = 1;
	
	$apnsHost = 'gateway.sandbox.push.apple.com';
	$apnsPort = 2195;
	$apnsCert = 'ck.pem';

	$streamContext = stream_context_create();
	stream_context_set_option($streamContext, 'ssl', 'local_cert', $apnsCert);

	$apns = stream_socket_client('ssl://' . $apnsHost . ':' . $apnsPort, $error, $errorString, 2, STREAM_CLIENT_CONNECT, $streamContext);
	
	
	
	$payload['aps'] = array('alert' => $alertBody, 'badge' => $badge, 'sound' => 'default');
  $payload = json_encode($payload);
        
  
  if ($apns)
        {
          $apnsMessage = chr(0) . chr(0) . chr(32) . pack('H*', str_replace(' ', '', $deviceToken)) . chr(0) . chr(strlen($payload)) . $payload;
          $is = fwrite($apns, $apnsMessage);

          echo "sent: $deviceToken<br />";
        }
        else
        {
          echo "Fehler!";
          var_dump($error);
          var_dump($errorString);
        }
	}
	
	/*$ctx = stream_context_create();
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
	}*/
	
	
	
	
	
	fclose($fp);
							
	
}
?>

<form action="pushnotifications.php" method="post">
	<input type="text" name="message" maxlength="100">
	<input type="submit" value="Send Notification">
</form>