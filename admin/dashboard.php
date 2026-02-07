<?php
require_once '../includes/config.php';
require_role(['Admin', 'Sales', 'Allocation', 'Support']);

// Get dashboard statistics
$fresh_leads = $conn->query("SELECT COUNT(*) as count FROM leads WHERE status = 'Fresh'")->fetch_assoc()['count'];
$in_progress_leads = $conn->query("SELECT COUNT(*) as count FROM leads WHERE status = 'In progress'")->fetch_assoc()['count'];
$pending_bookings = $conn->query("SELECT COUNT(*) as count FROM bookings WHERE status IN ('In progress', 'Shortlisted', 'Assigned')")->fetch_assoc()['count'];
$pending_payments = $conn->query("SELECT COUNT(*) as count FROM payments WHERE status = 'Pending'")->fetch_assoc()['count'];
$missed_calls = $conn->query("SELECT COUNT(*) as count FROM missed_calls WHERE missed_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetch_assoc()['count'];

// Get recent leads
$recent_leads = $conn->query("SELECT l.id, l.name, l.phone, l.service, l.status, l.created_at, u.name as updated_by_name FROM leads l LEFT JOIN users u ON l.updated_by = u.id ORDER BY l.created_at DESC LIMIT 5");

// Get pending payments
$pending_payment_records = $conn->query("SELECT id, customer_name, customer_phone, total_amount, created_at FROM payments WHERE status = 'Pending' ORDER BY created_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Servon Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <div class="main-content">
            <!-- Header -->
            <div class="header">
                <h1>Dashboard</h1>
                <div class="user-menu">
                    <span>Welcome, <?php echo $_SESSION['user_name']; ?> (<?php echo $_SESSION['user_role']; ?>)</span>
                    <a href="../api/logout.php" class="btn btn-secondary">Logout</a>
                </div>
            </div>

            <!-- Content -->
            <div class="content">
                <!-- Action Button -->
                <div style="margin-bottom: 30px;">
                    <a href="bookings.php?action=new" class="btn btn-primary">+ Create Booking Link</a>
                </div>

                <!-- Dashboard Cards -->
                <div class="dashboard-grid">
                    <div class="dashboard-card">
                        <h3>Fresh Leads</h3>
                        <div class="value"><?php echo $fresh_leads; ?></div>
                    </div>
                    <div class="dashboard-card">
                        <h3>In Progress Leads</h3>
                        <div class="value"><?php echo $in_progress_leads; ?></div>
                    </div>
                    <div class="dashboard-card">
                        <h3>Pending Bookings</h3>
                        <div class="value"><?php echo $pending_bookings; ?></div>
                    </div>
                    <div class="dashboard-card">
                        <h3>Pending Payments</h3>
                        <div class="value"><?php echo $pending_payments; ?></div>
                    </div>
                    <div class="dashboard-card">
                        <h3>Missed Calls (7d)</h3>
                        <div class="value"><?php echo $missed_calls; ?></div>
                    </div>
                </div>

                <!-- Open Leads Section -->
                <div class="table-container" style="margin-bottom: 40px;">
                    <div class="table-header">
                        <h3>Open Leads (Fresh & In Progress)</h3>
                        <a href="leads.php" class="btn btn-secondary">View All</a>
                    </div>
                    <table>
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
                            $counter = 1;
                            if ($recent_leads->num_rows > 0) {
                                while ($lead = $recent_leads->fetch_assoc()) {
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
                                    echo "<td>" . ($lead['updated_by_name'] ? htmlspecialchars($lead['updated_by_name']) : '-') . "</td>";
                                    echo "<td><a href='leads.php?action=view&id=" . $lead['id'] . "' class='action-btn view-btn' data-id='" . $lead['id'] . "' data-type='lead'>View</a></td>";
                                    echo "</tr>";
                                    $counter++;
                                }
                            } else {
                                echo "<tr><td colspan='8' style='text-align: center;'>No open leads</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pending Payments Section -->
                <div class="table-container" style="margin-bottom: 40px;">
                    <div class="table-header">
                        <h3>Pending Payments</h3>
                        <a href="payments.php" class="btn btn-secondary">View All</a>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Serial No</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Amount (Rs)</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $counter = 1;
                            if ($pending_payment_records->num_rows > 0) {
                                while ($payment = $pending_payment_records->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $counter . "</td>";
                                    echo "<td>" . htmlspecialchars($payment['customer_name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($payment['customer_phone']) . "</td>";
                                    echo "<td>₹" . number_format($payment['total_amount'], 2) . "</td>";
                                    echo "<td>" . date('M d, Y H:i', strtotime($payment['created_at'])) . "</td>";
                                    echo "<td><a href='payments.php?action=view&id=" . $payment['id'] . "' class='action-btn view-btn'>View</a></td>";
                                    echo "</tr>";
                                    $counter++;
                                }
                            } else {
                                echo "<tr><td colspan='6' style='text-align: center;'>No pending payments</td></tr>";
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