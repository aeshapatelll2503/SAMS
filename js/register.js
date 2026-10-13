document.addEventListener("DOMContentLoaded", () => {
  // Theme Toggle
  const themeToggle = document.getElementById("themeToggle");
  const themeIcon = document.querySelector(
    "#themeToggle .material-symbols-outlined",
  );

  if (themeToggle) {
    const savedTheme = localStorage.getItem("theme") || "light";

    document.documentElement.setAttribute("data-theme", savedTheme);

    themeIcon.textContent = savedTheme === "dark" ? "light_mode" : "dark_mode";

    themeToggle.addEventListener("click", () => {
      const currentTheme = document.documentElement.getAttribute("data-theme");

      const newTheme = currentTheme === "dark" ? "light" : "dark";

      document.documentElement.setAttribute("data-theme", newTheme);

      localStorage.setItem("theme", newTheme);

      themeIcon.textContent = newTheme === "dark" ? "light_mode" : "dark_mode";
    });
  }

  // Password Show / Hide
const passwordInput = document.getElementById("password");
const passwordToggle = document.getElementById("passwordToggle");
const passwordIcon = document.getElementById("passwordIcon");


if (passwordToggle) {
  passwordToggle.addEventListener("click", () => {
    if (passwordInput.type === "password") {
      passwordInput.type = "text";
      passwordIcon.textContent = "visibility";     // Pass dikh raha hai -> Eye Open
    } else {
      passwordInput.type = "password";
      passwordIcon.textContent = "visibility_off"; // Pass chhip gaya -> Eye Off
    }
  });
}

  // Password Strength
  const bars = [
    document.getElementById("bar-1"),
    document.getElementById("bar-2"),
    document.getElementById("bar-3"),
    document.getElementById("bar-4"),
  ];

  const strengthText = document.getElementById("strengthText");

  if (passwordInput) {
    passwordInput.addEventListener("input", function () {
      const pass = this.value;

      let score = 0;

      if (pass.length >= 6) score++;
      if (/[A-Z]/.test(pass)) score++;
      if (/[0-9]/.test(pass)) score++;
      if (/[^A-Za-z0-9]/.test(pass)) score++;

      bars.forEach((bar) => {
        bar.className = "strength-bar";
      });

      const colors = [
        "strength-weak",
        "strength-fair",
        "strength-good",
        "strength-excellent",
      ];

      for (let i = 0; i < score; i++) {
        bars[i].classList.add(colors[score - 1]);
      }

      const names = ["", "Weak", "Fair", "Good", "Excellent"];

      strengthText.textContent =
        score === 0 ? "Strength:" : "Strength: " + names[score];
    });
  }

  // Real-time Role Detection on Email Input
  const emailInput = document.getElementById("email");
  const rolePill = document.getElementById("rolePill");
  const roleIcon = document.getElementById("roleIcon");
  const roleLabel = document.getElementById("roleLabel");
  const roleDesc = document.getElementById("roleDesc");

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

  function updateRoleUI() {
    if (!emailInput || !rolePill) return;
    const role = detectRole(emailInput.value);

    rolePill.className = "role-pill";
    if (role === 'admin') {
      rolePill.classList.add('role-pill-admin');
      if (roleIcon) roleIcon.textContent = 'shield';
      if (roleLabel) roleLabel.textContent = 'Admin Role';
      if (roleDesc) roleDesc.textContent = 'Admin email detected — Full administrative & editing permissions.';
    } else if (role === 'manager') {
      rolePill.classList.add('role-pill-manager');
      if (roleIcon) roleIcon.textContent = 'manage_accounts';
      if (roleLabel) roleLabel.textContent = 'Manager Role';
      if (roleDesc) roleDesc.textContent = 'Manager email detected — Hierarchy design & role mapping permissions.';
    } else {
      rolePill.classList.add('role-pill-viewer');
      if (roleIcon) roleIcon.textContent = 'visibility';
      if (roleLabel) roleLabel.textContent = 'Viewer Role';
      if (roleDesc) roleDesc.textContent = 'Simple email detected — Standard read-only viewing permissions.';
    }
  }

  if (emailInput) {
    emailInput.addEventListener("input", updateRoleUI);
    updateRoleUI();
  }

  // Form Validation
  const form = document.getElementById("registrationForm");

  form.addEventListener("submit", function (e) {
    const fullName = document.getElementById("fullName").value.trim();
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();
    const terms = document.getElementById("terms").checked;

    if (fullName === "") {
      alert("Enter Full Name");
      e.preventDefault();
      return;
    }

    if (email === "") {
      alert("Enter Email");
      e.preventDefault();
      return;
    }

    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!emailPattern.test(email)) {
      alert("Enter Valid Email");
      e.preventDefault();
      return;
    }

    if (password.length < 6) {
      alert("Password must be at least 6 characters");
      e.preventDefault();
      return;
    }

    if (!terms) {
      alert("Please accept Terms & Conditions");
      e.preventDefault();
      return;
    }

    const submitBtn = document.querySelector(".submit-button");

    submitBtn.disabled = true;

    submitBtn.innerHTML = `
            <span>Creating Account...</span>
        `;

    // No e.preventDefault() here
    // Form will automatically submit to api/register.php
  });
});
