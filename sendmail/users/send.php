<?php
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=utf-8");
include "../db.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

function send($email)
{
    $message = "http://localhost/resetpassword/?controller=pages&action=resetPassword&email=" . $email; //path to reset password page.
    $subject = "send mail API testing";
    $name = "";

    require("../vendor/autoload.php");

    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->SMTPAuth = true;

    $mail->Host = "smtp.gmail.com"; //host

    $mail->Username = "testwebsite536@gmail.com"; //owner web email
    $mail->Password = "lygn bmvf grvk qvjg"; //owner web email code&password

    $mail->Port = 465;
    $mail->SMTPSecure = "ssl";

    $mail->isHTML(true);
    $mail->setFrom('testwebsite536@gmail.com');
    $mail->addAddress($email); //sent to mail

    $mail->Subject = $subject; //title
    $mail->Body = $message; //message that you want to send

    $result = $mail->send();
    
    return $result;
}


try {

    if (isset($_GET['id'])) {
        $stmt = $conn->prepare("select * from users where id = ?");
        $stmt->execute([$_GET['id']]);
        $email = "";
        foreach ($stmt as $row) {
            $email = $row['email'];
            break;
        }
    } else {
        return;
    }

    send($email);

    $conn = null;
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}
