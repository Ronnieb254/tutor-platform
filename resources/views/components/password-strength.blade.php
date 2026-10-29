{{-- Password Strength Indicator Component --}}
<div class="password-strength-container" style="display: none;" id="password-strength-indicator">
    <div class="strength-bar-wrapper">
        <div class="strength-bar" id="strength-bar"></div>
    </div>
    <div class="strength-label" id="strength-label">Password strength</div>
    <div class="strength-requirements" id="strength-requirements">
        <div class="requirement" id="req-length">
            <span class="req-icon">○</span>
            <span class="req-text">At least 8 characters</span>
        </div>
        <div class="requirement" id="req-mixed">
            <span class="req-icon">○</span>
            <span class="req-text">Mixed case letters</span>
        </div>
        <div class="requirement" id="req-number">
            <span class="req-icon">○</span>
            <span class="req-text">At least one number</span>
        </div>
        <div class="requirement" id="req-special">
            <span class="req-icon">○</span>
            <span class="req-text">At least one special character</span>
        </div>
    </div>
</div>

<style>
.password-strength-container {
    margin-top: 12px;
    padding: 16px;
    background: rgba(242, 245, 242, 0.6);
    border-radius: 10px;
    border: 1px solid var(--hair);
}

.strength-bar-wrapper {
    width: 100%;
    height: 6px;
    background: rgba(21, 40, 31, 0.08);
    border-radius: 3px;
    overflow: hidden;
    margin-bottom: 10px;
}

.strength-bar {
    height: 100%;
    width: 0%;
    transition: all 0.3s ease;
    border-radius: 3px;
}

.strength-bar.weak {
    width: 33%;
    background: linear-gradient(90deg, #E8522A, #DC2626);
}

.strength-bar.medium {
    width: 66%;
    background: linear-gradient(90deg, #F59E0B, #D97706);
}

.strength-bar.strong {
    width: 100%;
    background: linear-gradient(90deg, #10B981, #059669);
}

.strength-label {
    font-size: 13px;
    font-weight: 600;
    color: var(--ink);
    margin-bottom: 12px;
}

.strength-label.weak {
    color: #E8522A;
}

.strength-label.medium {
    color: #F59E0B;
}

.strength-label.strong {
    color: #10B981;
}

.strength-requirements {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.requirement {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: var(--muted);
    transition: color 0.2s;
}

.requirement.met {
    color: #10B981;
}

.req-icon {
    font-size: 16px;
    font-weight: 700;
    width: 16px;
    height: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.requirement.met .req-icon {
    color: #10B981;
}

.requirement.met .req-icon::before {
    content: '✓';
}

.requirement:not(.met) .req-icon::before {
    content: '○';
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const strengthIndicator = document.getElementById('password-strength-indicator');
    const strengthBar = document.getElementById('strength-bar');
    const strengthLabel = document.getElementById('strength-label');
    
    const reqLength = document.getElementById('req-length');
    const reqMixed = document.getElementById('req-mixed');
    const reqNumber = document.getElementById('req-number');
    const reqSpecial = document.getElementById('req-special');
    
    if (!passwordInput) return;
    
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        
        // Show indicator when user starts typing
        if (password.length > 0) {
            strengthIndicator.style.display = 'block';
        } else {
            strengthIndicator.style.display = 'none';
            return;
        }
        
        // Check requirements
        const hasLength = password.length >= 8;
        const hasLower = /[a-z]/.test(password);
        const hasUpper = /[A-Z]/.test(password);
        const hasMixed = hasLower && hasUpper;
        const hasNumber = /\d/.test(password);
        const hasSpecial = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~`]/.test(password);
        
        // Update requirement indicators
        updateRequirement(reqLength, hasLength);
        updateRequirement(reqMixed, hasMixed);
        updateRequirement(reqNumber, hasNumber);
        updateRequirement(reqSpecial, hasSpecial);
        
        // Calculate strength
        let metCount = 0;
        if (hasLength) metCount++;
        if (hasMixed) metCount++;
        if (hasNumber) metCount++;
        if (hasSpecial) metCount++;
        
        // Update strength bar and label
        strengthBar.className = 'strength-bar';
        strengthLabel.className = 'strength-label';
        
        if (metCount === 4) {
            strengthBar.classList.add('strong');
            strengthLabel.classList.add('strong');
            strengthLabel.textContent = '✓ Strong password';
        } else if (metCount >= 2) {
            strengthBar.classList.add('medium');
            strengthLabel.classList.add('medium');
            strengthLabel.textContent = '⚠ Medium strength';
        } else {
            strengthBar.classList.add('weak');
            strengthLabel.classList.add('weak');
            strengthLabel.textContent = '✗ Weak password';
        }
    });
    
    function updateRequirement(element, isMet) {
        if (isMet) {
            element.classList.add('met');
        } else {
            element.classList.remove('met');
        }
    }
});
</script>
