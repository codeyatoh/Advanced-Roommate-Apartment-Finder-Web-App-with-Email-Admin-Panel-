/**
 * Form Utilities
 * Handles password visibility toggle, validation, and form submission
 */

// Password Toggle Functionality
function initPasswordToggles() {
    const toggleButtons = document.querySelectorAll('.password-toggle');

    toggleButtons.forEach(button => {
        button.addEventListener('click', function () {
            const input = this.closest('.form-input-wrapper').querySelector('.form-input');
            const eyeIcon = this.querySelector('.eye-icon');
            const eyeOffIcon = this.querySelector('.eye-off-icon');

            if (input.type === 'password') {
                input.type = 'text';
                if (eyeIcon) eyeIcon.style.display = 'none';
                if (eyeOffIcon) eyeOffIcon.style.display = 'block';
            } else {
                input.type = 'password';
                if (eyeIcon) eyeIcon.style.display = 'block';
                if (eyeOffIcon) eyeOffIcon.style.display = 'none';
            }
        });
    });
}

// Form Validation
class FormValidator {
    constructor(form) {
        this.form = form;
        this.errors = {};
    }

    validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    validatePassword(password) {
        // At least 8 characters
        return password.length >= 8;
    }

    validateRequired(value) {
        return value.trim() !== '';
    }

    validatePasswordMatch(password, confirmPassword) {
        return password === confirmPassword;
    }

    showError(inputName, message) {
        const input = this.form.querySelector(`[name="${inputName}"]`);
        if (!input) return;

        // Add error class to input
        input.classList.add('form-input-error');

        // Create or update error message
        let errorElement = input.parentElement.querySelector('.form-error');
        if (!errorElement) {
            errorElement = document.createElement('div');
            errorElement.className = 'form-error';
            input.parentElement.appendChild(errorElement);
        }
        errorElement.textContent = message;

        this.errors[inputName] = message;
    }

    clearError(inputName) {
        const input = this.form.querySelector(`[name="${inputName}"]`);
        if (!input) return;

        // Remove error class
        input.classList.remove('form-input-error');

        // Remove error message
        const errorElement = input.parentElement.querySelector('.form-error');
        if (errorElement) {
            errorElement.remove();
        }

        delete this.errors[inputName];
    }

    clearAllErrors() {
        Object.keys(this.errors).forEach(inputName => {
            this.clearError(inputName);
        });
    }

    isValid() {
        return Object.keys(this.errors).length === 0;
    }
}

// Real-time validation for password matching
function initPasswordMatchValidation(form) {
    const password = form.querySelector('[name="password"]');
    const confirmPassword = form.querySelector('[name="confirmPassword"]');

    if (!password || !confirmPassword) return;

    const validator = new FormValidator(form);

    confirmPassword.addEventListener('input', function () {
        if (this.value === '') {
            validator.clearError('confirmPassword');
            return;
        }

        if (password.value !== this.value) {
            validator.showError('confirmPassword', 'Passwords do not match');
        } else {
            validator.clearError('confirmPassword');
        }
    });

    password.addEventListener('input', function () {
        if (confirmPassword.value !== '' && this.value !== confirmPassword.value) {
            validator.showError('confirmPassword', 'Passwords do not match');
        } else if (confirmPassword.value !== '' && this.value === confirmPassword.value) {
            validator.clearError('confirmPassword');
        }
    });
}

// Initialize all form utilities
function initForms() {
    initPasswordToggles();

    // Initialize password match validation for all forms
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        initPasswordMatchValidation(form);
    });
}

// Auto-initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initForms);
} else {
    initForms();
}

// Export for use in other modules
window.FormValidator = FormValidator;
