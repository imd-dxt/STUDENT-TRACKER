<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../PHPMailer/PHPMailer/src/Exception.php';
require __DIR__ . '/../PHPMailer/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/../PHPMailer/PHPMailer/src/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);

    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'aziliyoussef2003@gmail.com'; 
        $mail->Password = 'ddaa rovf wwue exxo'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Disable SSL certificate verification (no money to  pay for that certificate hh) 
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        // Recipients
        $mail->setFrom($email, $name);
        $mail->addAddress('aziliyoussef2003@gmail.com'); 

        // Content
        $mail->isHTML(false);
        $mail->Subject = 'Contact Us Message from ' . $name;
        $mail->Body    = "Name: $name\nEmail: $email\n\nMessage:\n$message";

        $mail->send();
        echo 'Message sent successfully.';
    } catch (Exception $e) {
        echo 'Failed to send message. Mailer Error: ', $mail->ErrorInfo;
    }
} else {
    echo 'Invalid request method.';
}
?>