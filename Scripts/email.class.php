<?php
class Email {

	public function send($receivers, $subject, $content){
		$headers  = "From: social_network < patrick.geyer1@gmail.com >\n";
	    $headers .= "Cc: social_network < patrick.geyer1@gmail.com >\n"; 
	    $headers .= "X-Sender: Patrick Geyer < patrick.geyer1@gmail.com >\n";
	    $headers .= 'X-Mailer: PHP/' . phpversion();
	    $headers .= "X-Priority: 1\n"; 
	    $headers .= "Return-Path: patrick.geyer1@gmail.com\n"; // Return path for errors
	    $headers .= "MIME-Version: 1.0\r\n";
	    $headers .= "Content-Type: text/html; charset=iso-8859-1\n";

	    ini_set('SMTP', 'smtp.gmail.com');
		ini_set('smtp_port', 465);
		ini_set('auth_username', 'patrick.geyer1@gmailc.com');
		ini_set('auth_password', '$19PHPMyWeb98$');

		foreach ($receivers as $to)
		{
			mail($to, $subject, $content, $headers);
		}
	}
}

$mail = new Email();
$receivers = array();
$receivers[0] = 'patrick.geyer1@gmail.com';
$mail->send($receivers, 'Test email', 'This is test html<div>!</div>');

?>