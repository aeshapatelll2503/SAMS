<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>SAMS - Reset Password</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;family=JetBrains+Mono:wght@400;500&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "primary-fixed": "#dbe1ff",
                        "tertiary-fixed-dim": "#bec6e0",
                        "on-primary-fixed": "#00174b",
                        "on-background": "#191c1e",
                        "on-tertiary-container": "#eef0ff",
                        "inverse-surface": "#2d3133",
                        "surface-tint": "#0053db",
                        "on-tertiary-fixed": "#131b2e",
                        "outline-variant": "#c3c6d7",
                        "on-tertiary-fixed-variant": "#3f465c",
                        "on-secondary-container": "#54647a",
                        "background": "#f7f9fb",
                        "surface": "#f7f9fb",
                        "surface-container-low": "#f2f4f6",
                        "on-primary-container": "#eeefff",
                        "inverse-primary": "#b4c5ff",
                        "error-container": "#ffdad6",
                        "primary-fixed-dim": "#b4c5ff",
                        "surface-bright": "#f7f9fb",
                        "secondary-fixed-dim": "#b7c8e1",
                        "on-error": "#ffffff",
                        "on-surface": "#191c1e",
                        "on-secondary": "#ffffff",
                        "inverse-on-surface": "#eff1f3",
                        "secondary-fixed": "#d3e4fe",
                        "secondary-container": "#d0e1fb",
                        "error": "#ba1a1a",
                        "surface-container-lowest": "#ffffff",
                        "primary": "#004ac6",
                        "secondary": "#505f76",
                        "surface-container": "#eceef0",
                        "on-error-container": "#93000a",
                        "primary-container": "#2563eb",
                        "on-surface-variant": "#434655",
                        "tertiary": "#4d556b",
                        "on-tertiary": "#ffffff",
                        "surface-container-highest": "#e0e3e5",
                        "on-primary": "#ffffff",
                        "tertiary-fixed": "#dae2fd",
                        "surface-dim": "#d8dadc",
                        "on-secondary-fixed-variant": "#38485d",
                        "on-primary-fixed-variant": "#003ea8",
                        "surface-variant": "#e0e3e5",
                        "tertiary-container": "#656d84",
                        "outline": "#737686",
                        "surface-container-high": "#e6e8ea",
                        "on-secondary-fixed": "#0b1c30"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.125rem",
                        "lg": "0.25rem",
                        "xl": "0.5rem",
                        "full": "0.75rem"
                    },
                    "spacing": {
                        "base": "4px",
                        "xl": "32px",
                        "2xl": "48px",
                        "sm": "8px",
                        "margin-desktop": "32px",
                        "lg": "24px",
                        "margin-mobile": "16px",
                        "md": "16px",
                        "xs": "4px",
                        "gutter": "16px"
                    },
                    "fontFamily": {
                        "headline-lg": ["Inter"],
                        "label-md": ["Inter"],
                        "headline-md": ["Inter"],
                        "display-lg": ["Inter"],
                        "title-md": ["Inter"],
                        "title-lg": ["Inter"],
                        "body-md": ["Inter"],
                        "body-lg": ["Inter"],
                        "mono-md": ["JetBrains Mono"]
                    },
                    "fontSize": {
                        "headline-lg": ["32px", {
                            "lineHeight": "40px",
                            "letterSpacing": "-0.01em",
                            "fontWeight": "600"
                        }],
                        "label-md": ["12px", {
                            "lineHeight": "16px",
                            "letterSpacing": "0.02em",
                            "fontWeight": "500"
                        }],
                        "headline-md": ["24px", {
                            "lineHeight": "32px",
                            "fontWeight": "600"
                        }],
                        "display-lg": ["48px", {
                            "lineHeight": "56px",
                            "letterSpacing": "-0.02em",
                            "fontWeight": "700"
                        }],
                        "title-md": ["16px", {
                            "lineHeight": "24px",
                            "fontWeight": "600"
                        }],
                        "title-lg": ["20px", {
                            "lineHeight": "28px",
                            "fontWeight": "600"
                        }],
                        "body-md": ["14px", {
                            "lineHeight": "20px",
                            "fontWeight": "400"
                        }],
                        "body-lg": ["16px", {
                            "lineHeight": "24px",
                            "fontWeight": "400"
                        }],
                        "mono-md": ["13px", {
                            "lineHeight": "20px",
                            "fontWeight": "400"
                        }]
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-background text-on-background min-h-screen flex flex-col font-body-md antialiased selection:bg-primary-container selection:text-on-primary-container">
    <!-- TopNavBar -->
    <header class="bg-surface border-b border-outline-variant w-full h-16 flex justify-between items-center px-margin-desktop sticky top-0 z-50">
        <div class="flex items-center gap-md">
            <span class="font-title-lg text-title-lg font-bold text-primary tracking-tight">SAMS</span>
        </div>
        <nav class="hidden md:flex items-center gap-lg">
            <!-- Navigation hidden for linear transactional flow, but kept structure per specs -->
        </nav>
        <div class="flex items-center gap-md">
            <button class="w-8 h-8 flex items-center justify-center text-on-surface-variant hover:text-primary transition-colors rounded-full focus:outline-none focus:ring-2 focus:ring-primary-container focus:ring-offset-2 focus:ring-offset-surface">

            </button>
        </div>
    </header>
    <!-- Main Content Canvas -->
    <main class="flex-grow flex items-center justify-center p-margin-mobile md:p-margin-desktop relative overflow-hidden">
        <!-- Ambient Background Pattern -->
        <div class="absolute inset-0 z-0 pointer-events-none opacity-20" style="background-image: radial-gradient(var(--tw-colors-outline-variant) 1px, transparent 1px); background-size: 24px 24px;"></div>
        <!-- Auth Card -->
        <div class="relative z-10 w-full max-w-[420px] bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm p-xl flex flex-col gap-lg transition-all duration-300 hover:shadow-md">
            <!-- Header Section -->
            <div class="flex flex-col items-center text-center gap-sm">
                <div class="w-12 h-12 bg-primary-container text-on-primary-container rounded-lg flex items-center justify-center mb-sm shadow-sm">
                    <span class="material-symbols-outlined text-[28px]" data-icon="lock_reset">lock_reset</span>
                </div>
                <h1 class="font-headline-md text-headline-md text-on-surface">Forgot Password</h1>
                <p class="font-body-md text-body-md text-on-surface-variant max-w-[320px]">
                    Enter your email address and we'll send you a secure link to reset your password.
                </p>
            </div>
            <!-- Form Section inside forgotPassword.php -->
            <form action="api/forgotPassword.php" class="flex flex-col gap-md mt-sm" method="POST">
                <div class="flex flex-col gap-xs">
                    <label class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider" for="email">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="material-symbols-outlined text-outline text-[20px]">mail</span>
                        </div>
                        <input class="w-full pl-10 pr-3 py-2 bg-surface border border-outline-variant rounded-md font-mono-md text-mono-md text-on-surface placeholder-outline focus:outline-none focus:ring-1 focus:ring-primary-container focus:border-primary-container transition-all shadow-sm" id="email" name="email" placeholder="Enter your work email" required type="email">
                    </div>
                </div>
                <div class="mt-sm">
                    <button class="w-full bg-primary-container text-on-primary hover:bg-primary transition-colors rounded-md py-2 px-4 font-title-md text-title-md flex items-center justify-center gap-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-container focus:ring-offset-2 focus:ring-offset-surface" type="submit">
                        <span class="material-symbols-outlined text-[20px]">send</span>
                        Send Reset Link
                    </button>
                </div>
            </form>
            <!-- Back to Login Link -->
            <div class="mt-sm text-center border-t border-outline-variant pt-md">
                <a class="inline-flex items-center gap-xs font-body-md text-body-md text-secondary hover:text-primary transition-colors group" href="./login.php">
                    <span class="material-symbols-outlined text-[18px] group-hover:-translate-x-1 transition-transform" data-icon="arrow_back">arrow_back</span>
                    Back to Login
                </a>
            </div>
        </div>
    </main>
    <!-- Footer -->
    <footer class="bg-surface-container-low border-t border-outline-variant flex flex-col md:flex-row justify-center items-center px-margin-desktop py-lg w-full mt-auto z-10">
        <div class="font-label-md text-label-md text-secondary text-center md:text-right">
            © 2024 SAMS Structural Authority Mapping System. All rights reserved.
        </div>
    </footer>


</body>

</html>