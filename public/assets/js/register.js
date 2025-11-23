/**
 * Register Page Logic
 * Handles role selection, step transitions, and form submission
 */

class RegisterPage {
    constructor() {
        this.currentStep = 'role'; // 'role' or 'details'
        this.selectedRole = null;
        this.formData = {
            name: '',
            email: '',
            password: '',
            confirmPassword: '',
            terms: false
        };

        this.init();
    }

    init() {
        // Get DOM elements
        this.roleSelection = document.getElementById('role-selection');
        this.registrationForm = document.getElementById('registration-form');
        this.backButton = document.getElementById('back-to-role');
        this.form = document.getElementById('register-form');

        // Set up event listeners
        this.setupRoleSelection();
        this.setupBackButton();
        this.setupFormSubmission();

        // Show initial step
        this.showStep('role');
    }

    setupRoleSelection() {
        const seekerCard = document.getElementById('role-seeker');
        const landlordCard = document.getElementById('role-landlord');

        if (seekerCard) {
            seekerCard.addEventListener('click', () => this.selectRole('seeker'));
        }

        if (landlordCard) {
            landlordCard.addEventListener('click', () => this.selectRole('landlord'));
        }
    }

    selectRole(role) {
        this.selectedRole = role;
        this.showStep('details');

        // Update form header to show selected role
        const roleIcon = document.getElementById('form-role-icon');
        const roleTitle = document.getElementById('form-role-title');

        if (roleIcon && roleTitle) {
            if (role === 'seeker') {
                roleTitle.textContent = 'Room Seeker Account';
                // Update icon if needed
            } else {
                roleTitle.textContent = 'Landlord Account';
                // Update icon if needed
            }
        }
    }

    setupBackButton() {
        if (this.backButton) {
            this.backButton.addEventListener('click', () => {
                this.showStep('role');
            });
        }
    }

    showStep(step) {
        this.currentStep = step;

        if (step === 'role') {
            if (this.roleSelection) this.roleSelection.style.display = 'grid';
            if (this.registrationForm) this.registrationForm.style.display = 'none';
        } else {
            if (this.roleSelection) this.roleSelection.style.display = 'none';
            if (this.registrationForm) this.registrationForm.style.display = 'block';
        }
    }

    setupFormSubmission() {
        if (!this.form) return;

        this.form.addEventListener('submit', (e) => {
            e.preventDefault();

            // Get form data
            const formData = new FormData(this.form);
            this.formData = {
                name: formData.get('name'),
                email: formData.get('email'),
                password: formData.get('password'),
                confirmPassword: formData.get('confirmPassword'),
                terms: formData.get('terms') === 'on',
                role: this.selectedRole
            };

            // Validate form
            const validator = new window.FormValidator(this.form);
            validator.clearAllErrors();

            // Required fields
            if (!validator.validateRequired(this.formData.name)) {
                validator.showError('name', 'Name is required');
            }

            if (!validator.validateRequired(this.formData.email)) {
                validator.showError('email', 'Email is required');
            } else if (!validator.validateEmail(this.formData.email)) {
                validator.showError('email', 'Please enter a valid email');
            }

            if (!validator.validateRequired(this.formData.password)) {
                validator.showError('password', 'Password is required');
            } else if (!validator.validatePassword(this.formData.password)) {
                validator.showError('password', 'Password must be at least 8 characters');
            }

            if (!validator.validatePasswordMatch(this.formData.password, this.formData.confirmPassword)) {
                validator.showError('confirmPassword', 'Passwords do not match');
            }

            if (!this.formData.terms) {
                alert('Please accept the Terms of Service and Privacy Policy');
                return;
            }

            // If validation passes
            if (validator.isValid()) {
                console.log('Registration data:', this.formData);
                // Here you would typically send the data to your backend
                alert('Registration successful! (This is a demo)');
            }
        });
    }
}

// Initialize register page
function initRegisterPage() {
    if (document.getElementById('register-page')) {
        new RegisterPage();
    }
}

// Auto-initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initRegisterPage);
} else {
    initRegisterPage();
}
