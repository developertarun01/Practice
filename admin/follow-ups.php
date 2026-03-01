<?php
require_once '../includes/config.php';
require_role(['Admin', 'Sales', 'Allocation', 'Support']);

// Define esc function if not already defined in config.php
if (!function_exists('esc')) {
    function esc($str)
    {
        global $conn;
        return mysqli_real_escape_string($conn, $str);
    }
}

// Get min and max dates for context
$date_range_result = $conn->query("SELECT MIN(DATE(created_at)) as min_date, MAX(DATE(created_at)) as max_date FROM follow_ups");
$date_range = $date_range_result->fetch_assoc();
$min_date = $date_range['min_date'] ?? date('Y-m-d', strtotime('-30 days'));
$max_date = $date_range['max_date'] ?? date('Y-m-d');

// Get filter parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$direction = isset($_GET['direction']) ? trim($_GET['direction']) : '';
$channel = isset($_GET['channel']) ? trim($_GET['channel']) : '';
$created_before = isset($_GET['created_before']) ? trim($_GET['created_before']) : '';

// Build query with proper escaping
$where = array("1=1");

// User role filter
if ($_SESSION['user_role'] != 'Admin') {
    $where[] = "f.user_id = " . intval($_SESSION['user_id']);
}

// Apply filters with proper escaping
if (!empty($search)) {
    $search_esc = esc($search);
    $where[] = "(l.name LIKE '%$search_esc%' OR l.phone LIKE '%$search_esc%')";
}
if (!empty($direction)) {
    $direction_esc = esc($direction);
    $where[] = "f.direction = '$direction_esc'";
}
if (!empty($channel)) {
    $channel_esc = esc($channel);
    $where[] = "f.channel = '$channel_esc'";
}
if (!empty($created_before)) {
    // Validate date format
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $created_before)) {
        $created_before_esc = esc($created_before);
        $where[] = "DATE(f.created_at) <= '$created_before_esc'";
    }
}

// Combine where conditions
$where_clause = implode(" AND ", $where);

// Execute query with error handling
$follow_ups = $conn->query("
    SELECT f.*, l.name, l.phone, u.name as user_name 
    FROM follow_ups f 
    LEFT JOIN leads l ON f.lead_id = l.id 
    LEFT JOIN users u ON f.user_id = u.id 
    WHERE $where_clause 
    ORDER BY f.created_at DESC
");

if (!$follow_ups) {
    die("Query failed: " . $conn->error);
}

// Get total count without filters for context
$total_count_query = "SELECT COUNT(*) as total FROM follow_ups f";
if ($_SESSION['user_role'] != 'Admin') {
    $total_count_query .= " WHERE f.user_id = " . intval($_SESSION['user_id']);
}
$total_count_result = $conn->query($total_count_query);
$total_count = $total_count_result->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Follow Ups - Servon Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .filter-section form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            width: 100%;
            align-items: end;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
        }

        .filter-group label {
            margin-bottom: 5px;
            font-weight: 500;
            color: #495057;
        }

        .filter-group input,
        .filter-group select {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .filter-group input:focus,
        .filter-group select:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
        }

        .reset-filter-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            width: 100%;
            transition: background-color 0.2s;
        }

        .reset-filter-btn:hover {
            background-color: #c82333;
        }

        .active-filters {
            margin-top: 15px;
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

        .filter-tag {
            background-color: #007bff;
            color: white;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .filter-tag .remove-filter {
            cursor: pointer;
            font-weight: bold;
            margin-left: 5px;
            font-size: 16px;
        }

        .filter-tag .remove-filter:hover {
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
    </style>
</head>

<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>

        <div class="main-content">
            <div class="header">
                <h1>Follow Ups Management</h1>
                <div class="user-menu">
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                    <a href="../api/logout.php" class="btn btn-secondary">Logout</a>
                </div>
            </div>

            <div class="content">
                <div class="table-container">
                    <!-- Filter Section -->
                    <div class="filter-section">
                        <form method="GET" id="filterForm">
                            <div class="filter-group">
                                <label>Search Lead</label>
                                <input type="text" name="search" placeholder="Name or Phone"
                                    value="<?php echo htmlspecialchars($search); ?>"
                                    autocomplete="off">
                            </div>

                            <div class="filter-group">
                                <label>Direction</label>
                                <select name="direction">
                                    <option value="">All Directions</option>
                                    <option value="Inbound" <?php echo $direction == 'Inbound' ? 'selected' : ''; ?>>Inbound</option>
                                    <option value="Outbound" <?php echo $direction == 'Outbound' ? 'selected' : ''; ?>>Outbound</option>
                                </select>
                            </div>

                            <div class="filter-group">
                                <label>Channel</label>
                                <select name="channel">
                                    <option value="">All Channels</option>
                                    <option value="Phone" <?php echo $channel == 'Phone' ? 'selected' : ''; ?>>Phone</option>
                                    <option value="Email" <?php echo $channel == 'Email' ? 'selected' : ''; ?>>Email</option>
                                    <option value="WhatsApp" <?php echo $channel == 'WhatsApp' ? 'selected' : ''; ?>>WhatsApp</option>
                                    <option value="SMS" <?php echo $channel == 'SMS' ? 'selected' : ''; ?>>SMS</option>
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
                    if (!empty($direction)) $active_filters[] = ['type' => 'direction', 'label' => "Direction: $direction", 'param' => 'direction'];
                    if (!empty($channel)) $active_filters[] = ['type' => 'channel', 'label' => "Channel: $channel", 'param' => 'channel'];
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

                    <div class="table-header">
                        <h3>All Follow Ups</h3>
                        <?php if ($follow_ups && $follow_ups->num_rows > 0): ?>
                            <div style="text-align: right; color: #6c757d; font-size: 13px;">
                                Showing <?php echo $follow_ups->num_rows; ?> of <?php echo $total_count; ?> follow up(s)
                            </div>
                        <?php endif; ?>
                    </div>

                    <table>
                        <thead>
                            <tr>
                                <th>Serial No</th>
                                <th>Lead Name</th>
                                <th>Phone</th>
                                <th>Direction</th>
                                <th>Channel</th>
                                <th>Comments</th>
                                <th>Reminder At</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($follow_ups && $follow_ups->num_rows > 0) {
                                $counter = 1;
                                while ($fu = $follow_ups->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $counter++ . "</td>";
                                    echo "<td>" . (isset($fu['name']) ? htmlspecialchars($fu['name']) : "-") . "</td>";
                                    echo "<td>" . (isset($fu['phone']) ? htmlspecialchars($fu['phone']) : "-") . "</td>";
                                    echo "<td>" . htmlspecialchars($fu['direction']) . "</td>";
                                    echo "<td>" . htmlspecialchars($fu['channel']) . "</td>";
                                    echo "<td>" . (isset($fu['comments']) ? htmlspecialchars(substr($fu['comments'], 0, 50)) . "..." : "-") . "</td>";
                                    echo "<td>" . ($fu['reminder_at'] ? date('M d, Y H:i', strtotime($fu['reminder_at'])) : "-") . "</td>";
                                    echo "<td>" . date('M d, Y H:i', strtotime($fu['created_at'])) . "</td>";
                                    echo "<td>";
                                    echo "<a href='#' class='action-btn view-btn' onclick='viewFollowUp(" . $fu['id'] . "); return false;'>View</a> ";
                                    echo "<a href='#' class='action-btn' onclick='editFollowUp(" . $fu['id'] . "); return false;' style='background-color: #f59e0b;'>Edit</a>";
                                    if ($_SESSION['user_role'] == 'Admin') {
                                        echo " <a href='#' class='action-btn' onclick='deleteRecord(" . $fu['id'] . ", \"follow-up\"); return false;' style='background-color: #dc3545;'>Delete</a>";
                                    }
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr>";
                                echo "<td colspan='9' class='no-results'>";
                                echo "<div style='font-size: 24px; margin-bottom: 10px;'>📭</div>";
                                echo "<div style='font-size: 18px; font-weight: 500; margin-bottom: 10px;'>No Follow Ups Found</div>";

                                if (!empty($active_filters)) {
                                    echo "<div class='suggestion'>No follow ups match your current filters. Try:</div>";
                                    echo "<div class='action-buttons'>";
                                    echo "<button class='btn btn-secondary' onclick='clearAllFilters()'>Clear All Filters</button>";
                                    echo "</div>";
                                } else {
                                    echo "<div class='suggestion'>No follow ups found in the system.</div>";
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

        // Auto-filter functionality
        let filterTimeout;

        function setupAutoFilters() {
            const form = document.getElementById('filterForm');
            if (!form) return;

            // Text inputs
            form.querySelectorAll('input[type="text"]').forEach(input => {
                input.addEventListener('keyup', function(e) {
                    clearTimeout(filterTimeout);
                    filterTimeout = setTimeout(() => submitFilter(), 800);
                });
                input.addEventListener('blur', submitFilter);
            });

            // Select dropdowns
            form.querySelectorAll('select').forEach(select => {
                select.addEventListener('change', submitFilter);
            });

            // Date inputs
            form.querySelectorAll('input[type="date"]').forEach(dateInput => {
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

                    submitFilter();
                });
            });
        }

        function submitFilter() {
            const form = document.getElementById('filterForm');
            if (!form) return;

            const params = new URLSearchParams();
            let hasActiveFilter = false;

            // Collect all non-empty values
            form.querySelectorAll('input[type="text"], select, input[type="date"]').forEach(field => {
                const value = field.value ? field.value.trim() : '';
                if (value && field.name) {
                    params.append(field.name, value);
                    hasActiveFilter = true;
                }
            });

            const baseUrl = window.location.pathname;
            window.location.href = hasActiveFilter ? baseUrl + '?' + params.toString() : baseUrl;
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            setupAutoFilters();

            // Set active menu if function exists
            if (typeof setActiveMenu === 'function') {
                setActiveMenu();
            }

            // Auto-hide messages if function exists
            if (typeof autoHideMessages === 'function') {
                autoHideMessages();
            }
        });

        // View and Edit functions (implement these as needed)
        function viewFollowUp(id) {
            // Implement view functionality
            console.log('View follow up:', id);
        }

        function editFollowUp(id) {
            // Implement edit functionality
            console.log('Edit follow up:', id);
        }
    </script>

    <script src="../assets/js/main.js"></script>
</body>

</html>