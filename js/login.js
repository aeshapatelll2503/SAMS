document.addEventListener("DOMContentLoaded", () => {
    const loginForm = document.getElementById("loginForm");
    
    if (loginForm) {
        loginForm.addEventListener("submit", (event) => {
            event.preventDefault(); // डिफ़ॉल्ट सबमिशन रोकें

            const email = document.getElementById("email").value.trim();
            const password = document.getElementById("password").value;

            if (!email || !password) {
                alert("Please fill in all required fields.");
                return;
            }

            // FormData ऑब्जेक्ट बनाएं ताकि PHP को डेटा मिल सके
            const formData = new FormData(loginForm);

            // बैकएंड API (api/login.php) को डेटा भेजें
            fetch('api/login.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                // अगर PHP सही लॉगिन पर रीडायरेक्ट (dashboard.php) कर रहा है
                if (response.redirected) {
                    window.location.href = response.url; // डैशबोर्ड पर भेजें
                } else {
                    return response.text();
                }
            })
            .then(data => {
                if (data) {
                    // अगर गलत पासवर्ड होने पर PHP से कोई अलर्ट स्क्रिप्ट आई है, तो उसे चलाएं
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = data;
                    const script = tempDiv.querySelector('script');
                    if (script) {
                        eval(script.innerText);
                    }
                }
            })
            .catch(error => console.error('Error:', error));
        });
    }

    // Live Role Detection on Email Input in Login
    const emailInput = document.getElementById("email");
    const loginRoleDot = document.getElementById("loginRoleDot");
    const loginRoleName = document.getElementById("loginRoleName");
    const loginRoleBadge = document.getElementById("loginRoleBadge");

    function detectRole(email) {
        const emailLower = email.trim().toLowerCase();
        const localPart = emailLower.split('@')[0] || '';
        if (localPart === 'admin' || /(^|\.)admin(\.|$)/i.test(localPart)) {
            return 'admin';
        }
        if (localPart === 'manager' || /(^|\.)manager(\.|$)/i.test(localPart)) {
            return 'manager';
        }
        return 'viewer';
    }

    function updateLoginRole() {
        if (!emailInput || !loginRoleName) return;
        const role = detectRole(emailInput.value);

        if (role === 'admin') {
            if (loginRoleDot) loginRoleDot.className = "w-2.5 h-2.5 rounded-full bg-purple-600 transition-colors";
            if (loginRoleName) {
                loginRoleName.className = "text-purple-700 dark:text-purple-400 font-semibold";
                loginRoleName.textContent = "Admin";
            }
            if (loginRoleBadge) {
                loginRoleBadge.className = "px-2 py-0.5 rounded text-[11px] font-mono bg-purple-100 text-purple-800 border border-purple-300";
                loginRoleBadge.textContent = "Full Access";
            }
        } else if (role === 'manager') {
            if (loginRoleDot) loginRoleDot.className = "w-2.5 h-2.5 rounded-full bg-blue-600 transition-colors";
            if (loginRoleName) {
                loginRoleName.className = "text-blue-700 dark:text-blue-400 font-semibold";
                loginRoleName.textContent = "Manager";
            }
            if (loginRoleBadge) {
                loginRoleBadge.className = "px-2 py-0.5 rounded text-[11px] font-mono bg-blue-100 text-blue-800 border border-blue-300";
                loginRoleBadge.textContent = "Management";
            }
        } else {
            if (loginRoleDot) loginRoleDot.className = "w-2.5 h-2.5 rounded-full bg-emerald-500 transition-colors";
            if (loginRoleName) {
                loginRoleName.className = "text-emerald-700 dark:text-emerald-400 font-semibold";
                loginRoleName.textContent = "Viewer";
            }
            if (loginRoleBadge) {
                loginRoleBadge.className = "px-2 py-0.5 rounded text-[11px] font-mono bg-emerald-100 text-emerald-800 border border-emerald-300";
                loginRoleBadge.textContent = "Read-Only";
            }
        }
    }

    if (emailInput) {
        emailInput.addEventListener("input", updateLoginRole);
        updateLoginRole();
    }
});