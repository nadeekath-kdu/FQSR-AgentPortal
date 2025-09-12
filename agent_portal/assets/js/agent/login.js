document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('loginForm');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');

    // Validate email format
    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    // Form submission handler
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const email = emailInput.value.trim();
        const password = passwordInput.value.trim();

        if (!email || !password) {
            alert('Please fill in all fields');
            return;
        }

        if (!validateEmail(email)) {
            alert('Please enter a valid email address');
            return;
        }

        // Submit form using traditional POST (optional: use AJAX instead)
        form.submit(); // Or use fetch() to send to ../pages/system_login.php

        // Demo:
        // console.log('Submitting login:', { email, password });
        // alert('Login submitted. (Redirecting...)');
    });

    // Input focus/blur effects
    const inputs = document.querySelectorAll('.input');
    inputs.forEach(input => {
        input.addEventListener('focus', function () {
            this.parentElement.style.transform = 'translateY(-2px)';
        });
        input.addEventListener('blur', function () {
            this.parentElement.style.transform = 'translateY(0)';
        });
    });

    // Button hover effects
    const buttons = document.querySelectorAll('.signin-btn, .agent-btn');
    buttons.forEach(button => {
        button.addEventListener('mouseenter', () => button.style.transform = 'translateY(-2px)');
        button.addEventListener('mouseleave', () => button.style.transform = 'translateY(0)');
    });
});

// Optional: Toggle password visibility
function togglePasswordVisibility() {
    const passwordInput = document.getElementById('password');
    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordInput.setAttribute('type', type);
}
