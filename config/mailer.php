<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';


function kirimEmail($tujuan, $subjek, $isiHTML)
{
    $mail = new PHPMailer(true);

    try {

        // SMTP Gmail
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;

        $mail->Username   = 'deffsuha@gmail.com';
        $mail->Password   = 'olny jhux kdjw bpab';

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;


        // Pengirim
        $mail->setFrom(
            'deffsuha@gmail.com',
            'LSP PPPOLRI'
        );


        // Penerima
        $mail->addAddress($tujuan);


        // Format email
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';

        $mail->Subject = $subjek;
        $mail->Body    = $isiHTML;


        // Kirim
        $mail->send();

        return true;

    } catch (Exception $e) {

        echo "Error SMTP: " . $mail->ErrorInfo;

        return false;
    }
}