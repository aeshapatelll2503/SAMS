<?php

require_once dirname(__DIR__) . '/config.php'; 


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $fullName = isset($_POST['fullName']) ? trim($_POST['fullName']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $plainPassword = isset($_POST['password']) ? trim($_POST['password']) : '';
    $terms = isset($_POST['terms']) ? true : false;

   
    if (empty($fullName) || empty($email) || empty($plainPassword)) {
        die("fields cannot be empty");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Email wrong format");
    }

    if (strlen($plainPassword) < 6) {
        die("Password must be at least 6 characters long");
    }

    if (!$terms) {
        die("You must accept the terms and conditions");
    }

    try {
       
        $checkEmailStmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
        $checkEmailStmt->execute(['email' => $email]);
        
        if ($checkEmailStmt->rowCount() > 0) {
            die("This email ID is already registered. Please use another email.");
        }

        
        $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

       
        $sql = "INSERT INTO users (full_name, email, password) VALUES (:full_name, :email, :password)";
        $stmt = $pdo->prepare($sql);
        
        $stmt->execute([
            'full_name' => $fullName,
            'email' => $email,
            'password' => $hashedPassword
        ]);

        echo "<script>
                alert('Account created successfully!');
                window.location.href = '../login.php';
              </script>";
              
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    
    header("Location: ../register.php");
    exit();
}
?>