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
});