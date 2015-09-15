<?php
	include_once 'config.php';
	
	define('CODE_SUCCESS', 0);
	define('CODE_TRANSMISSION_FAILED', -1);
	define('CODE_INCOMPLETE_REQUEST', -2);
	define('CODE_CAPTCHA_FAILED', -3);
	
	if($_POST["target"] == "contact-form" AND ISSET($_POST["email"]) AND ISSET($_POST["msg"]) AND ISSET($_POST["g-recaptcha-response"]))
	{
		$api_params = array(
				'secret' => GOOGLE_SECRET,
				'response' => $_POST["g-recaptcha-response"]
		);

		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($api_params));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		$response = curl_exec($ch);
		$response = json_decode($response);
		curl_close($ch);
		
		if(!$response->success)
		{
			echo CODE_CAPTCHA_FAILED;
			exit;
		}
		
		$email = htmlspecialchars($_POST["email"], ENT_QUOTES, "UTF-8");
		$msg = htmlspecialchars($_POST["msg"], ENT_QUOTES, "UTF-8");
		
		$id = time().'-'.(strlen($email) + strlen($msg));
		
		$header = 'From: '.$email."\r\n"."Content-type: text/html; charset=utf-8";
		$subject = 'Contact form [threekelv.in] #'.$id;
		
		date_default_timezone_set('CET');
		
		$message = '<b>Form information:</b><br/>';
		$message .= 'IP address: '.$_SERVER['REMOTE_ADDR'].'<br />';
		$message .= 'eMail: '.$email.'<br />';
		$message .= 'Message ID: '.$id.'<br />';
		$message.='Sent: '.date("l j F Y H:i:s e").'<br /><br /><br />';
		$message .= $msg;
		
		if(mail(EMAIL,$subject,$message,$header))
			echo CODE_SUCCESS;
		else
			echo CODE_TRANSMISSION_FAILED;
	}
	else echo CODE_INCOMPLETE_REQUEST;
?>