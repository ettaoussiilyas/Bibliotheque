<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';
class SendEmail
{

    private $email;
    private $name;

    private $date;


    public function __construct($email, $name, $date)
    {
        $this->email = $email;
        $this->name = $name;
        $this->date = $date;
    }


    public function send()
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.naver.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'droiders@naver.com';
            $mail->Password = '';
            $mail->SMTPSecure = "tls";
            $mail->Port = 587;

            $mail->setFrom('droiders@naver.com', 'YouCode Bibliotheque');
            $mail->addAddress($this->email, $this->name);

            $mail->isHTML(true);
            $mail->Subject = 'You have passed the due date';
            $mail->Body = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #4CAF50;
        }
        .content {
            margin-bottom: 20px;
        }
        .content p {
            font-size: 16px;
        }
        .footer {
            font-size: 14px;
            text-align: center;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Book Return Reminder</h1>
        </div>
        <div class="content">
            <p>Dear ' . $this->name . ',</p>
            <p>This is a reminder that you have passed the due date to return the book. The book was due to be returned on <strong>' . $this->date . '</strong>.</p>
            <p>Please return the book as soon as possible.</p>
        </div>
        <div class="footer">
            <p>Thank you for your prompt attention.</p>
            <p>Youcode Team</p>
        </div>
    </div>
</body>
</html>
';

            $mail->send();
            echo 'ok';
        } catch (Exception $e) {
            echo "error: " . $e->getMessage();
        }
    }



}