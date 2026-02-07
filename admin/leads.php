<?php
require_once '../includes/config.php';
require_role(['Admin', 'Sales', 'Allocation', 'Support']);

// Get filter parameters
$search = isset($_GET['search']) ? esc($_GET['search']) : '';
$service = isset($_GET['service']) ? esc($_GET['service']) : '';
$responder = isset($_GET['responder']) ? esc($_GET['responder']) : '';
$status = isset($_GET['status']) ? esc($_GET['status']) : '';
$from_date = isset($_GET['from_date']) ? esc($_GET['from_date']) : '';
$to_date = isset($_GET['to_date']) ? esc($_GET['to_date']) : '';
$action = isset($_GET['action']) ? esc($_GET['action']) : '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get all responders for filter
$responders = $conn->query("SELECT id, name FROM users WHERE role IN ('Admin', 'Sales') ORDER BY name");

// Build query
$where = "1=1";
if ($search) {
    $where .= " AND (name LIKE '%$search%' OR phone LIKE '%$search%')";
}
if ($service) {
    $where .= " AND service = '$service'";
}
if ($responder) {
    $where .= " AND responder_id = $responder";
}
if ($status) {
    $where .= " AND status = '$status'";
}
if ($from_date) {
    $where .= " AND DATE(created_at) >= '$from_date'";
}
if ($to_date) {
    $where .= " AND DATE(created_at) <= '$to_date'";
}

// For non-admin users, show only their assigned leads
if ($_SESSION['user_role'] != 'Admin') {
    $where .= " AND responder_id = " . $_SESSION['user_id'];
}

$leads = $conn->query("SELECT * FROM leads WHERE $where ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leads - Servon Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>

        <div class="main-content">
            <div class="header">
                <h1>Leads Management</h1>
                <div class="user-menu">
                    <span>Welcome, <?php echo $_SESSION['user_name']; ?></span>
                    <a href="../api/logout.php" class="btn btn-secondary">Logout</a>
                </div>
            </div>

            <div class="content">
                <!-- Filter Section -->
                <div class="table-container">
                    <div class="filter-section">
                        <form method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; width: 100%;">
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
                                        <?php while ($resp = $responders->fetch_assoc()): ?>
                                            <option value="<?php echo $resp['id']; ?>" <?php echo $responder == $resp['id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($resp['name']); ?>
                                            </option>
                                        <?php endwhile; ?>
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
                                <label>From Date</label>
                                <input type="date" name="from_date" value="<?php echo $from_date; ?>">
                            </div>

                            <div class="filter-group">
                                <label>To Date</label>
                                <input type="date" name="to_date" value="<?php echo $to_date; ?>">
                            </div>

                            <div class="filter-group" style="display: flex; align-items: flex-end;">
                                <button type="submit" class="btn btn-primary" style="width: 100%; margin: 0;">Filter</button>
                            </div>
                        </form>
                    </div>

                    <!-- Table -->
                    <div class="table-header">
                        <h3>All Leads</h3>
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
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $counter = 1;
                            if ($leads->num_rows > 0) {
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
                                    echo "<td><span style='padding: 4px 8px; border-radius: 4px; background-color: $status_color; color: $status_text_color;'>" . $lead['status'] . "</span></td>";
                                    echo "<td>" . date('M d, Y H:i', strtotime($lead['created_at'])) . "</td>";
                                    echo "<td>";
                                    echo "<a href='#' class='action-btn view-btn' data-id='" . $lead['id'] . "' data-type='lead'>View</a>";
                                    echo "</td>";
                                    echo "</tr>";
                                    $counter++;
                                }
                            } else {
                                echo "<tr><td colspan='7' style='text-align: center;'>No leads found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/main.js"></script>
</body>

</html>