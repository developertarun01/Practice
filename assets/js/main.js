// Form validation and submission
document.addEventListener('DOMContentLoaded', function () {
    const contactForm = document.getElementById('contactForm');

    if (contactForm) {
        contactForm.addEventListener('submit', handleFormSubmit);

        // Real-time validation
        document.getElementById('name').addEventListener('blur', validateName);
        document.getElementById('phone').addEventListener('blur', validatePhone);
        document.getElementById('service').addEventListener('change', validateService);
    }

    // Smooth scrolling for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href !== '#') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            }
        });
    });
});

// Form validation functions
function validateName() {
    const nameInput = document.getElementById('name');
    const nameError = document.getElementById('nameError');
    const value = nameInput.value.trim();

    if (!value) {
        nameError.textContent = 'Name is required';
        nameError.classList.add('error');
        return false;
    } else if (value.length < 3) {
        nameError.textContent = 'Name must be at least 3 characters';
        nameError.classList.add('error');
        return false;
    } else {
        nameError.textContent = '';
        nameError.classList.remove('error');
        return true;
    }
}

function validatePhone() {
    const phoneInput = document.getElementById('phone');
    const phoneError = document.getElementById('phoneError');
    const value = phoneInput.value.trim();

    if (!value) {
        phoneError.textContent = 'Phone number is required';
        phoneError.classList.add('error');
        return false;
    } else if (!/^\d{10}$/.test(value)) {
        phoneError.textContent = 'Phone number must be 10 digits';
        phoneError.classList.add('error');
        return false;
    } else {
        phoneError.textContent = '';
        phoneError.classList.remove('error');
        return true;
    }
}

function validateService() {
    const serviceSelect = document.getElementById('service');
    const serviceError = document.getElementById('serviceError');
    const value = serviceSelect.value;

    return true;

    // if (!value) {
    //     serviceError.textContent = 'Please select a service';
    //     serviceError.classList.add('error');
    //     return false;
    // } else {
    //     serviceError.textContent = '';
    //     serviceError.classList.remove('error');
    //     return true;
    // }
}

// Form submission handler
async function handleFormSubmit(e) {
    e.preventDefault();

    // Validate all fields
    const isNameValid = validateName();
    const isPhoneValid = validatePhone();
    const isServiceValid = validateService();

    if (!isNameValid || !isPhoneValid || !isServiceValid) {
        return;
    }

    const formData = new FormData(e.target);
    const messageElement = document.getElementById('formMessage');

    try {
        const response = await fetch('api/submit-lead.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {

            messageElement.style.display = "block";
            messageElement.textContent = '✓ Thank you! Our team will contact you within 2 hours.';
            messageElement.className = 'success';
            e.target.reset();

            // Clear error messages
            document.getElementById('nameError').textContent = '';
            document.getElementById('phoneError').textContent = '';
            // document.getElementById('serviceError').textContent = '';

            // Hide message after 5 seconds
            setTimeout(() => {
                messageElement.style.display = "none";
                // messageElement.className = 'form-message';
            }, 5000);
        } else {
            messageElement.textContent = '✗ ' + (data.message || 'Error submitting form. Please try again.');
            messageElement.className = 'error';
        }
    } catch (error) {
        console.error('Error:', error);
        console.log(error)
        messageElement.textContent = '✗ Network error. Please try again.';
        messageElement.className = 'error';
    }
}

// Admin panel functions
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active');
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
    }
}

// Close modal when clicking outside
window.addEventListener('click', function (event) {
    if (event.target.classList.contains('modal')) {
        event.target.classList.remove('active');
    }
});

// Admin filter functionality
function applyFilters(formId, tableId) {
    const form = document.getElementById(formId);
    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const filters = new FormData(form);
        const params = new URLSearchParams(filters);

        // Update table with filtered data
        fetch('?' + params.toString())
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTable = doc.getElementById(tableId);
                if (newTable) {
                    document.getElementById(tableId).innerHTML = newTable.innerHTML;
                }
            })
            .catch(error => console.error('Error:', error));
    });
}

// Date range filter
function initDateRangeFilters() {
    const fromDates = document.querySelectorAll('[data-date-from]');
    const toDates = document.querySelectorAll('[data-date-to]');

    fromDates.forEach(fromDate => {
        fromDate.addEventListener('change', function () {
            const toDate = document.querySelector(this.dataset.dateFrom);
            if (toDate && this.value > toDate.value) {
                toDate.value = this.value;
            }
        });
    });

    toDates.forEach(toDate => {
        toDate.addEventListener('change', function () {
            const fromDate = document.querySelector(this.dataset.dateTo);
            if (fromDate && this.value < fromDate.value) {
                fromDate.value = this.value;
            }
        });
    });
}

// Initialize admin panel
document.addEventListener('DOMContentLoaded', function () {
    initDateRangeFilters();

    // Set active menu item
    const currentUrl = window.location.pathname;
    document.querySelectorAll('.sidebar-menu a').forEach(link => {
        if (currentUrl.includes(link.getAttribute('href'))) {
            link.classList.add('active');
        }
    });

    // Auto-hide messages after 3 seconds
    const messages = document.querySelectorAll('[data-auto-hide]');
    messages.forEach(msg => {
        setTimeout(() => {
            msg.style.display = 'none';
        }, 3000);
    });
});
