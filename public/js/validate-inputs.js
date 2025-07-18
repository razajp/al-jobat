function validateInput(input) {
    const rules = (input.dataset.validate || '').split('|');
    let value = input.value;
    const originalValue = value;
    let error = '';

    rules.forEach(rule => {
        if (rule === 'required' && value.trim() === '') {
            error = 'This field is required.';
        }

        if (rule === 'lowercase') {
            value = value.toLowerCase();
        }

        if (rule === 'alphanumeric') {
            value = value.replace(/[^a-z0-9]/gi, '');
        }

        if (rule === 'letters') {
            value = value.replace(/[^a-zA-Z ]/g, '');
        }

        if (rule === 'numeric') {
            value = value.replace(/[^0-9.]/g, '');
        }

        // friendly = allows letters, numbers, space, dot, dash
        if (rule === 'friendly') {
            value = value.replace(/[^a-zA-Z0-9 .-]/g, '');
        }

        // phone = auto-format to 0000-0000000
        if (rule === 'phone') {
            value = value.replace(/\D/g, '');
            if (value.length > 11) value = value.substring(0, 11);
            if (value.length >= 5) {
                value = value.substring(0, 4) + '-' + value.substring(4);
            }
            if (!/^\d{4}-\d{7}$/.test(value)) {
                error = 'Phone number must be in format 0000-0000000';
            }
        }

        // urdu = only Urdu letters, Urdu punctuation, and spaces
        if (rule === 'urdu') {
            value = value.replace(/[^\u0600-\u06FF\s،۔!?؟]/g, '');
            if (!/[\u0600-\u06FF]/.test(value)) {
                error = 'Please enter in Urdu only.';
            }
        }

        if (rule.startsWith('min:')) {
            const min = parseInt(rule.split(':')[1]);
            if (value.length < min) {
                error = `Minimum ${min} characters required.`;
            }
        }

        if (rule.startsWith('max:')) {
            const max = parseInt(rule.split(':')[1]);
            if (parseFloat(value) > max) {
                error = `Maximum allowed value is ${max}.`;
                value = max;
            }
        }

        // if (rule.startsWith('unique:')) {
        //     const field = rule.split(':')[1];
        //     if (typeof window[field + 's'] !== 'undefined') {
        //         const dataset = window[field + 's'];
        //         if (Array.isArray(dataset) && dataset.some(item => item[field] === value)) {
        //             error = `${field.replace('_', ' ')} is already taken.`;
        //         }
        //     }
        // }

        if (rule.startsWith('unique:')) {
            const field = rule.split(':')[1];
            const dataset = window[field + 's'];
            if (Array.isArray(dataset) && dataset.includes(value)) {
                error = `${field} is already taken.`;
            }
        }
    });

    input.value = value;

    const errorEl = document.getElementById(`${input.name}-error`);
    if (error) {
        input.classList.add("border-[var(--border-error)]");
        errorEl?.classList.remove("hidden");
        errorEl.textContent = error;
        console.log(error);
        return false;
    } else {
        input.classList.remove("border-[var(--border-error)]");
        errorEl?.classList.add("hidden");
        errorEl.textContent = '';
        return true;
    }
}

// Attach validation to every input field with data-validate
window.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-validate]').forEach(input => {
        input.addEventListener('input', () => validateInput(input));
        input.addEventListener('blur', () => validateInput(input));
    });
});

function validateAllInputs() {
    let valid = true;
    document.querySelectorAll('[data-validate]').forEach(input => {
        if (!validateInput(input)) valid = false;
    });
    return valid;
}
