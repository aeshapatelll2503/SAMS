<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Your Account | SAMS</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <link rel="stylesheet" href="./css/register.css">
</head>
<body class="bg-surface">
    <!-- Top Navigation Bar -->
    <header class="navbar">
        <div class="logo">SAMS</div>
        <nav class="nav-links">
            <a href="#" class="nav-link">Help</a>
            <a href="#" class="nav-link">Security Policy</a>
        </nav>
        
    </header>
    <!-- Main Content: Registration Card -->
    <main class="main-container">
        <div class="card">
            <!-- Header -->
            <div class="card-header">
                <h1 class="card-title">Create your Account</h1>
                <p class="card-subtitle">Join the Structural Authority Mapping System</p>
            </div>
            <!-- Form -->
            <form id="registrationForm" class="registration-form"  action="api/register.php" method="POST">
                <!-- Full Name -->
                <div class="form-group">
                    <label for="fullName" class="form-label">Full Name</label>
                    <input type="text" id="fullName" class="form-input" name="fullName" placeholder="John Doe" required autocomplete="name">
                </div>
                <!-- Email Address -->
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" id="email" name="email" class="form-input" placeholder="john@example.com" required autocomplete="email">
                </div>
                <!-- Password -->
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="password-input-wrapper">
                        <input type="password" id="password" name="password" class="form-input" placeholder="••••••••" required autocomplete="new-password">
                        <button type="button" id="passwordToggle" class="password-toggle" aria-label="Toggle password visibility">
    <span class="material-symbols-outlined" id="passwordIcon">visibility_off</span>
</button>

                    </div>
                    <p class="input-hint">Enter a secure password</p>
                    <!-- Strength Indicator -->
                    <div class="strength-container">
                        <div class="strength-bars">
                            <div class="strength-bar" id="bar-1"></div>
                            <div class="strength-bar" id="bar-2"></div>
                            <div class="strength-bar" id="bar-3"></div>
                            <div class="strength-bar" id="bar-4"></div>
                        </div>
                        <span class="strength-text" id="strengthText">Strength:</span>
                    </div>
                </div>
                <!-- Terms Checkbox -->
                <div class="form-checkbox-group">
                    <input type="checkbox" id="terms" name="terms" class="form-checkbox" required>
                    <label for="terms" class="checkbox-label">
                        I agree to the <a href="#" class="inline-link">Terms</a> and <a href="#" class="inline-link">Privacy Policy</a>
                    </label>
                </div>
                <!-- Primary Button -->
                <button type="submit" class="submit-button">
                    <span>Create Account</span>
                    <span class="material-symbols-outlined">arrow_forward</span>
                </button>
            </form>
            <!-- Bottom Link -->
            <div class="card-footer">
                <p class="footer-text">
                    Already have an account? <a href="./login.php" class="bold-link">Login</a>
                </p>
            </div>
        </div>
    </main>
    <!-- Footer -->
    <footer class="page-footer">
        
        <div class="footer-copy">
            &copy; 2026 SAMS Structural Authority Mapping System. All rights reserved.
        </div>
    </footer>
    <script src="./js/register.js"></script>
</body>
</html>
