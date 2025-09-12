// Set current year
document.getElementById('currentYear').textContent = new Date().getFullYear();


document.getElementById('registrationForm').addEventListener('submit', function (e) {
    e.preventDefault();
    clearErrors();

    let isValid = true;
    const requiredFields = ['organisation', 'addressLine1', 'email', 'fullname', 'nic'];

    requiredFields.forEach(function (fieldId) {
        const field = document.getElementById(fieldId);
        const errorElement = document.getElementById(fieldId + 'Error');

        if (!field.value.trim()) {
            showError(fieldId, 'This field is required');
            isValid = false;
        }
    });


    const email = document.getElementById('email');
    if (email.value && !isValidEmail(email.value)) {
        showError('email', 'Please enter a valid email address');
        isValid = false;
    }

    if (isValid) {
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Registering...';
        submitBtn.classList.add('loading');
        setTimeout(function () {
            document.getElementById('successMessage').style.display = 'block';
            submitBtn.disabled = false;
            submitBtn.textContent = 'Register Agency';
            submitBtn.classList.remove('loading');
            document.getElementById('successMessage').scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }, 2000);

    }
});

function clearErrors() {
    const errorElements = document.querySelectorAll('.error-message');
    errorElements.forEach(function (element) {
        element.style.display = 'none';
        element.textContent = '';
    });

    const inputs = document.querySelectorAll('.form-input, .form-textarea');
    inputs.forEach(function (input) {
        input.style.borderColor = '#d1d5db';
    });
}

function showError(fieldId, message) {
    const field = document.getElementById(fieldId);
    const errorElement = document.getElementById(fieldId + 'Error');

    if (errorElement) {
        errorElement.textContent = message;
        errorElement.style.display = 'block';
    }

    field.style.borderColor = '#ef4444';
    field.focus();
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}


document.getElementById('document').addEventListener('change', function (e) {
    const files = e.target.files;
    let totalSize = 0;

    for (let i = 0; i < files.length; i++) {
        totalSize += files[i].size;
    }

    const maxSize = 5 * 1024 * 1024;
    if (totalSize > maxSize) {
        alert('Total file size exceeds 5MB limit. Please select smaller files.');
        e.target.value = '';
    }
});
const inputs = document.querySelectorAll('.form-input, .form-textarea, .file-input');
inputs.forEach(function (input) {
    input.addEventListener('focus', function () {
        this.style.transform = 'translateY(-1px)';
        this.style.boxShadow = '0 4px 12px rgba(59, 130, 246, 0.15)';
    });

    input.addEventListener('blur', function () {
        this.style.transform = 'translateY(0)';
        this.style.boxShadow = 'none';
    });
});