<?php
session_start();                  

require_once dirname(__DIR__) . '/config.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $plainPassword = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (empty($email) || empty($plainPassword)) {
        die("<script>alert('Please fill in both email and password.'); window.history.back();</script>");
    }

    try {
       
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

       
        if ($user && password_verify($plainPassword, $user['password'])) {
            
          
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_email'] = $user['email'];

            // Open the new page (Dashboard)
            header("Location: ../dashboard.php");
            exit();

        } else {
            // Email and password do not match
            echo "<script>
                    alert('Invalid email or password. Please try again.');
                    window.history.back();
                  </script>";
        }

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    header("Location: ../login.php");
    exit();
}
?>