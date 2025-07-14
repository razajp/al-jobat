// js/validate-inputs.js

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

        if (rule.startsWith('min:')) {
            const min = parseInt(rule.split(':')[1]);
            if (value.length < min) {
                error = `Minimum ${min} characters required.`;
            }
        }

        if (rule.startsWith('unique:')) {
            const field = rule.split(':')[1];
            if (typeof window[field + 's'] !== 'undefined') {
                const dataset = window[field + 's'];
                if (Array.isArray(dataset) && dataset.some(item => item[field] === value)) {
                    error = `${field.replace('_', ' ')} is already taken.`;
                }
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