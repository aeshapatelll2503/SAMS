<!DOCTYPE html>
<html class="light" lang="en">

    <head>
        <meta charset="utf-8">
        <meta content="width=device-width, initial-scale=1.0" name="viewport">
        <title>SAMS - Login</title>
        <!-- Tailwind CSS -->
        <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com" rel="preconnect">
        <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect">
        <link
            href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;family=JetBrains+Mono:wght@400;500&amp;display=swap"
            rel="stylesheet">
        <!-- Material Symbols -->
        <link
            href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
            rel="stylesheet">
        <!-- Tailwind Config -->
        <script id="tailwind-config">
            tailwind.config = {
                darkMode: "class",
                theme: {
                    extend: {
                        "colors": {
                            "on-tertiary-fixed": "#131b2e",
                            "surface": "#f7f9fb",
                            "on-tertiary-fixed-variant": "#3f465c",
                            "surface-container-lowest": "#ffffff",
                            "on-error": "#ffffff",
                            "surface-container-low": "#f2f4f6",
                            "surface-bright": "#f7f9fb",
                            "tertiary-container": "#656d84",
                            "tertiary-fixed": "#dae2fd",
                            "on-error-container": "#93000a",
                            "surface-tint": "#0053db",
                            "secondary": "#505f76",
                            "tertiary-fixed-dim": "#bec6e0",
                            "on-surface": "#191c1e",
                            "on-primary-fixed-variant": "#003ea8",
                            "background": "#f7f9fb",
                            "surface-container": "#eceef0",
                            "surface-container-highest": "#e0e3e5",
                            "error": "#ba1a1a",
                            "inverse-primary": "#b4c5ff",
                            "on-background": "#191c1e",
                            "inverse-surface": "#2d3133",
                            "on-primary-fixed": "#00174b",
                            "secondary-fixed-dim": "#b7c8e1",
                            "surface-container-high": "#e6e8ea",
                            "surface-variant": "#e0e3e5",
                            "secondary-fixed": "#d3e4fe",
                            "primary-fixed": "#dbe1ff",
                            "primary-container": "#2563eb",
                            "on-secondary": "#ffffff",
                            "outline-variant": "#c3c6d7",
                            "on-primary-container": "#eeefff",
                            "outline": "#737686",
                            "surface-dim": "#d8dadc",
                            "on-secondary-fixed-variant": "#38485d",
                            "error-container": "#ffdad6",
                            "on-tertiary": "#ffffff",
                            "on-surface-variant": "#434655",
                            "primary": "#004ac6",
                            "inverse-on-surface": "#eff1f3",
                            "tertiary": "#4d556b",
                            "on-secondary-fixed": "#0b1c30",
                            "secondary-container": "#d0e1fb",
                            "on-primary": "#ffffff",
                            "on-secondary-container": "#54647a",
                            "primary-fixed-dim": "#b4c5ff",
                            "on-tertiary-container": "#eef0ff"
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
                            "xs": "4px",
                            "sm": "8px",
                            "lg": "24px",
                            "2xl": "48px",
                            "md": "16px",
                            "gutter": "16px",
                            "margin-desktop": "32px",
                            "margin-mobile": "16px"
                        },
                        "fontFamily": {
                            "headline-lg": ["Inter"],
                            "title-md": ["Inter"],
                            "title-lg": ["Inter"],
                            "body-md": ["Inter"],
                            "headline-md": ["Inter"],
                            "mono-md": ["JetBrains Mono"],
                            "display-lg": ["Inter"],
                            "body-lg": ["Inter"],
                            "label-md": ["Inter"]
                        },
                        "fontSize": {
                            "headline-lg": ["32px", {
                                "lineHeight": "40px",
                                "letterSpacing": "-0.01em",
                                "fontWeight": "600"
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
                            "headline-md": ["24px", {
                                "lineHeight": "32px",
                                "fontWeight": "600"
                            }],
                            "mono-md": ["13px", {
                                "lineHeight": "20px",
                                "fontWeight": "400"
                            }],
                            "display-lg": ["48px", {
                                "lineHeight": "56px",
                                "letterSpacing": "-0.02em",
                                "fontWeight": "700"
                            }],
                            "body-lg": ["16px", {
                                "lineHeight": "24px",
                                "fontWeight": "400"
                            }],
                            "label-md": ["12px", {
                                "lineHeight": "16px",
                                "letterSpacing": "0.02em",
                                "fontWeight": "500"
                            }]
                        }
                    }
                }
            }
        </script>
        <style>
            .material-symbols-outlined {
                font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            }
        </style>
    </head>

    <body
        class="bg-surface text-on-surface font-body-md text-body-md flex flex-col min-h-screen selection:bg-primary-fixed selection:text-on-primary-fixed">
        <!-- TopNavBar -->
        <header
            class="bg-surface border-b border-outline-variant flex justify-between items-center px-margin-desktop w-full h-16 sticky top-0 z-50">
            <div class="flex items-center gap-md">
                <span class="font-title-lg text-title-lg font-bold text-primary">SAMS</span>
            </div>
            <nav class="hidden md:flex items-center gap-lg">
                <a class="font-body-md text-body-md text-on-surface-variant hover:text-primary transition-colors"
                    href="#">Help</a>
                <a class="font-body-md text-body-md text-on-surface-variant hover:text-primary transition-colors"
                    href="#">Security Policy</a>
            </nav>
        </header>

        <!-- Main Content: Login Card -->
        <main class="flex-1 flex items-center justify-center p-margin-mobile md:p-margin-desktop w-full">
            <div
                class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm w-full max-w-[440px] p-xl flex flex-col gap-lg">
                <div class="text-center mb-sm">
                    <h1 class="font-headline-md text-headline-md text-on-surface">Sign In</h1>
                    <p class="font-body-md text-body-md text-on-surface-variant mt-sm">Access your SAMS mapping workspace.</p>
                </div>
                <form id="loginForm" class="flex flex-col gap-md" action="api/login.php" method="POST">
                    <!-- Email Field -->
                    <div class="flex flex-col gap-xs">
                        <label class="font-label-md text-label-md text-on-surface" for="email">Email Address</label>
                        <input
                            class="font-body-md text-body-md bg-surface-container-lowest border border-outline-variant rounded-lg px-md py-sm w-full text-on-surface placeholder:text-outline focus:outline-none focus:ring-1 focus:ring-primary-container focus:border-primary-container transition-all"
                            id="email" placeholder="name@company.com" required type="email" name="email">
                    </div>

                    <!-- Password Field -->
                    <div class="flex flex-col gap-xs">
                        <div class="flex justify-between items-center">
                            <label class="font-label-md text-label-md text-on-surface" for="password">Password</label>
                            <a class="font-label-md text-label-md text-primary-container hover:underline hover:text-primary transition-colors"
                                href="./forgotPassword.php">Forgot password?</a>
                        </div>
                        <!-- Password Input Wrapper -->
                        <div class="relative flex items-center">
                            <input
                                class="font-body-md text-body-md bg-surface-container-lowest border border-outline-variant rounded-lg pl-md pr-10 py-sm w-full text-on-surface placeholder:text-outline focus:outline-none focus:ring-1 focus:ring-primary-container focus:border-primary-container transition-all"
                                id="password" placeholder="••••••••" required type="password" name="password">
                            
                            <!-- Toggle Button -->
                            <button type="button" id="togglePassword" aria-label="Toggle password visibility"
                                class="absolute right-3 text-outline hover:text-on-surface transition-colors flex items-center justify-center focus:outline-none">
                                <!-- Initially 'visibility_off' for hidden password -->
                                <span id="toggleIcon" class="material-symbols-outlined text-[20px]">visibility_off</span>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center gap-sm mt-xs">
                        <input
                            class="w-4 h-4 rounded border-outline-variant text-primary-container focus:ring-primary-container focus:ring-offset-surface-container-lowest bg-surface-container-lowest cursor-pointer transition-all"
                            id="remember" type="checkbox" name="remember">
                        <label class="font-body-md text-body-md text-on-surface-variant cursor-pointer select-none"
                            for="remember">Remember me</label>
                    </div>

                    <!-- Submit Button -->
                    <button
                        class="mt-sm bg-primary-container hover:bg-primary text-on-primary-container font-title-md text-title-md py-sm px-md rounded-lg flex items-center justify-center gap-sm transition-all shadow-sm group"
                        type="submit">
                        <span>Sign In</span>
                        <span class="material-symbols-outlined text-[20px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
                    </button>
                </form>

                <!-- Secondary Navigation -->
                <div class="mt-md text-center border-t border-outline-variant pt-lg">
                    <p class="font-body-md text-body-md text-on-surface-variant">
                        Don't have an account?
                        <a class="text-primary-container font-medium hover:underline hover:text-primary transition-colors"
                            href="./register.php">Register</a>
                    </p>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-surface-container-low border-t border-outline-variant flex justify-center items-center px-margin-desktop py-lg w-full">
            <div class="font-label-md text-label-md text-secondary text-center">
                © 2024 SAMS Structural Authority Mapping System. All rights reserved.
            </div>
        </footer>

        <script src="./js/login.js"></script>
        
        <!-- Updated Toggle Password Logic -->
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const togglePassword = document.getElementById('togglePassword');
                const passwordInput = document.getElementById('password');
                const toggleIcon = document.getElementById('toggleIcon');

                if (togglePassword && passwordInput && toggleIcon) {
                    togglePassword.addEventListener('click', () => {
                        const isPassword = passwordInput.getAttribute('type') === 'password';
                        
                        // Password show hoga to icon 'visibility' (khuli aankh) ban jayega
                        // Password hide hoga to icon 'visibility_off' (band aankh) ban jayega
                        passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                        toggleIcon.textContent = isPassword ? 'visibility' : 'visibility_off';
                    });
                }
            });
        </script>
    </body>

</html>