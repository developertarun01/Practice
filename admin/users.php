<?php
require_once '../includes/config.php';
require_role(['Admin']);

// Define esc function if not already defined in config.php
if (!function_exists('esc')) {
    function esc($str)
    {
        global $conn;
        return mysqli_real_escape_string($conn, $str);
    }
}

// Get min and max dates from database for context
$date_range_result = $conn->query("SELECT MIN(DATE(created_at)) as min_date, MAX(DATE(created_at)) as max_date FROM users");
$date_range = $date_range_result->fetch_assoc();
$min_date = $date_range['min_date'] ?? date('Y-m-d', strtotime('-1 year'));
$max_date = $date_range['max_date'] ?? date('Y-m-d');

// Get filter parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$role = isset($_GET['role']) ? trim($_GET['role']) : '';
$enabled = isset($_GET['enabled']) ? trim($_GET['enabled']) : '';
$created_before = isset($_GET['created_before']) ? trim($_GET['created_before']) : '';

// Build query with proper escaping
$where = array("1=1");

if (!empty($search)) {
    $search_esc = esc($search);
    $where[] = "(u.name LIKE '%$search_esc%' OR u.email LIKE '%$search_esc%' OR u.phone LIKE '%$search_esc%')";
}

if (!empty($role)) {
    $role_esc = esc($role);
    $where[] = "u.role = '$role_esc'";
}

if ($enabled !== '') {
    $enabled_value = $enabled == '1' ? '1' : '0';
    $where[] = "u.enabled = $enabled_value";
}

if (!empty($created_before)) {
    // Validate date format
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $created_before)) {
        $created_before_esc = esc($created_before);
        $where[] = "DATE(u.created_at) <= '$created_before_esc'";
    }
}

// Combine where conditions
$where_clause = implode(" AND ", $where);

// Execute query with error handling
$users = $conn->query("
    SELECT u.*, 
           editor.name as updated_by_name
    FROM users u
    LEFT JOIN users editor ON u.updated_by = editor.id
    WHERE $where_clause 
    ORDER BY u.created_at DESC
");

if (!$users) {
    die("Query failed: " . $conn->error);
}

// Get total count without filters for context
$total_count_result = $conn->query("SELECT COUNT(*) as total FROM users");
$total_count = $total_count_result->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users - Servon Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .reset-filter-btn {
            background-color: #6c757d;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            height: fit-content;
            align-self: flex-end;
        }

        .reset-filter-btn:hover {
            background-color: #5a6268;
        }

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

        .filter-group .date-hint {
            font-size: 11px;
            color: #6c757d;
            margin-top: 3px;
        }
    </style>
</head>

<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>

        <div class="main-content">
            <div class="header">
                <h1>Users Management</h1>
                <div class="user-menu">
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                    <a href="../api/logout.php" class="btn btn-secondary">Logout</a>
                </div>
            </div>

            <div class="content">
                <!-- Add User Button -->
                <div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
                    <button class="btn btn-primary" onclick="openNewUserModal()">+ Add User</button>
                    <div style="color: #6c757d; font-size: 14px;">
                        Total Users: <strong><?php echo $total_count; ?></strong>
                    </div>
                </div>

                <!-- Filter Section -->
                <div class="table-container">
                    <div class="filter-section">
                        <form method="GET" id="filterForm">
                            <div class="filter-group">
                                <label>Search</label>
                                <input type="text" name="search" placeholder="Name, Email or Phone" value="<?php echo htmlspecialchars($search); ?>">
                            </div>

                            <div class="filter-group">
                                <label>Role</label>
                                <select name="role">
                                    <option value="">All Roles</option>
                                    <option value="Admin" <?php echo $role == 'Admin' ? 'selected' : ''; ?>>Admin</option>
                                    <option value="Sales" <?php echo $role == 'Sales' ? 'selected' : ''; ?>>Sales</option>
                                    <option value="Allocation" <?php echo $role == 'Allocation' ? 'selected' : ''; ?>>Allocation</option>
                                    <option value="Support" <?php echo $role == 'Support' ? 'selected' : ''; ?>>Support</option>
                                </select>
                            </div>

                            <div class="filter-group">
                                <label>Status</label>
                                <select name="enabled">
                                    <option value="">All Status</option>
                                    <option value="1" <?php echo $enabled == '1' ? 'selected' : ''; ?>>Enabled</option>
                                    <option value="0" <?php echo $enabled == '0' ? 'selected' : ''; ?>>Disabled</option>
                                </select>
                            </div>

                            <div class="filter-group">
                                <label>Created Before</label>
                                <input type="date" name="created_before"
                                    value="<?php echo htmlspecialchars($created_before); ?>"
                                    min="<?php echo $min_date; ?>"
                                    max="<?php echo $max_date; ?>">
                            </div>
                        </form>
                    </div>

                    <!-- Active Filters Display -->
                    <?php
                    $active_filters = array();
                    if (!empty($search)) $active_filters[] = ['type' => 'search', 'label' => "Search: \"$search\"", 'param' => 'search'];
                    if (!empty($role)) $active_filters[] = ['type' => 'role', 'label' => "Role: $role", 'param' => 'role'];
                    if ($enabled !== '') {
                        $status_label = $enabled == '1' ? 'Enabled' : 'Disabled';
                        $active_filters[] = ['type' => 'enabled', 'label' => "Status: $status_label", 'param' => 'enabled'];
                    }
                    if (!empty($created_before)) $active_filters[] = ['type' => 'date', 'label' => "Until: $created_before", 'param' => 'created_before'];

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
                        <h3>All Users</h3>
                        <?php if ($users && $users->num_rows > 0): ?>
                            <div style="text-align: right; color: #6c757d; font-size: 13px;">
                                Showing <?php echo $users->num_rows; ?> user(s)
                            </div>
                        <?php endif; ?>
                    </div>

                    <table>
                        <thead>
                            <tr>
                                <th>Serial No</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Last Edited By</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($users && $users->num_rows > 0) {
                                $counter = 1;
                                while ($user = $users->fetch_assoc()) {
                                    $status_color = $user['enabled'] ? '#d1fae5' : '#fee2e2';
                                    $status_text = $user['enabled'] ? 'Enabled' : 'Disabled';

                                    echo "<tr>";
                                    echo "<td>" . $counter . "</td>";
                                    echo "<td>" . htmlspecialchars($user['name'] ?? '') . "</td>";
                                    echo "<td>" . htmlspecialchars($user['email'] ?? '') . "</td>";
                                    echo "<td>" . htmlspecialchars($user['phone'] ?? '') . "</td>";
                                    echo "<td>" . htmlspecialchars($user['role'] ?? '') . "</td>";
                                    echo "<td><span style='padding: 4px 8px; border-radius: 4px; background-color: $status_color;'>" . $status_text . "</span></td>";
                                    echo "<td>" . ($user['created_at'] ? date('M d, Y', strtotime($user['created_at'])) : '-') . "</td>";
                                    echo "<td>" . ($user['updated_by_name'] ? htmlspecialchars($user['updated_by_name']) : '-') . "</td>";
                                    echo "<td><a href='#' class='action-btn view-btn' data-id='" . $user['id'] . "' data-type='user' onclick='viewUser(" . $user['id'] . "); return false;'>View</a>";
                                    echo "<a href='#' class='action-btn' data-id='" . $user['id'] . "' data-type='user' style='background-color: #c28400;' onclick='editUser(" . $user['id'] . "); return false;'>Edit</a>";
                                    if ($_SESSION['user_role'] == 'Admin' && $user['id'] != $_SESSION['user_id']) {
                                        echo " <a href='#' class='action-btn' onclick='deleteRecord(" . $user['id'] . ", \"user\"); return false;' style='background-color: #dc3545;'>Delete</a>";
                                    }
                                    echo "</td>";
                                    echo "</tr>";
                                    $counter++;
                                }
                            } else {
                                echo "<tr>";
                                echo "<td colspan='9' class='no-results'>";
                                echo "<div style='font-size: 24px; margin-bottom: 10px;'>👥</div>";
                                echo "<div style='font-size: 18px; font-weight: 500; margin-bottom: 10px;'>No Users Found</div>";

                                if (!empty($active_filters)) {
                                    echo "<div class='suggestion'>No users match your current filters. Try:</div>";
                                    echo "<div class='action-buttons'>";
                                    echo "<button class='btn btn-secondary' onclick='clearAllFilters()'>Clear All Filters</button>";
                                    if (!empty($created_before)) {
                                        $suggested_date = $max_date;
                                        echo "<button class='btn btn-primary' onclick='useSuggestedDate(\"$suggested_date\")'>Show latest users</button>";
                                    }
                                    echo "</div>";
                                } else {
                                    echo "<div class='suggestion'>Get started by creating your first user!</div>";
                                    echo "<div class='action-buttons'>";
                                    echo "<button class='btn btn-primary' onclick='openNewUserModal()'>+ Add New User</button>";
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

    <script>
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

        // Function to use suggested date
        function useSuggestedDate(date) {
            const url = new URL(window.location.href);
            url.searchParams.set('created_before', date);
            window.location.href = url.toString();
        }

        // ============================
        // AUTO FILTERS FUNCTIONALITY
        // ============================

        let filterTimeout;

        function setupAutoFilters() {
            const filterForm = document.querySelector('.filter-section form');

            if (!filterForm) return;

            // Text inputs
            filterForm.querySelectorAll('input[type="text"], input[type="search"]').forEach(input => {
                input.addEventListener('keyup', handleKeyUp);
                input.addEventListener('blur', handleBlur);
            });

            // Select dropdowns
            filterForm.querySelectorAll('select').forEach(select => {
                select.addEventListener('change', handleChange);
            });

            // Date inputs
            filterForm.querySelectorAll('input[type="date"]').forEach(dateInput => {
                dateInput.addEventListener('change', handleDateChange);
            });

            // Add reset button
            addResetButton(filterForm);
        }

        function handleKeyUp(e) {
            clearTimeout(filterTimeout);
            filterTimeout = setTimeout(() => submitFilter(), 800);
        }

        function handleBlur(e) {
            submitFilter();
        }

        function handleChange(e) {
            submitFilter();
        }

        function handleDateChange(e) {
            const dateInput = e.target;
            const selectedDate = dateInput.value;

            // Check if date is within allowed range
            const min = dateInput.getAttribute('min');
            const max = dateInput.getAttribute('max');

            if (min && selectedDate && selectedDate < min) {
                alert(`Date cannot be before ${min}`);
                dateInput.value = '';
                return;
            }

            if (max && selectedDate && selectedDate > max) {
                alert(`Date cannot be after ${max}`);
                dateInput.value = '';
                return;
            }

            submitFilter();
        }

        function submitFilter() {
            const form = document.querySelector('.filter-section form');
            if (!form) return;

            const params = new URLSearchParams();
            let hasActiveFilter = false;

            // Collect all non-empty values
            form.querySelectorAll('input[type="text"], input[type="search"], select, input[type="date"]').forEach(field => {
                const value = field.value ? field.value.trim() : '';
                if (value !== '' && field.name) {
                    params.append(field.name, value);
                    hasActiveFilter = true;
                }
            });

            const baseUrl = window.location.pathname;
            window.location.href = hasActiveFilter ? baseUrl + '?' + params.toString() : baseUrl;
        }

        function addResetButton(form) {
            // Remove existing reset button if any
            const existingBtn = form.querySelector('.reset-filter-btn');
            if (existingBtn) existingBtn.remove();

            // Create container for reset button
            const resetContainer = document.createElement('div');
            resetContainer.className = 'filter-group';
            resetContainer.style.display = 'flex';
            resetContainer.style.alignItems = 'flex-end';

            // Create reset button
            const resetBtn = document.createElement('button');
            resetBtn.type = 'button';
            resetBtn.className = 'btn btn-secondary reset-filter-btn';
            resetBtn.textContent = 'Clear Filters';
            resetBtn.style.cssText = 'width: 100%; background-color: #dc3545; color: white; border: none; padding: 10px; border-radius: 4px; cursor: pointer;';

            resetBtn.addEventListener('click', () => {
                window.location.href = window.location.pathname;
            });

            resetContainer.appendChild(resetBtn);
            form.appendChild(resetContainer);
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            setupAutoFilters();
        });
    </script>

    <script src="../assets/js/main.js"></script>
</body>

</html>