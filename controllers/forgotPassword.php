<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../db.php'; // เชื่อมต่อกับฐานข้อมูลของคุณ

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    // ตรวจสอบว่ามีอีเมลนี้ในฐานข้อมูลหรือไม่
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // หากอีเมลพบในฐานข้อมูล
        $token = bin2hex(random_bytes(50)); // สร้าง token สำหรับรีเซ็ตรหัสผ่าน
        $resetLink = "http://localhost/poultryFarmManagement/views/pages/resetPasswordPage.php?token=" . $token;

        // อัพเดต token ลงในฐานข้อมูลเพื่อใช้ยืนยันในอนาคต
        $updateToken = $conn->prepare("UPDATE users SET reset_token = ? WHERE email = ?");
        $updateToken->bind_param("ss", $token, $email);
        $updateToken->execute();

        // ตั้งค่าและส่งอีเมล
        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'testwebsite536@gmail.com'; // Your email
            $mail->Password   = 'your_app_password'; // Your app-specific password
            $mail->SMTPSecure = 'ssl';
            $mail->Port       = 465;

            //Recipients
            $mail->setFrom('testwebsite536@gmail.com', 'Poultry Farm Management System');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Reset Your Password';
            $mail->Body    = "Click the link below to reset your password:<br><a href='" . $resetLink . "'>Reset Password</a>";

            $mail->send();
            header('Location: ../views/pages/success.php');
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        // อีเมลไม่พบในฐานข้อมูล
        header('Location: ../views/forgot-password.html?error=email_not_found');
    }
}
?>
