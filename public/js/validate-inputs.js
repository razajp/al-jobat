function validateInput(input, listner) {
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
            if (/[^a-z0-9]/gi.test(value)) {
                error = 'Only letters and numbers are allowed.';
            }
            value = value.replace(/[^a-z0-9]/gi, '');
        }

        if (rule === 'letters') {
            value = value.replace(/[^a-zA-Z ]/g, '');
        }

        if (rule === 'numeric') {
            if (/[^0-9.]/g.test(value)) {
                error = 'Only numbers are allowed.';
            }
            value = value.replace(/[^0-9.]/g, '');
        }

        // friendly = allows letters, numbers, space, dot, dash
        if (rule === 'friendly') {
            if (/[^a-zA-Z0-9 .-|]/g.test(value)) {
                error = 'Only letters, numbers, space, dot, dash, and pipe are allowed.';
            }
            value = value.replace(/[^a-zA-Z0-9 .-|]/g, '');
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

        // urdu = only Urdu letters, Urdu & English numbers, Urdu punctuation, and spaces
        if (rule === 'urdu') {
            // Allow: Urdu letters (\u0600-\u06FF), Urdu digits (\u06F0-\u06F9), English digits (0-9), spaces, and Urdu punctuation
            value = value.replace(/[^\u0600-\u06FF\u06F0-\u06F90-9\s،۔!?؟]/g, '');

            // Check if at least one Urdu letter or number exists
            if (!/[\u0600-\u06FF\u06F0-\u06F90-9]/.test(value)) {
                error = 'Please enter in Urdu only.';
            }
        }

        if (rule === 'amount') {
            // remove non-numeric characters except dot (for decimals)
            value = value.replace(/[^0-9.]/g, '');

            if (value) {
                const parts = value.split('.');

                // format integer part with commas
                parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

                // limit decimal part to max 2 digits
                if (parts[1]) {
                    parts[1] = parts[1].slice(0, 2);
                }

                value = parts.join('.');
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
        if (errorEl) {
            errorEl.classList.remove("hidden");
            errorEl.textContent = error;
        }
        return false;
    } else {
        input.classList.remove("border-[var(--border-error)]");
        if (errorEl) {
            errorEl.classList.add("hidden");
            errorEl.textContent = '';
        }
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
