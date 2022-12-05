<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once(__DIR__ . "/PHPMailer/src/PHPMailer.php");
require_once(__DIR__ . "/PHPMailer/src/Exception.php");
require_once(__DIR__ . "/PHPMailer/src/SMTP.php");

class Mailer
{
    public static function sendOrderConfirmation($firstname, $lastname, $email, $orderID)
    {
        $mail = Mailer::getMailer();
        
        //Set who the message is to be sent to
        $mail->addAddress($email);

        //Set the subject line
        $mail->Subject = "Your Order Confirmation: #" . $orderID;

        //Read an HTML message body from an external file, convert referenced images to embedded,
        //convert HTML into a basic plain-text alternative body
        $html = file_get_contents(__DIR__ . '/orderConfirmation/index.html');
        $html = str_replace("!!FIRSTNAME", $firstname, $html);
        $html = str_replace("!!LASTNAME", $lastname, $html);
        $html = str_replace("!!ORDERNUM", $orderID, $html);
        $html = str_replace("!!ORDERURL", "https://google.com", $html);
        $mail->msgHTML($html, __DIR__ . "/orderConfirmation");

        //Replace the plain text body with one created manually
        $mail->AltBody = 'This is your order confirmation for Order #' . $orderID;

        //send the message, check for errors
        if (!$mail->send()) {
            error_log("Error sending email.");
        }
    }

    public static function sendRepairConfirmation($firstname, $lastname, $email, $repairID)
    {
        $mail = Mailer::getMailer();
        
        //Set who the message is to be sent to
        $mail->addAddress($email);

        //Set the subject line
        $mail->Subject = "Your Repair Confirmation: #" . $repairID;

        //Read an HTML message body from an external file, convert referenced images to embedded,
        //convert HTML into a basic plain-text alternative body
        $html = file_get_contents(__DIR__ . '/repairConfirmation/index.html');
        $html = str_replace("!!FIRSTNAME", $firstname, $html);
        $html = str_replace("!!LASTNAME", $lastname, $html);
        $html = str_replace("!!REPAIRNUM", $repairID, $html);
        $html = str_replace("!!REPAIRURL", "https://google.com", $html);
        $mail->msgHTML($html, __DIR__ . "/repairConfirmation");

        //Replace the plain text body with one created manually
        $mail->AltBody = 'This is your order confirmation for Order #' . $repairID;

        //send the message, check for errors
        if (!$mail->send()) {
            error_log("Error sending email.");
        }
    }

    // Code taken from https://github.com/PHPMailer/PHPMailer/blob/master/examples/gmail.phps
    private static function getMailer(){
        //Create a new PHPMailer instance
        $mail = new PHPMailer();

        //Tell PHPMailer to use SMTP
        $mail->isSMTP();

        //Enable SMTP debugging
        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER;

        //Set the hostname of the mail server
        $mail->Host = 'smtp.gmail.com';
        //Use `$mail->Host = gethostbyname('smtp.gmail.com');`
        //if your network does not support SMTP over IPv6,
        //though this may cause issues with TLS

        //Set the SMTP port number:
        // - 465 for SMTP with implicit TLS, a.k.a. RFC8314 SMTPS or
        // - 587 for SMTP+STARTTLS
        $mail->Port = 587;

        //Set the encryption mechanism to use:
        // - SMTPS (implicit TLS on port 465) or
        // - STARTTLS (explicit TLS on port 587)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

        //Whether to use SMTP authentication
        $mail->SMTPAuth = true;

        //Username to use for SMTP authentication - use full email address for gmail
        $mail->Username = 'maturemotherboards@gmail.com';

        //Password to use for SMTP authentication
        $mail->Password = 'dtgyhnzzdruufuju';

        //Set who the message is to be sent from
        //Note that with gmail you can only use your account address (same as `Username`)
        //or predefined aliases that you have configured within your account.
        //Do not use user-submitted addresses in here
        $mail->setFrom('maturemotherboards@gmail.com', 'Mature Motherboards');

        return $mail;
    }
}