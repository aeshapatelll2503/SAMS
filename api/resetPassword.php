<?php
session_start();
require_once dirname(__DIR__) . '/config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $token           = isset($_POST['token']) ? trim($_POST['token']) : '';
    $newPassword     = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (empty($token) || empty($newPassword) || empty($confirmPassword)) {
        echo "<script>alert('All fields are required.'); window.history.back();</script>";
        exit();
    }

    if ($newPassword !== $confirmPassword) {
        echo "<script>alert('Passwords do not match.'); window.history.back();</script>";
        exit();
    }

    try {
        // 1. Verify token validity and expiration
        $stmt = $pdo->prepare("SELECT id FROM users WHERE reset_token = :token AND reset_expiry > NOW()");
        $stmt->execute(['token' => $token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            echo "<script>alert('Invalid or expired token.'); window.location.href = '../forgotPassword.php';</script>";
            exit();
        }

        // 2. Hash new password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // 3. Update password and invalidate the token
        $updateStmt = $pdo->prepare("UPDATE users SET password = :password, reset_token = NULL, reset_expiry = NULL WHERE id = :id");
        $updateStmt->execute([
            'password' => $hashedPassword,
            'id'       => $user['id']
        ]);

        echo "<script>
                alert('Your password has been reset successfully! Please sign in.');
                window.location.href = '../login.php';
              </script>";
        exit();

    } catch (PDOException $e) {
        echo "<script>alert('Error updating password.'); window.history.back();</script>";
        exit();
    }
} else {
    header("Location: ../login.php");
    exit();
}