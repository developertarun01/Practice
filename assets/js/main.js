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
        const response = await fetch('../api/submit-lead.php', {
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
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
        // Remove the modal from DOM after closing animation
        setTimeout(() => {
            if (modal && modal.parentNode) {
                modal.remove();
            }
        }, 300);
    }
}

function removeAllModals() {
    document.querySelectorAll('.modal.active').forEach(modal => {
        modal.remove();
    });
}

window.addEventListener('click', function (e) {
    if (e.target.classList.contains('modal')) {
        e.target.classList.remove('active');
        setTimeout(() => {
            if (e.target && e.target.parentNode) {
                e.target.remove();
            }
        }, 300);
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
        link.classList.remove('active');
        const href = link.getAttribute('href');
        if (path.includes(href)) {
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

// ============================
// MODAL AND FORM HELPERS
// ============================
function openNewBookingModal() {
    const html = `
        <div id="newBookingModal" class="modal active">
            <div class="modal-content">
                <span class="close" onclick="closeModal('newBookingModal')">&times;</span>
                <h2>Create New Booking</h2>
                <form id="newBookingForm" onsubmit="handleCreateBooking(event)">
                    <div class="form-group">
                        <label>Customer Name *</label>
                        <input type="text" name="customer_name" required>
                    </div>
                    <div class="form-group">
                        <label>Customer Email</label>
                        <input type="email" name="customer_email">
                    </div>
                    <div class="form-group">
                        <label>Customer Phone *</label>
                        <input type="tel" name="customer_phone" pattern="[0-9]{10}" required>
                    </div>
                    <div class="form-group">
                        <label>Service *</label>
                        <select name="service" required>
                            <option value="">Select Service</option>
                            <option value="All Rounder">All Rounder</option>
                            <option value="Baby Caretaker">Baby Caretaker</option>
                            <option value="Cooking Maid">Cooking Maid</option>
                            <option value="House Maid">House Maid</option>
                            <option value="Elderly Care">Elderly Care</option>
                            <option value="Security Guard">Security Guard</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Full Address</label>
                        <textarea name="full_address" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Starts At</label>
                        <input type="datetime-local" name="starts_at">
                    </div>
                    <div class="form-group">
                        <label>Job Hours</label>
                        <input type="number" name="job_hours" min="1">
                    </div>
                    <div class="form-group">
                        <label>Salary Bracket</label>
                        <input type="text" name="salary_bracket" placeholder="e.g., 10000-15000">
                    </div>
                    <button type="submit" class="btn btn-primary">Create Booking</button>
                </form>
            </div>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', html);
}

function openNewPaymentModal() {
    const html = `
        <div id="newPaymentModal" class="modal active">
            <div class="modal-content">
                <span class="close" onclick="closeModal('newPaymentModal')">&times;</span>
                <h2>Create Payment Link</h2>
                <form id="newPaymentForm" onsubmit="handleCreatePayment(event)">
                    <div class="form-group">
                        <label>Customer Name *</label>
                        <input type="text" name="customer_name" required>
                    </div>
                    <div class="form-group">
                        <label>Customer Email *</label>
                        <input type="email" name="customer_email" required>
                    </div>
                    <div class="form-group">
                        <label>Customer Phone *</label>
                        <input type="tel" name="customer_phone" pattern="[0-9]{10}" required>
                    </div>
                    <div class="form-group">
                        <label>Service</label>
                        <input type="text" name="service" placeholder="Service name">
                    </div>
                    <div class="form-group">
                        <label>Purpose *</label>
                        <input type="text" name="purpose" required placeholder="e.g., Monthly payment">
                    </div>
                    <div class="form-group">
                        <label>Amount (₹) *</label>
                        <input type="number" name="total_amount" step="0.01" min="0" required>
                    </div>
                    <div class="form-group">
                        <label>Payment Method</label>
                        <select name="payment_method">
                            <option value="">Select Method</option>
                            <option value="UPI">UPI</option>
                            <option value="Card">Card</option>
                            <option value="NetBanking">Net Banking</option>
                            <option value="Wallet">Wallet</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Create Payment Link</button>
                </form>
            </div>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', html);
}

function openNewProfessionalModal() {
    const html = `
        <div id="newProfessionalModal" class="modal active">
            <div class="modal-content">
                <span class="close" onclick="closeModal('newProfessionalModal')">&times;</span>
                <h2>Add New Professional</h2>
                <form id="newProfessionalForm" onsubmit="handleCreateProfessional(event)">
                    <div class="form-group">
                        <label>Name *</label>
                        <input type="text" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Phone *</label>
                        <input type="tel" name="phone" pattern="[0-9]{10}" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email">
                    </div>
                    <div class="form-group">
                        <label>Service *</label>
                        <select name="service" required>
                            <option value="">Select Service</option>
                            <option value="All Rounder">All Rounder</option>
                            <option value="Baby Caretaker">Baby Caretaker</option>
                            <option value="Cooking Maid">Cooking Maid</option>
                            <option value="House Maid">House Maid</option>
                            <option value="Elderly Care">Elderly Care</option>
                            <option value="Security Guard">Security Guard</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Gender *</label>
                        <select name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Experience (Years)</label>
                        <input type="number" name="experience" min="0">
                    </div>
                    <div class="form-group">
                        <label>Location</label>
                        <input type="text" name="location">
                    </div>
                    <button type="submit" class="btn btn-primary">Add Professional</button>
                </form>
            </div>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', html);
}

function openNewUserModal() {
    const html = `
        <div id="newUserModal" class="modal active">
            <div class="modal-content">
                <span class="close" onclick="closeModal('newUserModal')">&times;</span>
                <h2>Add New User</h2>
                <form id="newUserForm" onsubmit="handleCreateUser(event)">
                    <div class="form-group">
                        <label>Name *</label>
                        <input type="text" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>Phone *</label>
                        <input type="tel" name="phone" pattern="[0-9]{10}" required>
                    </div>
                    <div class="form-group">
                        <label>Password *</label>
                        <input type="password" name="password" minlength="6" required>
                    </div>
                    <div class="form-group">
                        <label>Role *</label>
                        <select name="role" required>
                            <option value="">Select Role</option>
                            <option value="Admin">Admin</option>
                            <option value="Sales">Sales</option>
                            <option value="Allocation">Allocation</option>
                            <option value="Support">Support</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Add User</button>
                </form>
            </div>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', html);
}

// ============================
// FORM SUBMISSION HANDLERS
// ============================
async function handleCreateBooking(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);

    try {
        const response = await fetch('../api/create-booking.php', {
            method: 'POST',
            body: formData
        });

        // Check if response is ok
        if (!response.ok) {
            const text = await response.text();
            console.error('Server error:', response.status, text);
            alert('Server error (500). Check console for details.');
            return;
        }

        const data = await response.json();

        if (data.success) {
            alert('Booking created successfully!');
            closeModal('newBookingModal');
            setTimeout(() => location.reload(), 500);
        } else {
            alert('Error: ' + (data.message || 'Unknown error'));
        }
    } catch (error) {
        console.error('Fetch error:', error);
        alert('Error: ' + error.message);
    }
}

async function handleCreatePayment(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);

    try {
        const response = await fetch('../api/create-payment.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            alert('Payment link created successfully!');
            closeModal('newPaymentModal');
            setTimeout(() => location.reload(), 500);
        } else {
            alert('Error: ' + (data.message || 'Unknown error'));
        }
    } catch (error) {
        console.error(error);
        alert('Network error');
    }
}

async function handleCreateProfessional(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);

    try {
        const response = await fetch('../api/create-professional.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            alert('Professional added successfully!');
            closeModal('newProfessionalModal');
            setTimeout(() => location.reload(), 500);
        } else {
            alert('Error: ' + (data.message || 'Unknown error'));
        }
    } catch (error) {
        console.error(error);
        alert('Network error');
    }
}

async function handleCreateUser(e) {
    e.preventDefault();
    const form = e.target;

    // Create object from form data
    const formData = {
        name: form.querySelector('[name="name"]').value,
        email: form.querySelector('[name="email"]').value,
        phone: form.querySelector('[name="phone"]').value,
        password: form.querySelector('[name="password"]').value,
        role: form.querySelector('[name="role"]').value
    };

    try {
        const response = await fetch('../api/create-user.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData)
        });

        const data = await response.json();

        if (data.success) {
            alert('User created successfully!');
            closeModal('newUserModal');
            setTimeout(() => location.reload(), 500);
        } else {
            alert('Error: ' + (data.message || 'Unknown error'));
        }
    } catch (error) {
        console.error('Create user error:', error);
        alert('Network error. Please check if the API file exists.');
    }
}

// ============================
// VIEW/EDIT MODAL HANDLERS
// ============================
async function viewLead(leadId) {
    try {
        const response = await fetch(`../api/get-lead.php?id=${leadId}`);
        const data = await response.json();

        if (!data.success) {
            alert('Error: ' + data.message);
            return;
        }

        const lead = data.data.lead;
        const comments = data.data.comments;

        let commentsHtml = '<div class="comments-list">';
        if (comments.length > 0) {
            comments.forEach(comment => {
                commentsHtml += `
                    <div class="comment">
                        <strong>${comment.user_name}</strong> - ${new Date(comment.created_at).toLocaleString()}
                        <p>${comment.comment}</p>
                    </div>
                `;
            });
        } else {
            commentsHtml += '<p>No comments yet</p>';
        }
        commentsHtml += '</div>';

        const html = `
            <div id="viewLeadModal" class="modal active">
                <div class="modal-content">
                    <span class="close" onclick="closeModal('viewLeadModal')">&times;</span>
                    <h2>Lead Details</h2>
                    <div class="lead-details">
                        <p><strong>Name:</strong> ${lead.name}</p>
                        <p><strong>Phone:</strong> ${lead.phone}</p>
                        <p><strong>Service:</strong> ${lead.service}</p>
                        <p><strong>Status:</strong> <span class="badge">${lead.status}</span></p>
                        <p><strong>Created At:</strong> ${new Date(lead.created_at).toLocaleString()}</p>
                    </div>
                    <h3>Comments</h3>
                    ${commentsHtml}
                    <form id="addCommentForm" onsubmit="handleAddComment(event, ${leadId})">
                        <textarea name="comment" placeholder="Add a comment..." required></textarea>
                        <button type="submit" class="btn btn-primary">Add Comment</button>
                    </form>
                    <button class="btn btn-secondary" onclick="closeModal('viewLeadModal')">Close</button>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', html);
    } catch (error) {
        console.error(error);
        alert('Error loading lead details');
    }
}

async function viewBooking(bookingId) {
    removeAllModals();
    try {
        const response = await fetch(`../api/get-booking.php?id=${bookingId}`);
        const data = await response.json();

        if (!data.success) {
            alert('Error: ' + data.message);
            return;
        }

        const booking = data.data;

        const html = `
            <div id="viewBookingModal" class="modal active">
                <div class="modal-content">
                    <span class="close" onclick="closeModal('viewBookingModal')">&times;</span>
                    <h2>Booking Details</h2>
                    <div class="booking-details">
                        <p><strong>Customer Name:</strong> ${booking.customer_name}</p>
                        <p><strong>Phone:</strong> ${booking.customer_phone}</p>
                        <p><strong>Email:</strong> ${booking.customer_email || '-'}</p>
                        <p><strong>Service:</strong> ${booking.service}</p>
                        <p><strong>Address:</strong> ${booking.full_address || '-'}</p>
                        <p><strong>Status:</strong> <span class="badge">${booking.status}</span></p>
                        <p><strong>Starts At:</strong> ${booking.starts_at ? new Date(booking.starts_at).toLocaleString() : '-'}</p>
                        <p><strong>Job Hours:</strong> ${booking.job_hours || '-'}</p>
                        <p><strong>Salary Bracket:</strong> ${booking.salary_bracket || '-'}</p>
                        ${booking.updated_by_name ? `<p><strong>Last Edited By:</strong> ${booking.updated_by_name}</p>` : ''}
                    </div>
                    <button class="btn btn-secondary" onclick="editBooking(${bookingId})">Edit</button>
                    <button class="btn btn-secondary" onclick="closeModal('viewBookingModal')">Close</button>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', html);
    } catch (error) {
        console.error(error);
        alert('Error loading booking details');
    }
}

async function editBooking(bookingId) {
    try {
        const response = await fetch(`../api/get-booking.php?id=${bookingId}`);
        const data = await response.json();

        if (!data.success) {
            alert('Error: ' + data.message);
            return;
        }

        const booking = data.data;
        closeModal('viewBookingModal');

        const html = `
            <div id="editBookingModal" class="modal active">
                <div class="modal-content">
                    <span class="close" onclick="closeModal('editBookingModal')">&times;</span>
                    <h2>Edit Booking</h2>
                    <form id="editBookingForm" onsubmit="handleEditBooking(event, ${bookingId})">
                        <div class="form-group">
                            <label>Customer Name</label>
                            <input type="text" name="customer_name" value="${booking.customer_name}">
                        </div>
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="tel" name="customer_phone" pattern="[0-9]{10}" value="${booking.customer_phone}">
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status">
                                <option value="In progress" ${booking.status === 'In progress' ? 'selected' : ''}>In Progress</option>
                                <option value="Shortlisted" ${booking.status === 'Shortlisted' ? 'selected' : ''}>Shortlisted</option>
                                <option value="Assigned" ${booking.status === 'Assigned' ? 'selected' : ''}>Assigned</option>
                                <option value="Deployed" ${booking.status === 'Deployed' ? 'selected' : ''}>Deployed</option>
                                <option value="Canceled" ${booking.status === 'Canceled' ? 'selected' : ''}>Canceled</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Starts At</label>
                            <input type="datetime-local" name="starts_at" value="${booking.starts_at ? booking.starts_at.replace(' ', 'T').slice(0, 16) : ''}">
                        </div>
                        <div class="form-group">
                            <label>Job Hours</label>
                            <input type="number" name="job_hours" value="${booking.job_hours || ''}">
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                        <button type="button" class="btn btn-secondary" onclick="closeModal('editBookingModal')">Cancel</button>
                    </form>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', html);
    } catch (error) {
        console.error(error);
        alert('Error loading booking for editing');
    }
}

async function handleEditBooking(e, bookingId) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    formData.append('booking_id', bookingId);

    try {
        const response = await fetch('../api/update-booking.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            alert('Booking updated successfully!');
            closeModal('editBookingModal');
            setTimeout(() => location.reload(), 500);
        } else {
            alert('Error: ' + (data.message || 'Unknown error'));
        }
    } catch (error) {
        console.error(error);
        alert('Network error');
    }
}

async function handleAddComment(e, leadId) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    formData.append('lead_id', leadId);

    try {
        const response = await fetch('../api/add-comment.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            alert('Comment added!');
            form.reset();
            closeModal('viewLeadModal');
            setTimeout(() => viewLead(leadId), 300);
        } else {
            alert('Error: ' + (data.message || 'Unknown error'));
        }
    } catch (error) {
        console.error(error);
        alert('Network error');
    }
}

// Update action buttons in pages to use view functions
function setupViewButtons() {
    document.querySelectorAll('.view-btn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const id = this.getAttribute('data-id');
            const type = this.getAttribute('data-type');

            if (type === 'lead') viewLead(id);
            else if (type === 'booking') viewBooking(id);
            else if (type === 'payment') viewPayment(id);
            else if (type === 'professional') viewProfessional(id);
            else if (type === 'user') viewUser(id);
        });
    });

    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const id = this.getAttribute('data-id');
            const type = this.getAttribute('data-type');

            if (type === 'booking') editBooking(id);
            else if (type === 'professional') editProfessional(id);
            else if (type === 'user') editUser(id);
        });
    });
}

// Payment view/edit functions
async function viewPayment(paymentId) {
    removeAllModals();
    try {
        const response = await fetch(`../api/get-payment.php?id=${paymentId}`);
        const data = await response.json();

        if (!data.success) {
            alert('Error: ' + data.message);
            return;
        }

        const payment = data.data;

        const html = `
            <div id="viewPaymentModal" class="modal active">
                <div class="modal-content">
                    <span class="close" onclick="closeModal('viewPaymentModal')">&times;</span>
                    <h2>Payment Details</h2>
                    <div class="payment-details">
                        <p><strong>Customer Name:</strong> ${payment.customer_name}</p>
                        <p><strong>Phone:</strong> ${payment.customer_phone}</p>
                        <p><strong>Email:</strong> ${payment.customer_email}</p>
                        <p><strong>Service:</strong> ${payment.service}</p>
                        <p><strong>Purpose:</strong> ${payment.purpose}</p>
                        <p><strong>Amount:</strong> ₹${parseFloat(payment.total_amount).toFixed(2)}</p>
                        <p><strong>Status:</strong> <span class="badge">${payment.status}</span></p>
                        <p><strong>Payment Method:</strong> ${payment.payment_method || '-'}</p>
                        <p><strong>Created At:</strong> ${new Date(payment.created_at).toLocaleString()}</p>
                        ${payment.updated_by_name ? `<p><strong>Last Edited By:</strong> ${payment.updated_by_name}</p>` : ''}
                    </div>
                    <button class="btn btn-secondary" onclick="editPayment(${paymentId})">Edit</button>
                    <button class="btn btn-secondary" onclick="closeModal('viewPaymentModal')">Close</button>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', html);
    } catch (error) {
        console.error(error);
        alert('Error loading payment details');
    }
}

async function editPayment(paymentId) {
    try {
        const response = await fetch(`../api/get-payment.php?id=${paymentId}`);
        const data = await response.json();

        if (!data.success) {
            alert('Error: ' + data.message);
            return;
        }

        const payment = data.data;
        closeModal('viewPaymentModal');

        const html = `
            <div id="editPaymentModal" class="modal active">
                <div class="modal-content">
                    <span class="close" onclick="closeModal('editPaymentModal')">&times;</span>
                    <h2>Edit Payment</h2>
                    <form id="editPaymentForm" onsubmit="handleEditPayment(event, ${paymentId})">
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status">
                                <option value="Pending" ${payment.status === 'Pending' ? 'selected' : ''}>Pending</option>
                                <option value="Completed" ${payment.status === 'Completed' ? 'selected' : ''}>Completed</option>
                                <option value="Failed" ${payment.status === 'Failed' ? 'selected' : ''}>Failed</option>
                                <option value="Refunded" ${payment.status === 'Refunded' ? 'selected' : ''}>Refunded</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                        <button type="button" class="btn btn-secondary" onclick="closeModal('editPaymentModal')">Cancel</button>
                    </form>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', html);
    } catch (error) {
        console.error(error);
        alert('Error loading payment for editing');
    }
}

async function handleEditPayment(e, paymentId) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    formData.append('payment_id', paymentId);

    try {
        const response = await fetch('../api/update-payment-status.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            alert('Payment updated successfully!');
            closeModal('editPaymentModal');
            setTimeout(() => location.reload(), 500);
        } else {
            alert('Error: ' + (data.message || 'Unknown error'));
        }
    } catch (error) {
        console.error(error);
        alert('Network error');
    }
}

// Professional view/edit functions
async function viewProfessional(professionalId) {
    removeAllModals();
    try {
        const response = await fetch(`../api/get-professional.php?id=${professionalId}`);
        const data = await response.json();

        if (!data.success) {
            alert('Error: ' + data.message);
            return;
        }

        const prof = data.data;

        const html = `
            <div id="viewProfessionalModal" class="modal active">
                <div class="modal-content">
                    <span class="close" onclick="closeModal('viewProfessionalModal')">&times;</span>
                    <h2>Professional Details</h2>
                    <div class="professional-details">
                        <p><strong>Name:</strong> ${prof.name}</p>
                        <p><strong>Phone:</strong> ${prof.phone}</p>
                        <p><strong>Email:</strong> ${prof.email || '-'}</p>
                        <p><strong>Service:</strong> ${prof.service}</p>
                        <p><strong>Gender:</strong> ${prof.gender}</p>
                        <p><strong>Experience:</strong> ${prof.experience || 0} years</p>
                        <p><strong>Rating:</strong> ⭐ ${prof.rating}</p>
                        <p><strong>Status:</strong> <span class="badge">${prof.status}</span></p>
                        <p><strong>Verification:</strong> <span class="badge">${prof.verify_status}</span></p>
                        <p><strong>Location:</strong> ${prof.location || '-'}</p>
                        ${prof.updated_by_name ? `<p><strong>Last Edited By:</strong> ${prof.updated_by_name}</p>` : ''}
                    </div>
                    <button class="btn btn-secondary" onclick="editProfessional(${professionalId})">Edit</button>
                    <button class="btn btn-secondary" onclick="closeModal('viewProfessionalModal')">Close</button>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', html);
    } catch (error) {
        console.error(error);
        alert('Error loading professional details');
    }
}

async function editProfessional(professionalId) {
    try {
        const response = await fetch(`../api/get-professional.php?id=${professionalId}`);
        const data = await response.json();

        if (!data.success) {
            alert('Error: ' + data.message);
            return;
        }

        const prof = data.data;
        closeModal('viewProfessionalModal');

        const html = `
            <div id="editProfessionalModal" class="modal active">
                <div class="modal-content">
                    <span class="close" onclick="closeModal('editProfessionalModal')">&times;</span>
                    <h2>Edit Professional</h2>
                    <form id="editProfessionalForm" onsubmit="handleEditProfessional(event, ${professionalId})">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" value="${prof.name}">
                        </div>
                        <div class="form-group">
                            <label>Experience (Years)</label>
                            <input type="number" name="experience" value="${prof.experience || 0}">
                        </div>
                        <div class="form-group">
                            <label>Rating</label>
                            <input type="number" name="rating" step="0.1" min="0" max="5" value="${prof.rating}">
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status">
                                <option value="Active" ${prof.status === 'Active' ? 'selected' : ''}>Active</option>
                                <option value="Inactive" ${prof.status === 'Inactive' ? 'selected' : ''}>Inactive</option>
                                <option value="On Leave" ${prof.status === 'On Leave' ? 'selected' : ''}>On Leave</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Verification Status</label>
                            <select name="verify_status">
                                <option value="Verified" ${prof.verify_status === 'Verified' ? 'selected' : ''}>Verified</option>
                                <option value="Pending" ${prof.verify_status === 'Pending' ? 'selected' : ''}>Pending</option>
                                <option value="Rejected" ${prof.verify_status === 'Rejected' ? 'selected' : ''}>Rejected</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                        <button type="button" class="btn btn-secondary" onclick="closeModal('editProfessionalModal')">Cancel</button>
                    </form>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', html);
    } catch (error) {
        console.error(error);
        alert('Error loading professional for editing');
    }
}

async function handleEditProfessional(e, professionalId) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    formData.append('professional_id', professionalId);

    try {
        const response = await fetch('../api/update-professional.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            alert('Professional updated successfully!');
            closeModal('editProfessionalModal');
            setTimeout(() => location.reload(), 500);
        } else {
            alert('Error: ' + (data.message || 'Unknown error'));
        }
    } catch (error) {
        console.error(error);
        alert('Network error');
    }
}

// User view/edit functions
async function viewUser(userId) {
    removeAllModals();
    console.log('Attempting to view user with ID:', userId);

    try {
        const response = await fetch(`../api/get-user.php?id=${userId}`);
        console.log('Response status:', response.status);

        const responseText = await response.text();
        console.log('Raw response:', responseText);

        let data;
        try {
            data = JSON.parse(responseText);
        } catch (e) {
            console.error('Failed to parse JSON:', e);
            alert('Invalid response from server');
            return;
        }

        if (!data.success) {
            console.error('API error:', data.message);
            alert('Error: ' + data.message);
            return;
        }

        const user = data.data;
        console.log('User data:', user);

        const html = `
            <div id="viewUserModal" class="modal active">
                <div class="modal-content">
                    <span class="close" onclick="closeModal('viewUserModal')">&times;</span>
                    <h2>User Details</h2>
                    <div class="user-details">
                        <p><strong>Name:</strong> ${user.name || 'N/A'}</p>
                        <p><strong>Email:</strong> ${user.email || 'N/A'}</p>
                        <p><strong>Phone:</strong> ${user.phone || 'N/A'}</p>
                        <p><strong>Role:</strong> ${user.role || 'N/A'}</p>
                        <p><strong>Status:</strong> <span class="badge">${user.enabled ? 'Enabled' : 'Disabled'}</span></p>
                        <p><strong>Created At:</strong> ${user.created_at ? new Date(user.created_at).toLocaleString() : 'N/A'}</p>
                        ${user.updated_by_name ? `<p><strong>Last Edited By:</strong> ${user.updated_by_name}</p>` : ''}
                    </div>
                    <button class="btn btn-secondary" onclick="editUser(${userId})">Edit</button>
                    <button class="btn btn-secondary" onclick="closeModal('viewUserModal')">Close</button>
                </div>
            </div>
        `;

        // Close any existing modal first
        const existingModal = document.getElementById('viewUserModal');
        if (existingModal) {
            existingModal.remove();
        }

        document.body.insertAdjacentHTML('beforeend', html);

    } catch (error) {
        console.error('View user error:', error);
        alert('Error loading user details: ' + error.message);
    }
}

async function editUser(userId) {
    console.log('Attempting to edit user with ID:', userId);

    try {
        // FIX: Add ../ to go up one level from admin to api folder
        const response = await fetch(`../api/get-user.php?id=${userId}`);
        console.log('Response status:', response.status);

        const responseText = await response.text();
        console.log('Raw response:', responseText);

        let data;
        try {
            data = JSON.parse(responseText);
        } catch (e) {
            console.error('Failed to parse JSON:', e);
            alert('Invalid response from server - expected JSON but got HTML');
            return;
        }

        if (!data.success) {
            alert('Error: ' + data.message);
            return;
        }

        const user = data.data;

        // Close any existing view modal first
        const existingViewModal = document.getElementById('viewUserModal');
        if (existingViewModal) {
            existingViewModal.remove();
        }

        // Close any existing edit modal first
        const existingEditModal = document.getElementById('editUserModal');
        if (existingEditModal) {
            existingEditModal.remove();
        }

        const html = `
            <div id="editUserModal" class="modal active">
                <div class="modal-content">
                    <span class="close" onclick="closeModal('editUserModal')">&times;</span>
                    <h2>Edit User</h2>
                    <form id="editUserForm" onsubmit="handleEditUser(event, ${userId})">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" value="${user.name || ''}" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" value="${user.email || ''}" required>
                        </div>
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="tel" name="phone" pattern="[0-9]{10}" value="${user.phone || ''}" required>
                        </div>
                        <div class="form-group">
                            <label>Role</label>
                            <select name="role" required>
                                <option value="Admin" ${user.role === 'Admin' ? 'selected' : ''}>Admin</option>
                                <option value="Sales" ${user.role === 'Sales' ? 'selected' : ''}>Sales</option>
                                <option value="Allocation" ${user.role === 'Allocation' ? 'selected' : ''}>Allocation</option>
                                <option value="Support" ${user.role === 'Support' ? 'selected' : ''}>Support</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select name="enabled" required>
                                <option value="1" ${user.enabled ? 'selected' : ''}>Enabled</option>
                                <option value="0" ${!user.enabled ? 'selected' : ''}>Disabled</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>New Password (leave blank to keep current)</label>
                            <input type="password" name="password" minlength="6" placeholder="Leave blank to keep current password">
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                        <button type="button" class="btn btn-secondary" onclick="closeModal('editUserModal')">Cancel</button>
                    </form>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', html);

    } catch (error) {
        console.error('Edit user error:', error);
        alert('Error loading user for editing: ' + error.message);
    }
}

async function handleEditUser(e, userId) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    formData.append('user_id', userId);

    try {
        // FIX: Add ../ here too
        const response = await fetch('../api/update-user.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            alert('User updated successfully!');
            closeModal('editUserModal');
            setTimeout(() => location.reload(), 500);
        } else {
            alert('Error: ' + (data.message || 'Unknown error'));
        }
    } catch (error) {
        console.error(error);
        alert('Network error');
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', setupViewButtons);
