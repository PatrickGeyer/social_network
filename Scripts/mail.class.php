<?php

require 'C://Program Files (x86)/PHP/v5.5.6/ext/PHPMailer-master/PHPMailerAutoload.php';

class Mail {

    function send($receivers, $subject, $message, $alt_message) {
        $mail = new PHPMailer;

        $mail->isSMTP();                                            // Set mailer to use SMTP
        $mail->Host = 'smtp.bis-school.com';                        // Specify main server
        $mail->SMTPAuth = true;                                     // Enable SMTP authentication
        $mail->Username = 's058211';                                // SMTP username
        $mail->Password = 'footballcrazy';                          // SMTP password
        $mail->SMTPSecure = 'tls';                                  // Enable encryption, 'ssl' also accepted

        $mail->From = 's058211@bis-school.com';
        $mail->FromName = 'Patrick Geyer';
        $mail->SingleTo = true;
        foreach ($receivers as $receiver) {
            $mail->addAddress($receiver['email'], $receiver['name']);
        }
        $mail->addAddress('s058211@bis-school.com', 'Patrick Geyer'); // Add a recipient
        $mail->addReplyTo('s058211@bis-school.com', 'Info@TritonCode');

        $mail->isHTML(true);                                  // Set email format to HTML

        $mail->Subject = $subject;
        $mail->Body = $message;
        $mail->AltBody = $alt_message;

        if (!$mail->send()) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
            exit;
        }

        echo 'Message has been sent';
    }

}
