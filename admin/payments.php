<?php
require_once '../includes/config.php';
require_role(['Admin', 'Sales', 'Allocation', 'Support']);

// Get filter parameters
$search = isset($_GET['search']) ? esc($_GET['search']) : '';
$purpose = isset($_GET['purpose']) ? esc($_GET['purpose']) : '';
$payment_method = isset($_GET['payment_method']) ? esc($_GET['payment_method']) : '';
$status = isset($_GET['status']) ? esc($_GET['status']) : '';
$from_date = isset($_GET['from_date']) ? esc($_GET['from_date']) : '';
$to_date = isset($_GET['to_date']) ? esc($_GET['to_date']) : '';

// Build query
$where = "1=1";
if ($search) {
    $where .= " AND (customer_name LIKE '%$search%' OR customer_phone LIKE '%$search%')";
}
if ($purpose) {
    $where .= " AND purpose = '$purpose'";
}
if ($payment_method) {
    $where .= " AND payment_method = '$payment_method'";
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

$payments = $conn->query("SELECT * FROM payments WHERE $where ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments - Servon Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>

        <div class="main-content">
            <div class="header">
                <h1>Payments Management</h1>
                <div class="user-menu">
                    <span>Welcome, <?php echo $_SESSION['user_name']; ?></span>
                    <a href="../api/logout.php" class="btn btn-secondary">Logout</a>
                </div>
            </div>

            <div class="content">
                <!-- New Payment Button -->
                <div style="margin-bottom: 20px;">
                    <button class="btn btn-primary" onclick="openNewPaymentModal()">+ New Payment Link</button>
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
                                <label>Purpose</label>
                                <input type="text" name="purpose" placeholder="Purpose" value="<?php echo htmlspecialchars($purpose); ?>">
                            </div>

                            <div class="filter-group">
                                <label>Status</label>
                                <select name="status">
                                    <option value="">All Status</option>
                                    <option value="Pending" <?php echo $status == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="Completed" <?php echo $status == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                    <option value="Failed" <?php echo $status == 'Failed' ? 'selected' : ''; ?>>Failed</option>
                                    <option value="Refunded" <?php echo $status == 'Refunded' ? 'selected' : ''; ?>>Refunded</option>
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
                        <h3>All Payments</h3>
                    </div>

                    <table>
                        <thead>
                            <tr>
                                <th>Serial No</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Service</th>
                                <th>Amount (Rs)</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $counter = 1;
                            if ($payments->num_rows > 0) {
                                while ($payment = $payments->fetch_assoc()) {
                                    $status_color = match ($payment['status']) {
                                        'Pending' => '#fef3c7',
                                        'Completed' => '#d1fae5',
                                        'Failed' => '#fee2e2',
                                        'Refunded' => '#fecaca',
                                        default => '#f3f4f6'
                                    };

                                    echo "<tr>";
                                    echo "<td>" . $counter . "</td>";
                                    echo "<td>" . htmlspecialchars($payment['customer_name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($payment['customer_phone']) . "</td>";
                                    echo "<td>" . htmlspecialchars($payment['service']) . "</td>";
                                    echo "<td>₹" . number_format($payment['total_amount'], 2) . "</td>";
                                    echo "<td><span style='padding: 4px 8px; border-radius: 4px; background-color: $status_color;'>" . $payment['status'] . "</span></td>";
                                    echo "<td>" . date('M d, Y H:i', strtotime($payment['created_at'])) . "</td>";
                                    echo "<td>";
                                    echo "<a href='#' class='action-btn view-btn' data-id='" . $payment['id'] . "' data-type='payment' onclick='viewPayment(" . $payment['id'] . "); return false;'>View</a>";
                                    echo "</td>";
                                    echo "</tr>";
                                    $counter++;
                                }
                            } else {
                                echo "<tr><td colspan='8' style='text-align: center;'>No payments found</td></tr>";
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