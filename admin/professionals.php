<?php
require_once '../includes/config.php';
require_role(['Admin', 'Allocation']);

// Define esc function if not already defined
if (!function_exists('esc')) {
    function esc($str)
    {
        global $conn;
        return mysqli_real_escape_string($conn, $str);
    }
}

// Get min and max dates from database for context
$date_range_result = $conn->query("SELECT MIN(DATE(created_at)) as min_date, MAX(DATE(created_at)) as max_date FROM professionals");
$date_range = $date_range_result->fetch_assoc();
$min_date = $date_range['min_date'] ?? date('Y-m-d', strtotime('-1 year'));
$max_date = $date_range['max_date'] ?? date('Y-m-d');

// Get distinct hours values for the filter
$hours_result = $conn->query("SELECT DISTINCT hours FROM professionals WHERE hours IS NOT NULL AND hours > 0 ORDER BY hours ASC");
$available_hours = [];
while ($row = $hours_result->fetch_assoc()) {
    $available_hours[] = $row['hours'];
}

// Get filter parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$service = isset($_GET['service']) ? trim($_GET['service']) : '';
$status = isset($_GET['status']) ? trim($_GET['status']) : '';
$gender = isset($_GET['gender']) ? trim($_GET['gender']) : '';
$verify_status = isset($_GET['verify_status']) ? trim($_GET['verify_status']) : '';
$created_before = isset($_GET['created_before']) ? trim($_GET['created_before']) : '';
$hours = isset($_GET['hours']) ? intval($_GET['hours']) : '';

// Build query with proper escaping
$where = array("1=1");

if (!empty($search)) {
    $search_esc = esc($search);
    $where[] = "(p.name LIKE '%$search_esc%' OR p.phone LIKE '%$search_esc%' OR p.location LIKE '%$search_esc%')";
}
if (!empty($service)) {
    $service_esc = esc($service);
    $where[] = "service = '$service_esc'";
}
if (!empty($status)) {
    $status_esc = esc($status);
    $where[] = "status = '$status_esc'";
}
if (!empty($gender)) {
    $gender_esc = esc($gender);
    $where[] = "gender = '$gender_esc'";
}
if (!empty($verify_status)) {
    $verify_status_esc = esc($verify_status);
    $where[] = "verify_status = '$verify_status_esc'";
}
if (!empty($created_before)) {
    // Validate date format
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $created_before)) {
        $created_before_esc = esc($created_before);
        $where[] = "DATE(p.created_at) <= '$created_before_esc'";
    }
}
if (!empty($hours) && is_numeric($hours)) {
    $where[] = "p.hours = $hours";
}

// Combine where conditions
$where_clause = implode(" AND ", $where);

// Execute query with error handling
$professionals = $conn->query("
    SELECT p.*, 
           u.name as updated_by_name
    FROM professionals p
    LEFT JOIN users u ON p.updated_by = u.id
    WHERE $where_clause 
    ORDER BY p.created_at DESC
");

if (!$professionals) {
    die("Query failed: " . $conn->error);
}

// Get total count without filters for context
$total_count_result = $conn->query("SELECT COUNT(*) as total FROM professionals");
$total_count = $total_count_result->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professionals - Servon Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">

    <!-- Font Awesome (For Icons)-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css"
        integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

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

        .filter-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: white;
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

        .filter-hint {
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
                <h1>Professionals Management</h1>
                <div class="user-menu">
                    <span>Welcome, <?php echo $_SESSION['user_name']; ?></span>
                    <a href="../api/logout.php" class="btn btn-secondary">Logout</a>
                </div>
            </div>

            <div class="content">
                <div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
                    <button class="btn btn-primary" onclick="openNewProfessionalModal()">+ Add Professional</button>
                    <div style="color: #6c757d; font-size: 14px;">
                        Total Professionals: <strong><?php echo $total_count; ?></strong>
                    </div>
                </div>

                <div class="table-container">
                    <div class="filter-section">
                        <form method="GET" id="filterForm">
                            <div class="filter-group">
                                <label>Search</label>
                                <input type="text" name="search" placeholder="Name, Phone or Location" value="<?php echo htmlspecialchars($search); ?>">
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
                            <div class="filter-group">
                                <label>Status</label>
                                <select name="status">
                                    <option value="">All Status</option>
                                    <option value="Active" <?php echo $status == 'Active' ? 'selected' : ''; ?>>Active</option>
                                    <option value="Inactive" <?php echo $status == 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                                    <option value="On Leave" <?php echo $status == 'On Leave' ? 'selected' : ''; ?>>On Leave</option>
                                </select>
                            </div>
                            <div class="filter-group">
                                <label>Gender</label>
                                <select name="gender">
                                    <option value="">All</option>
                                    <option value="Male" <?php echo $gender == 'Male' ? 'selected' : ''; ?>>Male</option>
                                    <option value="Female" <?php echo $gender == 'Female' ? 'selected' : ''; ?>>Female</option>
                                </select>
                            </div>
                            <div class="filter-group">
                                <label>Verification</label>
                                <select name="verify_status">
                                    <option value="">All</option>
                                    <option value="Verified" <?php echo $verify_status == 'Verified' ? 'selected' : ''; ?>>Verified</option>
                                    <option value="Pending" <?php echo $verify_status == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="Rejected" <?php echo $verify_status == 'Rejected' ? 'selected' : ''; ?>>Rejected</option>
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
                                <label>Hours Per Day</label>
                                <select name="hours">
                                    <option value="">All Hours</option>
                                    <?php foreach ($available_hours as $hour): ?>
                                        <option value="<?php echo $hour; ?>" <?php echo $hours == $hour ? 'selected' : ''; ?>>
                                            <?php echo $hour; ?> Hour<?php echo $hour > 1 ? 's' : ''; ?> Per Day
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (empty($available_hours)): ?>
                                    <div class="filter-hint">No hours data available</div>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>

                    <!-- Active Filters Display -->
                    <?php
                    $active_filters = array();
                    if (!empty($search)) $active_filters[] = ['type' => 'search', 'label' => "Search: \"$search\"", 'param' => 'search'];
                    if (!empty($service)) $active_filters[] = ['type' => 'service', 'label' => "Service: $service", 'param' => 'service'];
                    if (!empty($status)) $active_filters[] = ['type' => 'status', 'label' => "Status: $status", 'param' => 'status'];
                    if (!empty($gender)) $active_filters[] = ['type' => 'gender', 'label' => "Gender: $gender", 'param' => 'gender'];
                    if (!empty($verify_status)) $active_filters[] = ['type' => 'verify_status', 'label' => "Verification: $verify_status", 'param' => 'verify_status'];
                    if (!empty($created_before)) $active_filters[] = ['type' => 'date', 'label' => "Created Before: $created_before", 'param' => 'created_before'];
                    if (!empty($hours)) $active_filters[] = ['type' => 'hours', 'label' => "Hours: $hours Per Day", 'param' => 'hours'];

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

                    <div class="table-header">
                        <h3>All Professionals</h3>
                        <?php if ($professionals && $professionals->num_rows > 0): ?>
                            <div style="text-align: right; color: #6c757d; font-size: 13px;">
                                Showing <?php echo $professionals->num_rows; ?> professional(s)
                            </div>
                        <?php endif; ?>
                    </div>

                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Service</th>
                                <th>Location</th>
                                <th>Experi</th>
                                <th>Hours</th>
                                <th>Status</th>
                                <th>Verification</th>
                                <th>Created</th>
                                <th>Edited</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($professionals && $professionals->num_rows > 0) {
                                $counter = 1;
                                while ($prof = $professionals->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $counter . "</td>";
                                    echo "<td>" . htmlspecialchars($prof['name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($prof['phone']) . "</td>";
                                    echo "<td>" . htmlspecialchars($prof['service']) . "</td>";
                                    echo "<td>" . htmlspecialchars($prof['location']) . "</td>";
                                    echo "<td>" . ($prof['experience'] ? $prof['experience'] . " yrs" : "-") . "</td>";
                                    echo "<td>" . ($prof['hours'] ? $prof['hours'] . " hrs" : "-") . "</td>";
                                    echo "<td>" . $prof['status'] . "</td>";
                                    echo "<td>";
                                    $v_color = match ($prof['verify_status']) {
                                        'Verified' => '#d1fae5',
                                        'Pending' => '#fef3c7',
                                        'Rejected' => '#fee2e2',
                                        default => '#f3f4f6'
                                    };
                                    echo "<span style='padding: 4px 8px; border-radius: 4px; background-color: $v_color;'>" . $prof['verify_status'] . "</span></td>";
                                    echo "<td>" . date('M d, Y', strtotime($prof['created_at'])) . "</td>";
                                    echo "<td>" . ($prof['updated_by_name'] ? htmlspecialchars($prof['updated_by_name']) : '-') . "</td>";
                                    echo "<td style='width:156px'><a href='#' class='action-btn view-btn' data-id='" . $prof['id'] . "' data-type='professional' onclick='viewProfessional(" . $prof['id'] . "); return false;'><i class='fa-regular fa-eye'></i></a>";
                                    echo "<a href='#' class='action-btn' data-id='" . $prof['id'] . "' data-type='professional' style='background-color: #c28400; margin-right: 0' onclick='editProfessional(" . $prof['id'] . "); return false;'><i class='fa-regular fa-pen-to-square'></i></a>";
                                    if ($_SESSION['user_role'] == 'Admin') {
                                        echo " <a href='#' class='action-btn' onclick='deleteRecord(" . $prof['id'] . ", \"professional\"); return false;' style='background-color: #dc3545;'><i class='fa-solid fa-trash-can'></i></a>";
                                    }
                                    echo "</td>";
                                    echo "</tr>";
                                    $counter++;
                                }
                            } else {
                                echo "<tr>";
                                echo "<td colspan='12' class='no-results'>";
                                echo "<div style='font-size: 24px; margin-bottom: 10px;'>👥</div>";
                                echo "<div style='font-size: 18px; font-weight: 500; margin-bottom: 10px;'>No Professionals Found</div>";

                                if (!empty($active_filters)) {
                                    echo "<div class='suggestion'>No professionals match your current filters. Try:</div>";
                                    echo "<div class='action-buttons'>";
                                    echo "<button class='btn btn-secondary' onclick='clearAllFilters()'>Clear All Filters</button>";
                                    echo "<button class='btn btn-primary' onclick='window.location.href=\"" . $_SERVER['PHP_SELF'] . "\"'>View All Professionals</button>";
                                    echo "</div>";
                                } else {
                                    echo "<div class='suggestion'>Get started by adding your first professional!</div>";
                                    echo "<div class='action-buttons'>";
                                    echo "<button class='btn btn-primary' onclick='openNewProfessionalModal()'>+ Add Professional</button>";
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

    <div id="toast" class="toast"></div>

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

        // Auto-submit filters on change
        let filterTimeout;

        function setupAutoFilters() {
            const filterForm = document.querySelector('.filter-section form');

            if (!filterForm) return;

            // Handle text inputs with debounce
            filterForm.querySelectorAll('input[type="text"]').forEach(input => {
                input.addEventListener('keyup', function(e) {
                    clearTimeout(filterTimeout);
                    filterTimeout = setTimeout(() => {
                        filterForm.submit();
                    }, 800);
                });
            });

            // Handle select changes immediately
            filterForm.querySelectorAll('select').forEach(select => {
                select.addEventListener('change', function() {
                    filterForm.submit();
                });
            });

            // Handle date changes immediately
            filterForm.querySelectorAll('input[type="date"]').forEach(dateInput => {
                dateInput.addEventListener('change', function(e) {
                    const selectedDate = this.value;
                    const min = this.getAttribute('min');
                    const max = this.getAttribute('max');

                    if (min && selectedDate < min) {
                        alert(`Date cannot be before ${min}`);
                        this.value = '';
                        return;
                    }

                    if (max && selectedDate > max) {
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
        });
    </script>

    <script src="../assets/js/main.js"></script>
</body>

</html>