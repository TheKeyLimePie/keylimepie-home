<?php
	include_once 'config.php';
	
	define('CODE_SUCCESS', 0);
	define('CODE_TRANSMISSION_FAILED', -1);
	define('CODE_INCOMPLETE_REQUEST', -2);
	define('CODE_CAPTCHA_FAILED', -3);
	define('CODE_INVALID_FORMAT', -4);
	
	if(ISSET($_POST["target"]) AND $_POST["target"] == "contact-form" AND ISSET($_POST["email"]) AND ISSET($_POST["msg"]) AND ISSET($_POST["g-recaptcha-response"]))
	{
		if(is_string($_POST["g-recaptcha-response"]) AND is_string($_POST["email"]) AND is_string($_POST["msg"]))
		{
			$captcha_response = htmlspecialchars($_POST["g-recaptcha-response"], ENT_QUOTES, "UTF-8");
			$email = htmlspecialchars($_POST["email"], ENT_QUOTES, "UTF-8");
			$msg = htmlspecialchars($_POST["msg"], ENT_QUOTES, "UTF-8");
			$msg = str_replace(array("\r\n", "\r", "\n"), "<br />", $msg);
			
			$api_params = array(
					'secret' => GOOGLE_SECRET,
					'response' => $captcha_response
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
			
			date_default_timezone_set('CET');
			$id = time().'-'.(strlen($email) + strlen($msg));
			
			// mail parameters for the message to the recipient (me)
			$header = "From: ".HOSTEMAIL."\r\n";
			$header .= "Content-type: text/html; charset=utf-8\r\n";
			$header .= "Reply-to: $email\r\n";
			
			$subject = "Contact form [3k.keylimepie.me] #$id";
			
			$message = "<b>Form information:</b><br />";
			$message .= "IP address: ".$_SERVER['REMOTE_ADDR']."<br />";
			$message .= "eMail: $email<br />";
			$message .= "Message ID: $id<br />";
			$message .= "Sent: ".date("l j F Y H:i:s e")."<br /><br /><br />";
			$message .= $msg;
			
			// mail parameters for the message to the sender
			$header_recp = "From: ".HOSTEMAIL."\r\n";
			$header_recp .= "Content-type: text/html; charset=utf-8\r\n";
			
			$subject_recp = "Acknowledgement of receipt [3k.keylimepie.me] #$id";
			
			$message_recp = "<b>This is an automated message. Please do not reply.</b><br /><br />";
			$message_recp .= "Hey there!<br/>";
			$message_recp .= "I just wanted to let you know that your message made it through. Please give me some time to reply :)<br />";
			$message_recp .= "This is what I got: <br /><br />";
			$message_recp .= "IP address: ".$_SERVER['REMOTE_ADDR']."<br />";
			$message_recp .= "eMail: $email<br />";
			$message_recp .= "Message ID: $id<br />";
			$message_recp .= "Sent: ".date("l j F Y H:i:s e")."<br /><br /><br />";
			$message_recp .= $msg;
			
			if(mail(EMAIL, $subject, $message, $header) and mail($email, $subject_recp, $message_recp, $header_recp))
				echo CODE_SUCCESS;
			else
				echo CODE_TRANSMISSION_FAILED;
		}
		else
		{
			echo CODE_INVALID_FORMAT;
			exit;	
		}
	}
	else echo CODE_INCOMPLETE_REQUEST;
?>