/**
 * StudentHub - Practical 5: Comprehensive Frontend Form Validation & CAPTCHA
 */

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('studentRegForm');
    const nameInput = document.getElementById('fullName');
    const emailInput = document.getElementById('email');
    const mobileInput = document.getElementById('mobile');
    const courseSelect = document.getElementById('course');
    const yearSelect = document.getElementById('year');
    const passwordInput = document.getElementById('password');
    const confirmPassInput = document.getElementById('confirmPassword');
    const termsCheckbox = document.getElementById('terms');
    const captchaInput = document.getElementById('captchaInput');
    const refreshCaptchaBtn = document.getElementById('refreshCaptchaBtn');
    const successBanner = document.getElementById('successBanner');

    let generatedCaptcha = '';

    // Initialize Canvas CAPTCHA
    generateCaptcha();
    if (refreshCaptchaBtn) {
        refreshCaptchaBtn.addEventListener('click', generateCaptcha);
    }

    // Real-time Event Listeners
    nameInput.addEventListener('input', () => validateName(true));
    emailInput.addEventListener('input', () => validateEmail(true));
    mobileInput.addEventListener('input', () => validateMobile(true));
    courseSelect.addEventListener('change', () => validateCourse(true));
    yearSelect.addEventListener('change', () => validateYear(true));
    passwordInput.addEventListener('input', () => {
        updatePasswordStrength(passwordInput.value);
        validatePassword(true);
        if (confirmPassInput.value) validateConfirmPassword(true);
    });
    confirmPassInput.addEventListener('input', () => validateConfirmPassword(true));
    captchaInput.addEventListener('input', () => validateCaptcha(true));

    // Form Submission
    form.addEventListener('submit', (e) => {
        e.preventDefault();

        const isNameValid = validateName();
        const isEmailValid = validateEmail();
        const isMobileValid = validateMobile();
        const isCourseValid = validateCourse();
        const isYearValid = validateYear();
        const isGenderValid = validateGender();
        const isPasswordValid = validatePassword();
        const isConfirmValid = validateConfirmPassword();
        const isCaptchaValid = validateCaptcha();
        const isTermsValid = validateTerms();

        if (isNameValid && isEmailValid && isMobileValid && isCourseValid && 
            isYearValid && isGenderValid && isPasswordValid && isConfirmValid && 
            isCaptchaValid && isTermsValid) {
            
            // Show Success Notification
            successBanner.style.display = 'block';
            successBanner.scrollIntoView({ behavior: 'smooth' });
            form.reset();
            resetValidationFeedback();
            generateCaptcha();
        } else {
            successBanner.style.display = 'none';
        }
    });

    // Helper functions for feedback
    function setFieldState(inputEl, isValid, message = '') {
        const group = inputEl.closest('.form-group');
        if (!group) return;
        const errEl = group.querySelector('.error-message');

        if (isValid) {
            group.classList.remove('has-error');
            group.classList.add('has-success');
            inputEl.setAttribute('aria-invalid', 'false');
            if (errEl) errEl.style.display = 'none';
        } else {
            group.classList.remove('has-success');
            group.classList.add('has-error');
            inputEl.setAttribute('aria-invalid', 'true');
            if (errEl) {
                errEl.textContent = message;
                errEl.style.display = 'block';
            }
        }
    }

    function resetValidationFeedback() {
        document.querySelectorAll('.form-group').forEach(grp => {
            grp.classList.remove('has-error', 'has-success');
            const err = grp.querySelector('.error-message');
            if (err) err.style.display = 'none';
        });
        updatePasswordStrength('');
    }

    // 1. Full Name Validation (3-50 letters and spaces)
    function validateName() {
        const val = nameInput.value.trim();
        const regex = /^[a-zA-Z\s]{3,50}$/;
        if (!val) {
            setFieldState(nameInput, false, 'Full name is required.');
            return false;
        } else if (!regex.test(val)) {
            setFieldState(nameInput, false, 'Name must contain 3-50 alphabetic characters only.');
            return false;
        }
        setFieldState(nameInput, true);
        return true;
    }

    // 2. Email Validation
    function validateEmail() {
        const val = emailInput.value.trim();
        const regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        if (!val) {
            setFieldState(emailInput, false, 'Email address is required.');
            return false;
        } else if (!regex.test(val)) {
            setFieldState(emailInput, false, 'Please enter a valid email address (e.g., student@charusat.edu.in).');
            return false;
        }
        setFieldState(emailInput, true);
        return true;
    }

    // 3. Mobile Number Validation (10 digits starting with 6-9)
    function validateMobile() {
        const val = mobileInput.value.trim();
        const regex = /^[6-9]\d{9}$/;
        if (!val) {
            setFieldState(mobileInput, false, 'Mobile number is required.');
            return false;
        } else if (!regex.test(val)) {
            setFieldState(mobileInput, false, 'Must be a valid 10-digit Indian mobile number starting with 6-9.');
            return false;
        }
        setFieldState(mobileInput, true);
        return true;
    }

    // 4. Course Selection Validation
    function validateCourse() {
        if (!courseSelect.value) {
            setFieldState(courseSelect, false, 'Please select your academic course.');
            return false;
        }
        setFieldState(courseSelect, true);
        return true;
    }

    // 5. Year Selection Validation
    function validateYear() {
        if (!yearSelect.value) {
            setFieldState(yearSelect, false, 'Please select your current year of study.');
            return false;
        }
        setFieldState(yearSelect, true);
        return true;
    }

    // 6. Gender Selection Validation
    function validateGender() {
        const selected = document.querySelector('input[name="gender"]:checked');
        const genderContainer = document.getElementById('genderContainer');
        const errEl = genderContainer.querySelector('.error-message');

        if (!selected) {
            genderContainer.classList.add('has-error');
            if (errEl) {
                errEl.textContent = 'Please choose your gender.';
                errEl.style.display = 'block';
            }
            return false;
        }
        genderContainer.classList.remove('has-error');
        genderContainer.classList.add('has-success');
        if (errEl) errEl.style.display = 'none';
        return true;
    }

    // 7. Password Validation & Strength Meter
    function validatePassword() {
        const val = passwordInput.value;
        const hasLength = val.length >= 8;
        const hasUpper = /[A-Z]/.test(val);
        const hasLower = /[a-z]/.test(val);
        const hasNumber = /[0-9]/.test(val);
        const hasSpecial = /[@$!%*?&#^_\-]/.test(val);

        if (!val) {
            setFieldState(passwordInput, false, 'Password is required.');
            return false;
        } else if (!(hasLength && hasUpper && hasLower && hasNumber && hasSpecial)) {
            setFieldState(passwordInput, false, 'Password must fulfill all security criteria below.');
            return false;
        }
        setFieldState(passwordInput, true);
        return true;
    }

    function updatePasswordStrength(val) {
        const fill = document.getElementById('strengthBarFill');
        const text = document.getElementById('strengthText');
        const ruleLen = document.getElementById('rule-len');
        const ruleUpper = document.getElementById('rule-upper');
        const ruleNum = document.getElementById('rule-num');
        const ruleSpec = document.getElementById('rule-spec');

        const hasLen = val.length >= 8;
        const hasUpper = /[A-Z]/.test(val);
        const hasNum = /[0-9]/.test(val);
        const hasSpec = /[@$!%*?&#^_\-]/.test(val);

        if (ruleLen) ruleLen.classList.toggle('valid', hasLen);
        if (ruleUpper) ruleUpper.classList.toggle('valid', hasUpper);
        if (ruleNum) ruleNum.classList.toggle('valid', hasNum);
        if (ruleSpec) ruleSpec.classList.toggle('valid', hasSpec);

        let score = 0;
        if (hasLen) score += 25;
        if (hasUpper) score += 25;
        if (hasNum) score += 25;
        if (hasSpec) score += 25;

        fill.style.width = score + '%';

        if (score === 0) {
            fill.style.backgroundColor = '#e2e8f0';
            text.textContent = 'Password Strength: Not Entered';
            text.style.color = '#64748b';
        } else if (score <= 50) {
            fill.style.backgroundColor = '#dc2626';
            text.textContent = 'Password Strength: Weak';
            text.style.color = '#dc2626';
        } else if (score === 75) {
            fill.style.backgroundColor = '#f59e0b';
            text.textContent = 'Password Strength: Medium';
            text.style.color = '#f59e0b';
        } else {
            fill.style.backgroundColor = '#16a34a';
            text.textContent = 'Password Strength: Strong';
            text.style.color = '#16a34a';
        }
    }

    // 8. Confirm Password Matching
    function validateConfirmPassword() {
        const pass = passwordInput.value;
        const confirm = confirmPassInput.value;

        if (!confirm) {
            setFieldState(confirmPassInput, false, 'Please confirm your password.');
            return false;
        } else if (pass !== confirm) {
            setFieldState(confirmPassInput, false, 'Passwords do not match.');
            return false;
        }
        setFieldState(confirmPassInput, true);
        return true;
    }

    // 9. Terms Acceptance Checkbox
    function validateTerms() {
        const termsContainer = document.getElementById('termsContainer');
        const errEl = termsContainer.querySelector('.error-message');

        if (!termsCheckbox.checked) {
            termsContainer.classList.add('has-error');
            if (errEl) {
                errEl.textContent = 'You must accept the terms and conditions.';
                errEl.style.display = 'block';
            }
            return false;
        }
        termsContainer.classList.remove('has-error');
        if (errEl) errEl.style.display = 'none';
        return true;
    }

    // 10. Canvas CAPTCHA
    function generateCaptcha() {
        const canvas = document.getElementById('captchaCanvas');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        const chars = '23456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz';
        generatedCaptcha = '';
        for (let i = 0; i < 6; i++) {
            generatedCaptcha += chars.charAt(Math.floor(Math.random() * chars.length));
        }

        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.fillStyle = '#f8fafc';
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        // Background noise lines
        for (let i = 0; i < 4; i++) {
            ctx.strokeStyle = `rgba(${Math.floor(Math.random()*200)}, ${Math.floor(Math.random()*200)}, ${Math.floor(Math.random()*200)}, 0.4)`;
            ctx.beginPath();
            ctx.moveTo(Math.random() * canvas.width, Math.random() * canvas.height);
            ctx.lineTo(Math.random() * canvas.width, Math.random() * canvas.height);
            ctx.stroke();
        }

        // Render characters with distortion
        ctx.font = 'bold 22px system-ui';
        for (let i = 0; i < generatedCaptcha.length; i++) {
            ctx.fillStyle = '#1e3a8a';
            ctx.save();
            ctx.translate(20 + i * 20, 28);
            ctx.rotate((Math.random() - 0.5) * 0.4);
            ctx.fillText(generatedCaptcha[i], 0, 0);
            ctx.restore();
        }

        captchaInput.value = '';
        setFieldState(captchaInput, true);
    }

    function validateCaptcha() {
        const val = captchaInput.value.trim();
        if (!val) {
            setFieldState(captchaInput, false, 'Please enter the CAPTCHA characters.');
            return false;
        } else if (val !== generatedCaptcha) {
            setFieldState(captchaInput, false, 'Incorrect CAPTCHA. Please try again.');
            return false;
        }
        setFieldState(captchaInput, true);
        return true;
    }
});
