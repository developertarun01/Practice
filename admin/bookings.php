<?php
require_once '../includes/config.php';
require_role(['Admin', 'Sales', 'Allocation']);

// Get filter parameters
$search = isset($_GET['search']) ? esc($_GET['search']) : '';
$service = isset($_GET['service']) ? esc($_GET['service']) : '';
$location = isset($_GET['location']) ? esc($_GET['location']) : '';
$status = isset($_GET['status']) ? esc($_GET['status']) : '';
$from_date = isset($_GET['from_date']) ? esc($_GET['from_date']) : '';
$to_date = isset($_GET['to_date']) ? esc($_GET['to_date']) : '';

// Build query
$where = "1=1";
if ($search) {
    $where .= " AND (customer_name LIKE '%$search%' OR customer_phone LIKE '%$search%')";
}
if ($service) {
    $where .= " AND service = '$service'";
}
if ($location) {
    $where .= " AND full_address LIKE '%$location%'";
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

$bookings = $conn->query("SELECT * FROM bookings WHERE $where ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookings - Servon Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>

        <div class="main-content">
            <div class="header">
                <h1>Bookings Management</h1>
                <div class="user-menu">
                    <span>Welcome, <?php echo $_SESSION['user_name']; ?></span>
                    <a href="../api/logout.php" class="btn btn-secondary">Logout</a>
                </div>
            </div>

            <div class="content">
                <!-- New Booking Button -->
                <div style="margin-bottom: 20px;">
                    <button class="btn btn-primary" onclick="openNewBookingModal()">+ New Booking</button>
                </div>

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

                            <div class="filter-group">
                                <label>Status</label>
                                <select name="status">
                                    <option value="">All Status</option>
                                    <option value="In progress" <?php echo $status == 'In progress' ? 'selected' : ''; ?>>In Progress</option>
                                    <option value="Shortlisted" <?php echo $status == 'Shortlisted' ? 'selected' : ''; ?>>Shortlisted</option>
                                    <option value="Assigned" <?php echo $status == 'Assigned' ? 'selected' : ''; ?>>Assigned</option>
                                    <option value="Deployed" <?php echo $status == 'Deployed' ? 'selected' : ''; ?>>Deployed</option>
                                    <option value="Canceled" <?php echo $status == 'Canceled' ? 'selected' : ''; ?>>Canceled</option>
                                    <option value="Unpaid" <?php echo $status == 'Unpaid' ? 'selected' : ''; ?>>Unpaid</option>
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
                        <h3>All Bookings</h3>
                    </div>

                    <table>
                        <thead>
                            <tr>
                                <th>Serial No</th>
                                <th>Customer</th>
                                <th>Phone</th>
                                <th>Service</th>
                                <th>Status</th>
                                <th>Starts At</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $counter = 1;
                            if ($bookings->num_rows > 0) {
                                while ($booking = $bookings->fetch_assoc()) {
                                    $status_color = match ($booking['status']) {
                                        'In progress' => '#dbeafe',
                                        'Shortlisted' => '#fef3c7',
                                        'Assigned' => '#ddd6fe',
                                        'Deployed' => '#d1fae5',
                                        'Canceled' => '#fee2e2',
                                        'Unpaid' => '#fecaca',
                                        default => '#f3f4f6'
                                    };

                                    echo "<tr>";
                                    echo "<td>" . $counter . "</td>";
                                    echo "<td>" . htmlspecialchars($booking['customer_name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($booking['customer_phone']) . "</td>";
                                    echo "<td>" . htmlspecialchars($booking['service']) . "</td>";
                                    echo "<td><span style='padding: 4px 8px; border-radius: 4px; background-color: $status_color;'>" . $booking['status'] . "</span></td>";
                                    echo "<td>" . ($booking['starts_at'] ? date('M d, Y', strtotime($booking['starts_at'])) : '-') . "</td>";
                                    echo "<td>" . date('M d, Y H:i', strtotime($booking['created_at'])) . "</td>";
                                    echo "<td>";
                                    echo "<a href='#' class='action-btn view-btn' data-id='" . $booking['id'] . "' data-type='booking' onclick='viewBooking(" . $booking['id'] . "); return false;'>View</a>";
                                    echo "</td>";
                                    echo "</tr>";
                                    $counter++;
                                }
                            } else {
                                echo "<tr><td colspan='8' style='text-align: center;'>No bookings found</td></tr>";
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