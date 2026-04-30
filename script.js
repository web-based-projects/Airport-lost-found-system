document.addEventListener('DOMContentLoaded', () => {
    // Initialize date pickers to not allow future dates for lost/found items
    initDatePickers();
});

function toggleMobileMenu() {
    const navLinks = document.querySelector('.nav-links');
    if(navLinks) {
        navLinks.classList.toggle('show');
    }
}

function initDatePickers() {
    const dateInputs = document.querySelectorAll('input[type="date"]');
    if (dateInputs.length > 0) {
        const today = new Date().toISOString().split('T')[0];
        dateInputs.forEach(input => {
            input.setAttribute('max', today);
        });
    }
}

function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function validatePhone(phone) {
    const re = /^\d{10,}$/;
    return re.test(phone.replace(/\D/g,''));
}

function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return true;

    let isValid = true;
    const requiredFields = form.querySelectorAll('[required]');

    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            isValid = false;
            field.classList.add('error');
        } else {
            field.classList.remove('error');
        }
    });

    const emailField = form.querySelector('input[type="email"]');
    if (emailField && emailField.value && !validateEmail(emailField.value)) {
        isValid = false;
        alert('Please enter a valid email address.');
        return false;
    }

    const phoneField = form.querySelector('input[type="tel"]');
    if (phoneField && phoneField.value && !validatePhone(phoneField.value)) {
        isValid = false;
        alert('Please enter a valid phone number (at least 10 digits).');
        return false;
    }

    if (!isValid) {
        alert('Please fill in all required fields correctly.');
    }

    return isValid;
}

function copyToClipboard(elementId) {
    const textElement = document.getElementById(elementId);
    if (!textElement) return;

    const textToCopy = textElement.innerText || textElement.textContent;
    
    navigator.clipboard.writeText(textToCopy).then(() => {
        showAlert('Code copied to clipboard!', 'success');
    }).catch(err => {
        console.error('Failed to copy: ', err);
        showAlert('Failed to copy code.', 'error');
    });
}

function previewImage(inputElement, previewElementId) {
    const preview = document.getElementById(previewElementId);
    if (!preview) return;

    if (inputElement.files && inputElement.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        
        reader.readAsDataURL(inputElement.files[0]);
    } else {
        preview.src = '#';
        preview.style.display = 'none';
    }
}

function filterTable(inputId, tableId) {
    const input = document.getElementById(inputId);
    const filter = input.value.toUpperCase();
    const table = document.getElementById(tableId);
    if(!table) return;
    
    const tr = table.getElementsByTagName("tr");

    for (let i = 1; i < tr.length; i++) {
        let textValue = tr[i].textContent || tr[i].innerText;
        if (textValue.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = "";
        } else {
            tr[i].style.display = "none";
        }
    }
}

function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.textContent = message;
    alertDiv.style.position = 'fixed';
    alertDiv.style.top = '20px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '9999';
    alertDiv.style.minWidth = '250px';
    alertDiv.style.boxShadow = '0 4px 6px rgba(0,0,0,0.1)';
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        alertDiv.style.opacity = '0';
        alertDiv.style.transition = 'opacity 0.5s ease';
        setTimeout(() => alertDiv.remove(), 500);
    }, 3000);
}

function confirmAction(message) {
    return confirm(message || 'Are you sure you want to perform this action?');
}
