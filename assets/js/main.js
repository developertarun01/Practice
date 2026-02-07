// ============================
// CONTACT FORM HANDLING
// ============================
document.addEventListener('DOMContentLoaded', function () {

    const forms = document.querySelectorAll('.contact-form');

    forms.forEach(form => {

        // Submit
        form.addEventListener('submit', handleFormSubmit);

        // Real-time validation
        form.querySelector('[name="name"]')
            ?.addEventListener('blur', () => validateName(form));

        form.querySelector('[name="phone"]')
            ?.addEventListener('blur', () => validatePhone(form));

        form.querySelector('[name="service"]')
            ?.addEventListener('change', () => validateService(form));
    });

    // Smooth scrolling
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const targetId = this.getAttribute('href');
            if (targetId !== '#') {
                const target = document.querySelector(targetId);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            }
        });
    });

    // Admin init
    initDateRangeFilters();
    setActiveMenu();
    autoHideMessages();
});

// ============================
// VALIDATION FUNCTIONS
// ============================
function validateName(form) {
    const input = form.querySelector('[name="name"]');
    const error = form.querySelector('#nameError');
    const value = input.value.trim();

    if (!value) {
        error.textContent = 'Name is required';
        error.classList.add('error');
        return false;
    }

    if (value.length < 3) {
        error.textContent = 'Name must be at least 3 characters';
        error.classList.add('error');
        return false;
    }

    error.textContent = '';
    error.classList.remove('error');
    return true;
}

function validatePhone(form) {
    const input = form.querySelector('[name="phone"]');
    const error = form.querySelector('#phoneError');
    const value = input.value.trim();

    if (!value) {
        error.textContent = 'Phone number is required';
        error.classList.add('error');
        return false;
    }

    if (!/^\d{10}$/.test(value)) {
        error.textContent = 'Phone number must be 10 digits';
        error.classList.add('error');
        return false;
    }

    error.textContent = '';
    error.classList.remove('error');
    return true;
}

function validateService(form) {
    // Validation intentionally disabled
    return true;
}

// ============================
// FORM SUBMIT HANDLER
// ============================
async function handleFormSubmit(e) {
    e.preventDefault();
    const form = e.target;

    if (
        !validateName(form) ||
        !validatePhone(form) ||
        !validateService(form)
    ) return;

    const formData = new FormData(form);
    const message = form.querySelector('#formMessage');

    try {
        const response = await fetch('api/submit-lead.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            message.style.display = 'block';
            message.textContent = '✓ Thank you! Our team will contact you within 2 hours.';
            message.classList.add('success');

            form.reset();

            // setTimeout(() => {
            //     message.style.display = 'none';
            // }, 5000);
        } else {
            message.textContent = data.message || 'Error submitting form';
            message.classList.add('error');
        }

    } catch (error) {
        console.error(error);
        message.textContent = 'Network error. Please try again.';
        message.classList.add('error');
    }
}

// ============================
// ADMIN MODAL FUNCTIONS
// ============================
function openModal(modalId) {
    document.getElementById(modalId)?.classList.add('active');
}

function closeModal(modalId) {
    document.getElementById(modalId)?.classList.remove('active');
}

window.addEventListener('click', function (e) {
    if (e.target.classList.contains('modal')) {
        e.target.classList.remove('active');
    }
});

// ============================
// ADMIN FILTERS
// ============================
function applyFilters(formId, tableId) {
    const form = document.getElementById(formId);
    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const params = new URLSearchParams(new FormData(form));

        fetch('?' + params.toString())
            .then(res => res.text())
            .then(html => {
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const newTable = doc.getElementById(tableId);
                if (newTable) {
                    document.getElementById(tableId).innerHTML = newTable.innerHTML;
                }
            })
            .catch(console.error);
    });
}

// ============================
// DATE RANGE FILTER
// ============================
function initDateRangeFilters() {
    document.querySelectorAll('[data-date-from]').forEach(from => {
        from.addEventListener('change', function () {
            const to = document.querySelector(this.dataset.dateFrom);
            if (to && this.value > to.value) to.value = this.value;
        });
    });

    document.querySelectorAll('[data-date-to]').forEach(to => {
        to.addEventListener('change', function () {
            const from = document.querySelector(this.dataset.dateTo);
            if (from && this.value < from.value) from.value = this.value;
        });
    });
}

// ============================
// ADMIN UI HELPERS
// ============================
function setActiveMenu() {
    const path = window.location.pathname;
    document.querySelectorAll('.sidebar-menu a').forEach(link => {
        if (path.includes(link.getAttribute('href'))) {
            link.classList.add('active');
        }
    });
}

function autoHideMessages() {
    document.querySelectorAll('[data-auto-hide]').forEach(msg => {
        setTimeout(() => {
            msg.style.display = 'none';
        }, 3000);
    });
}
