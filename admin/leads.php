<?php
require_once '../includes/config.php';
require_role(['Admin', 'Sales', 'Allocation', 'Support']);

// Get min and max dates from database for context
$date_range_result = $conn->query("SELECT MIN(DATE(created_at)) as min_date, MAX(DATE(created_at)) as max_date FROM leads");
$date_range = $date_range_result->fetch_assoc();
$min_date = $date_range['min_date'] ?? date('Y-m-d', strtotime('-30 days'));
$max_date = $date_range['max_date'] ?? date('Y-m-d');

// Get filter parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$service = isset($_GET['service']) ? trim($_GET['service']) : '';
$responder = isset($_GET['responder']) ? trim($_GET['responder']) : '';
$status = isset($_GET['status']) ? trim($_GET['status']) : '';
$created_before = isset($_GET['created_before']) ? trim($_GET['created_before']) : '';

// Get all responders for filter
$responders = $conn->query("SELECT id, name FROM users WHERE role IN ('Admin', 'Sales') ORDER BY name");

// Build query with proper escaping
$where = array("1=1");
$params = array();

if (!empty($search)) {
    $search_esc = esc($search);
    $where[] = "(name LIKE '%$search_esc%' OR phone LIKE '%$search_esc%')";
}
if (!empty($service)) {
    $service_esc = esc($service);
    $where[] = "service = '$service_esc'";
}
if (!empty($responder)) {
    $responder_int = intval($responder);
    $where[] = "responder_id = $responder_int";
}
if (!empty($status)) {
    $status_esc = esc($status);
    $where[] = "status = '$status_esc'";
}
if (!empty($created_before)) {
    // Validate date format
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $created_before)) {
        $created_before_esc = esc($created_before);
        $where[] = "DATE(l.created_at) <= '$created_before_esc'";
    }
}

// For non-admin users, show only their assigned leads
if ($_SESSION['user_role'] != 'Admin') {
    $where[] = "responder_id = " . intval($_SESSION['user_id']);
}

// Combine where conditions
$where_clause = implode(" AND ", $where);

// Execute query with error handling
$leads = $conn->query("
    SELECT l.*, u.name as updated_by_name 
    FROM leads l 
    LEFT JOIN users u ON l.updated_by = u.id 
    WHERE $where_clause 
    ORDER BY l.created_at DESC
");

if (!$leads) {
    die("Query failed: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leads - Servon Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .filter-section form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            width: 100%;
            align-items: end;
        }

        .filter-group input[type="date"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .reset-filter-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            height: fit-content;
            align-self: flex-end;
        }

        .reset-filter-btn:hover {
            background-color: #c82333;
        }

        .active-filters {
            margin-top: 10px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 4px;
            font-size: 14px;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: center;
        }

        .active-filters strong {
            color: #495057;
        }

        .active-filters .filter-tag {
            background-color: #007bff;
            color: white;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .active-filters .filter-tag .remove-filter {
            cursor: pointer;
            font-weight: bold;
            margin-left: 5px;
        }

        .active-filters .filter-tag .remove-filter:hover {
            opacity: 0.8;
        }

        .no-results {
            text-align: center;
            padding: 40px !important;
            background-color: #f8f9fa;
        }

        .no-results .suggestion {
            color: #6c757d;
            margin-top: 10px;
            font-size: 14px;
        }

        .no-results .action-buttons {
            margin-top: 20px;
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .date-range-info {
            font-size: 11px;
            color: #6c757d;
            margin-top: 3px;
        }

        /* Add Lead button styles */
        .header-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .btn-add-lead {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            text-decoration: none;
            font-size: 14px;
        }

        .btn-add-lead:hover {
            background-color: #218838;
        }

        .btn-add-lead i {
            font-size: 16px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>

        <div class="main-content">
            <div class="header">
                <h1>Leads Management</h1>
                <div class="header-actions">

                    <div class="user-menu">
                        <span>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                        <a href="../api/logout.php" class="btn btn-secondary">Logout</a>
                    </div>
                </div>
            </div>

            <div class="content">
                <!-- Add Lead Button -->
                <button onclick="openAddLeadModal()" class="btn-add-lead">
                    <i>+</i> Add Lead
                </button>
                <!-- Filter Section -->
                <div class="table-container">
                    <div class="filter-section">
                        <form method="GET" id="filterForm">
                            <div class="filter-group">
                                <label>Search</label>
                                <input type="text" name="search" placeholder="Name or Phone" value="<?php echo htmlspecialchars($search); ?>">
                            </div>

                            <div class="filter-group">
                                <label>Service</label>
                                <select name="service">
                                    <option value="">All Services</option>
                                    <option value="All Rounder" <?php echo $service == 'All Rounder' ? 'selected' : ''; ?>>All Rounder</option>
                                    <option value="Baby Caretaker" <?php echo $service == 'Baby Caretaker' ? 'selected' : ''; ?>>Baby Caretaker</option>
                                    <option value="Cooking Maid" <?php echo $service == 'Cooking Maid' ? 'selected' : ''; ?>>Cooking Maid</option>
                                    <option value="House Maid" <?php echo $service == 'House Maid' ? 'selected' : ''; ?>>House Maid</option>
                                    <option value="Elderly Care" <?php echo $service == 'Elderly Care' ? 'selected' : ''; ?>>Elderly Care</option>
                                    <option value="Security Guard" <?php echo $service == 'Security Guard' ? 'selected' : ''; ?>>Security Guard</option>
                                </select>
                            </div>

                            <?php if ($_SESSION['user_role'] == 'Admin'): ?>
                                <div class="filter-group">
                                    <label>Responder</label>
                                    <select name="responder">
                                        <option value="">All Responders</option>
                                        <?php
                                        if ($responders && $responders->num_rows > 0) {
                                            while ($resp = $responders->fetch_assoc()):
                                        ?>
                                                <option value="<?php echo $resp['id']; ?>" <?php echo $responder == $resp['id'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($resp['name']); ?>
                                                </option>
                                        <?php
                                            endwhile;
                                        }
                                        ?>
                                    </select>
                                </div>
                            <?php endif; ?>

                            <div class="filter-group">
                                <label>Status</label>
                                <select name="status">
                                    <option value="">All Status</option>
                                    <option value="Fresh" <?php echo $status == 'Fresh' ? 'selected' : ''; ?>>Fresh</option>
                                    <option value="In progress" <?php echo $status == 'In progress' ? 'selected' : ''; ?>>In Progress</option>
                                    <option value="Converted" <?php echo $status == 'Converted' ? 'selected' : ''; ?>>Converted</option>
                                    <option value="Dropped" <?php echo $status == 'Dropped' ? 'selected' : ''; ?>>Dropped</option>
                                </select>
                            </div>

                            <div class="filter-group">
                                <label>Created Before</label>
                                <input type="date" name="created_before"
                                    value="<?php echo htmlspecialchars($created_before); ?>"
                                    min="<?php echo $min_date; ?>"
                                    max="<?php echo $max_date; ?>">
                            </div>

                            <div class="filter-group">
                                <button type="button" class="reset-filter-btn" onclick="clearAllFilters()">Reset Filters</button>
                            </div>
                        </form>
                    </div>

                    <!-- Active Filters Display -->
                    <?php
                    $active_filters = array();
                    if (!empty($search)) $active_filters[] = ['type' => 'search', 'label' => "Search: \"$search\"", 'param' => 'search'];
                    if (!empty($service)) $active_filters[] = ['type' => 'service', 'label' => "Service: $service", 'param' => 'service'];
                    if (!empty($responder) && $_SESSION['user_role'] == 'Admin') {
                        // Get responder name
                        $resp_name = "ID: $responder";
                        $resp_result = $conn->query("SELECT name FROM users WHERE id = " . intval($responder));
                        if ($resp_result && $resp_row = $resp_result->fetch_assoc()) {
                            $resp_name = $resp_row['name'];
                        }
                        $active_filters[] = ['type' => 'responder', 'label' => "Responder: $resp_name", 'param' => 'responder'];
                    }
                    if (!empty($status)) $active_filters[] = ['type' => 'status', 'label' => "Status: $status", 'param' => 'status'];
                    if (!empty($created_before)) $active_filters[] = ['type' => 'date', 'label' => "Created Before: $created_before", 'param' => 'created_before'];

                    if (!empty($active_filters)):
                    ?>
                        <div class="active-filters">
                            <strong>Active Filters:</strong>
                            <?php foreach ($active_filters as $filter): ?>
                                <span class="filter-tag">
                                    <?php echo htmlspecialchars($filter['label']); ?>
                                    <span class="remove-filter" onclick="removeFilter('<?php echo $filter['param']; ?>')">×</span>
                                </span>
                            <?php endforeach; ?>
                            <button class="btn btn-secondary" style="margin-left: auto; padding: 4px 10px; font-size: 12px;" onclick="clearAllFilters()">Clear All</button>
                        </div>
                    <?php endif; ?>

                    <!-- Table -->
                    <div class="table-header">
                        <h3>All Leads</h3>
                        <?php if ($leads && $leads->num_rows > 0): ?>
                            <div style="text-align: right; color: #6c757d; font-size: 13px;">
                                Showing <?php echo $leads->num_rows; ?> lead(s)
                            </div>
                        <?php endif; ?>
                    </div>

                    <table id="leadsTable">
                        <thead>
                            <tr>
                                <th>Serial No</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Service</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Last Edited By</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($leads && $leads->num_rows > 0) {
                                $counter = 1;
                                while ($lead = $leads->fetch_assoc()) {
                                    $status_color = match ($lead['status']) {
                                        'Fresh' => '#fef3c7',
                                        'In progress' => '#dbeafe',
                                        'Converted' => '#d1fae5',
                                        'Dropped' => '#fee2e2',
                                        default => '#f3f4f6'
                                    };

                                    $status_text_color = match ($lead['status']) {
                                        'Fresh' => '#92400e',
                                        'In progress' => '#0c4a6e',
                                        'Converted' => '#065f46',
                                        'Dropped' => '#7f1d1d',
                                        default => '#374151'
                                    };

                                    echo "<tr>";
                                    echo "<td>" . $counter . "</td>";
                                    echo "<td>" . htmlspecialchars($lead['name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($lead['phone']) . "</td>";
                                    echo "<td>" . htmlspecialchars($lead['service']) . "</td>";
                                    echo "<td><span style='padding: 4px 8px; border-radius: 4px; background-color: $status_color; color: $status_text_color;'>" . htmlspecialchars($lead['status']) . "</span></td>";
                                    echo "<td>" . ($lead['created_at'] ? date('M d, Y H:i', strtotime($lead['created_at'])) : '-') . "</td>";
                                    echo "<td>" . ($lead['updated_by_name'] ? htmlspecialchars($lead['updated_by_name']) : '-') . "</td>";
                                    echo "<td>";
                                    echo "<a href='#' class='action-btn view-btn' data-id='" . $lead['id'] . "' data-type='lead'>View</a> ";
                                    echo "<a href='#' class='action-btn' onclick='openCreateFollowUpModal(" . $lead['id'] . ", \"" . htmlspecialchars($lead['name']) . "\"); return false;' style='background-color: #10b981;'>Follow-up</a>";
                                    if ($_SESSION['user_role'] == 'Admin') {
                                        echo " <a href='#' class='action-btn' onclick='deleteRecord(" . $lead['id'] . ", \"lead\"); return false;' style='background-color: #dc3545;'>Delete</a>";
                                    }
                                    echo "</td>";
                                    echo "</tr>";
                                    $counter++;
                                }
                            } else {
                                echo "<tr>";
                                echo "<td colspan='8' class='no-results'>";
                                echo "<div style='font-size: 24px; margin-bottom: 10px;'>📭</div>";
                                echo "<div style='font-size: 18px; font-weight: 500; margin-bottom: 10px;'>No Leads Found</div>";

                                if (!empty($active_filters)) {
                                    echo "<div class='suggestion'>No leads match your current filters. Try clearing some filters.</div>";
                                    echo "<div class='action-buttons'>";
                                    echo "<button class='btn btn-secondary' onclick='clearAllFilters()'>Clear All Filters</button>";
                                    echo "</div>";
                                }

                                echo "</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Lead Modal -->
    <div id="addLeadModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('addLeadModal')">&times;</span>
            <h2>Add New Lead</h2>
            <div id="addLeadErrors" style="display: none; padding: 10px; margin-bottom: 15px; background-color: #fee2e2; border: 1px solid #fecaca; border-radius: 4px; color: #991b1b;"></div>
            <form id="addLeadForm" onsubmit="handleAddLead(event)">
                <div class="form-group">
                    <label>Name *</label>
                    <input type="text" name="name" required placeholder="Enter full name">
                    <small id="nameError" class="error-text"></small>
                </div>

                <div class="form-group">
                    <label>Phone *</label>
                    <input type="tel" name="phone" pattern="[0-9]{10}" required placeholder="10-digit mobile number">
                    <small id="phoneError" class="error-text"></small>
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
                    <small id="serviceError" class="error-text"></small>
                </div>

                <button type="submit" class="btn btn-primary">Add Lead</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('addLeadModal')">Cancel</button>
            </form>
        </div>
    </div>

    <script>
        // Function to open add lead modal
        function openAddLeadModal() {
            const modal = document.getElementById('addLeadModal');
            if (modal) {
                modal.classList.add('active');
                // Clear any previous errors
                document.getElementById('addLeadErrors').style.display = 'none';
                document.getElementById('addLeadForm').reset();
            }
        }

        // Validation functions for add lead form
        function validateAddLeadName(form) {
            const input = form.querySelector('[name="name"]');
            const error = document.querySelector('#addLeadForm .error-text');
            const value = input.value.trim();

            if (!value) {
                document.getElementById('addLeadErrors').innerHTML = '<strong>❌ Error:</strong> Name is required';
                document.getElementById('addLeadErrors').style.display = 'block';
                return false;
            }

            if (value.length < 3) {
                document.getElementById('addLeadErrors').innerHTML = '<strong>❌ Error:</strong> Name must be at least 3 characters';
                document.getElementById('addLeadErrors').style.display = 'block';
                return false;
            }

            return true;
        }

        function validateAddLeadPhone(form) {
            const input = form.querySelector('[name="phone"]');
            const value = input.value.trim();

            if (!value) {
                document.getElementById('addLeadErrors').innerHTML = '<strong>❌ Error:</strong> Phone number is required';
                document.getElementById('addLeadErrors').style.display = 'block';
                return false;
            }

            if (!/^\d{10}$/.test(value)) {
                document.getElementById('addLeadErrors').innerHTML = '<strong>❌ Error:</strong> Phone number must be 10 digits';
                document.getElementById('addLeadErrors').style.display = 'block';
                return false;
            }

            return true;
        }

        function validateAddLeadService(form) {
            const input = form.querySelector('[name="service"]');
            const value = input.value;

            if (!value) {
                document.getElementById('addLeadErrors').innerHTML = '<strong>❌ Error:</strong> Please select a service';
                document.getElementById('addLeadErrors').style.display = 'block';
                return false;
            }

            return true;
        }

        // Handle add lead form submission
        async function handleAddLead(e) {
            e.preventDefault();
            const form = e.target;
            const errorDiv = document.getElementById('addLeadErrors');

            // Hide any previous errors
            errorDiv.style.display = 'none';

            // Validate form
            if (!validateAddLeadName(form) || !validateAddLeadPhone(form) || !validateAddLeadService(form)) {
                return;
            }

            const formData = new FormData(form);

            try {
                const response = await fetch('../api/submit-lead.php', {
                    method: 'POST',
                    body: formData
                });

                // Check if response is ok
                if (!response.ok) {
                    const text = await response.text();
                    console.error('Server error:', response.status, text);
                    errorDiv.innerHTML = '<strong>❌ Error:</strong> Server error (' + response.status + '). Please try again.';
                    errorDiv.style.display = 'block';
                    return;
                }

                const data = await response.json();

                if (data.success) {
                    alert('Lead added successfully!');
                    closeModal('addLeadModal');
                    setTimeout(() => location.reload(), 500);
                } else {
                    // Display error message
                    errorDiv.innerHTML = '<strong>❌ Error:</strong> ' + (data.message || 'Unknown error');
                    errorDiv.style.display = 'block';
                    errorDiv.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            } catch (error) {
                console.error('Add lead error:', error);
                errorDiv.innerHTML = '<strong>❌ Error:</strong> Network error: ' + error.message;
                errorDiv.style.display = 'block';
            }
        }

        // Function to remove a specific filter
        function removeFilter(paramName) {
            const url = new URL(window.location.href);
            url.searchParams.delete(paramName);
            window.location.href = url.toString();
        }

        // Function to clear all filters
        function clearAllFilters() {
            window.location.href = window.location.pathname;
        }

        // Auto-submit filters on change
        let filterTimeout;

        function setupAutoFilters() {
            const filterForm = document.getElementById('filterForm');

            if (!filterForm) return;

            // Handle text inputs with debounce
            filterForm.querySelectorAll('input[type="text"]').forEach(input => {
                input.addEventListener('keyup', function(e) {
                    clearTimeout(filterTimeout);
                    filterTimeout = setTimeout(() => {
                        filterForm.submit();
                    }, 800); // Wait 800ms after user stops typing
                });
            });

            // Handle select changes immediately
            filterForm.querySelectorAll('select').forEach(select => {
                select.addEventListener('change', function() {
                    filterForm.submit();
                });
            });

            // Handle date changes
            filterForm.querySelectorAll('input[type="date"]').forEach(dateInput => {
                dateInput.addEventListener('change', function(e) {
                    const selectedDate = this.value;
                    const min = this.getAttribute('min');
                    const max = this.getAttribute('max');

                    // Validate date range
                    if (min && selectedDate && selectedDate < min) {
                        alert(`Date cannot be before ${min}`);
                        this.value = '';
                        return;
                    }
                    if (max && selectedDate && selectedDate > max) {
                        alert(`Date cannot be after ${max}`);
                        this.value = '';
                        return;
                    }

                    filterForm.submit();
                });
            });
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            setupAutoFilters();

            // Keep existing functionality
            if (typeof setActiveMenu === 'function') setActiveMenu();
            if (typeof autoHideMessages === 'function') autoHideMessages();
            if (typeof setupViewButtons === 'function') setupViewButtons();
        });
    </script>

    <script src="../assets/js/main.js"></script>
</body>

</html>