<?php
session_start();
date_default_timezone_set('Asia/Kolkata');

require_once dirname(__DIR__) . '/config.php';

// login check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Manual PHPMailer Files Include (Avoiding Composer for Simplicity)
require_once dirname(__DIR__) . '/PHPMailer/src/Exception.php';
require_once dirname(__DIR__) . '/PHPMailer/src/PHPMailer.php';
require_once dirname(__DIR__) . '/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';

    if (empty($email)) {
        echo "<script>alert('Please enter your email address.'); window.history.back();</script>";
        exit();
    }

    try {
        // 1. User Database Check
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // 2. Token & Expiry Generate
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // 3. Database Update
            $updateStmt = $pdo->prepare("UPDATE users SET reset_token = :token, reset_expiry = :expiry WHERE email = :email");
            $updateStmt->execute([
                'token'  => $token,
                'expiry' => $expiry,
                'email'  => $email
            ]);

            // 4. Link Creation with URL Encoding (Handles spaces in folder name)
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
            $host = $_SERVER['HTTP_HOST'];
            $projectFolder = rawurlencode(basename(dirname(__DIR__)));
            $resetLink = $protocol . $host . '/' . $projectFolder . "/resetPassword.php?token=" . $token;

            // 5. PHPMailer Config
            $mail = new PHPMailer(true);

            // Server details
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'yourmail@gmail.com';          
            $mail->Password   = 'yourpass';    // 16-digit App Password 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Email Sender & Receiver
            $mail->setFrom('maheshsuthar95888@gmail.com', 'SAMS System');
            $mail->addAddress($email);

            // Mail Content
            $mail->isHTML(true);
            $mail->Subject = 'SAMS - Password Reset Request';
            
            // Email Template
            $mail->Body = "
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset='UTF-8'>
                <style>
                    body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #f7f9fb; margin: 0; padding: 20px; color: #191c1e; }
                    .email-container { max-width: 500px; margin: 0 auto; background-color: #ffffff; border: 1px solid #c3c6d7; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05); }
                    .email-header { background-color: #f7f9fb; border-bottom: 1px solid #c3c6d7; padding: 20px 24px; text-align: left; }
                    .brand { font-size: 22px; font-weight: 700; color: #004ac6; text-decoration: none; }
                    .email-body { padding: 32px 24px; }
                    .title { font-size: 20px; font-weight: 600; color: #191c1e; margin-top: 0; margin-bottom: 12px; }
                    .text { font-size: 14px; line-height: 1.6; color: #434655; margin-bottom: 20px; }
                    .btn-container { text-align: center; margin: 28px 0; }
                    .btn { background-color: #2563eb; color: #ffffff !important; font-size: 14px; font-weight: 600; text-decoration: none; padding: 12px 28px; border-radius: 8px; display: inline-block; }
                    .link-box { background-color: #f2f4f6; padding: 12px; border-radius: 6px; word-break: break-all; font-size: 12px; color: #004ac6; margin-top: 12px; }
                    .email-footer { background-color: #f2f4f6; border-top: 1px solid #c3c6d7; padding: 16px 24px; text-align: center; font-size: 12px; color: #737686; }
                </style>
            </head>
            <body>
                <div class='email-container'>
                    <div class='email-header'>
                        <span class='brand'>SAMS</span>
                    </div>
                    <div class='email-body'>
                        <h2 class='title'>Password Reset Request</h2>
                        <p class='text'>We received a request to reset your password for your <strong>SAMS (Structural Authority Mapping System)</strong> account.</p>
                        <p class='text'>Click the button below to set a new password. This link is valid for <strong>1 hour</strong>.</p>
                        
                        <div class='btn-container'>
                            <a href='{$resetLink}' class='btn' target='_blank'>Reset Password</a>
                        </div>
                        
                        <p class='text' style='margin-bottom: 4px;'>If the button does not work, copy and paste this link into your browser:</p>
                        <div class='link-box'>{$resetLink}</div>
                    </div>
                    <div class='email-footer'>
                        &copy; " . date('Y') . " SAMS Structural Authority Mapping System. All rights reserved.<br>
                        If you did not request a password reset, you can safely ignore this email.
                    </div>
                </div>
            </body>
            </html>
            ";

            // Plain text version for non-HTML email clients
            $mail->AltBody = "SAMS Password Reset Request\n\nClick the link below to reset your password (valid for 1 hour):\n" . $resetLink;

            $mail->send();
        }

        echo "<script>
                alert('If that email exists in our system, a password reset link has been sent.');
                window.location.href = '../login.php';
              </script>";
        exit();

    } catch (Exception $e) {
        echo "<script>alert('Mail error: " . addslashes($mail->ErrorInfo) . "'); window.history.back();</script>";
        exit();
    } catch (PDOException $e) {
        echo "<script>alert('Database error occurred.'); window.history.back();</script>";
        exit();
    }
} else {
    header("Location: ../forgotPassword.php");
    exit();
}