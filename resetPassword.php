<?php
date_default_timezone_set('Asia/Kolkata'); // Bangalore/IST Timezone
require_once __DIR__ . '/config.php';

$token = $_GET['token'] ?? '';
$isValidToken = false;
$userEmail = '';

if (!empty($token)) {
    $currentTime = date('Y-m-d H:i:s'); // Current IST time

    
    $stmt = $pdo->prepare("SELECT email FROM users WHERE reset_token = :token AND reset_expiry > :current_time");
    $stmt->execute([
        'token'        => $token,
        'current_time' => $currentTime
    ]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $isValidToken = true;
        $userEmail = $user['email'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>SAMS - Set New Password</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
</head>
<body class="bg-[#f7f9fb] text-[#191c1e] min-h-screen flex flex-col font-sans antialiased">
    
    <header class="bg-[#f7f9fb] border-b border-[#c3c6d7] w-full h-16 flex items-center px-8">
        <span class="text-xl font-bold text-[#004ac6]">SAMS</span>
    </header>

    <main class="flex-grow flex items-center justify-center p-4">
        <div class="w-full max-w-[420px] bg-white border border-[#c3c6d7] rounded-xl shadow-sm p-8 flex flex-col gap-6">
            
            <?php if ($isValidToken): ?>
                <div class="text-center">
                    <h1 class="text-2xl font-semibold mb-1">Set New Password</h1>
                    <p class="text-sm text-[#434655]">Enter a new password for <strong><?= htmlspecialchars($userEmail) ?></strong></p>
                </div>

                <form action="api/resetPassword.php" method="POST" class="flex flex-col gap-4">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                    
                    <div class="flex flex-col gap-1">
                        <label for="password" class="text-xs font-semibold text-[#434655] uppercase">New Password</label>
                        <input type="password" id="password" name="password" minlength="8" required placeholder="••••••••" class="w-full px-3 py-2 border border-[#c3c6d7] rounded-md focus:outline-none focus:ring-1 focus:ring-[#2563eb]">
                    </div>

                    <div class="flex flex-col gap-1">
                        <label for="confirm_password" class="text-xs font-semibold text-[#434655] uppercase">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" minlength="8" required placeholder="••••••••" class="w-full px-3 py-2 border border-[#c3c6d7] rounded-md focus:outline-none focus:ring-1 focus:ring-[#2563eb]">
                    </div>

                    <button type="submit" class="w-full bg-[#2563eb] text-white hover:bg-[#004ac6] transition-colors rounded-md py-2 font-medium mt-2">
                        Update Password
                    </button>
                </form>
            <?php else: ?>
                <div class="text-center flex flex-col gap-3">
                    <span class="material-symbols-outlined text-4xl text-red-500 mx-auto">error</span>
                    <h1 class="text-xl font-semibold text-red-600">Invalid or Expired Link</h1>
                    <p class="text-sm text-[#434655]">This password reset link is invalid or has expired. Please request a new one.</p>
                    <a href="forgotPassword.php" class="text-[#2563eb] hover:underline font-medium text-sm mt-2">Request New Link</a>
                </div>
            <?php endif; ?>

        </div>
    </main>
    <!-- Footer -->
    <footer class="bg-surface-container-low border-t border-outline-variant flex flex-col md:flex-row justify-center items-center px-margin-desktop py-lg w-full min-h-16 mt-auto z-10">
    <div class="font-label-md text-label-md text-secondary text-center md:text-right">
        © 2024 SAMS Structural Authority Mapping System. All rights reserved.
    </div>
</footer>
</body>
</html>