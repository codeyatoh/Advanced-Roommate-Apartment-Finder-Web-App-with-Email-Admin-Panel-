/**
 * Login Page Logic
 * Handles login form submission and social login
 */

class LoginPage {
    constructor() {
        this.formData = {
            email: '',
            password: '',
            remember: false
        };

        this.init();
    }

    init() {
        this.form = document.getElementById('login-form');
        this.setupFormSubmission();
        this.setupSocialLogin();
    }

    setupFormSubmission() {
        if (!this.form) return;

        this.form.addEventListener('submit', (e) => {
            e.preventDefault();

            // Get form data
            const formData = new FormData(this.form);
            this.formData = {
                email: formData.get('email'),
                password: formData.get('password'),
                remember: formData.get('remember') === 'on'
            };

            // Validate form
            const validator = new window.FormValidator(this.form);
            validator.clearAllErrors();

            if (!validator.validateRequired(this.formData.email)) {
                validator.showError('email', 'Email is required');
            } else if (!validator.validateEmail(this.formData.email)) {
                validator.showError('email', 'Please enter a valid email');
            }

            if (!validator.validateRequired(this.formData.password)) {
                validator.showError('password', 'Password is required');
            }

            // If validation passes
            if (validator.isValid()) {
                console.log('Login data:', this.formData);
                // Here you would typically send the data to your backend
                alert('Login successful! (This is a demo)');
                // Redirect to dashboard
                // window.location.href = '/seeker/dashboard';
            }
        });
    }

    setupSocialLogin() {
        const googleBtn = document.getElementById('google-login');
        const facebookBtn = document.getElementById('facebook-login');

        if (googleBtn) {
            googleBtn.addEventListener('click', () => {
                console.log('Google login clicked');
                alert('Google login (This is a demo)');
                // Implement Google OAuth flow
            });
        }

        if (facebookBtn) {
            facebookBtn.addEventListener('click', () => {
                console.log('Facebook login clicked');
                alert('Facebook login (This is a demo)');
                // Implement Facebook OAuth flow
            });
        }
    }
}

// Initialize login page
function initLoginPage() {
    if (document.getElementById('login-page')) {
        new LoginPage();
    }
}

// Auto-initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initLoginPage);
} else {
    initLoginPage();
}
