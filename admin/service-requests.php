<?php
require_once '../includes/config.php';
require_role(['Admin', 'Support']);

$search = isset($_GET['search']) ? esc($_GET['search']) : '';
$status = isset($_GET['status']) ? esc($_GET['status']) : '';
$from_date = isset($_GET['from_date']) ? esc($_GET['from_date']) : '';
$to_date = isset($_GET['to_date']) ? esc($_GET['to_date']) : '';

$where = "1=1";
if ($search) $where .= " AND (p.name LIKE '%$search%' OR p.phone LIKE '%$search%')";
if ($status) $where .= " AND s.status = '$status'";
if ($from_date) $where .= " AND DATE(s.created_at) >= '$from_date'";
if ($to_date) $where .= " AND DATE(s.created_at) <= '$to_date'";

$service_requests = $conn->query("SELECT s.*, p.name as prof_name, p.phone as prof_phone, b.id as booking_id, u.name as created_by_name FROM service_requests s LEFT JOIN professionals p ON s.professional_id = p.id LEFT JOIN bookings b ON s.booking_id = b.id LEFT JOIN users u ON s.created_by = u.id WHERE $where ORDER BY s.created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Requests - Servon Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>

        <div class="main-content">
            <div class="header">
                <h1>Service Requests Management</h1>
                <div class="user-menu">
                    <span>Welcome, <?php echo $_SESSION['user_name']; ?></span>
                    <a href="../api/logout.php" class="btn btn-secondary">Logout</a>
                </div>
            </div>

            <div class="content">
                <div class="table-container">
                    <div class="filter-section">
                        <form method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; width: 100%;">
                            <div class="filter-group">
                                <label>Search</label>
                                <input type="text" name="search" placeholder="Professional Name or Phone" value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                            <div class="filter-group">
                                <label>Status</label>
                                <select name="status">
                                    <option value="">All</option>
                                    <option value="Open" <?php echo $status == 'Open' ? 'selected' : ''; ?>>Open</option>
                                    <option value="Closed" <?php echo $status == 'Closed' ? 'selected' : ''; ?>>Closed</option>
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

                    <div class="table-header">
                        <h3>All Service Requests</h3>
                    </div>

                    <table>
                        <thead>
                            <tr>
                                <th>Serial No</th>
                                <th>Professional</th>
                                <th>Phone</th>
                                <th>Booking ID</th>
                                <th>Status</th>
                                <th>Created By</th>
                                <th>Deployed At</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $counter = 1;
                            if ($service_requests->num_rows > 0) {
                                while ($sr = $service_requests->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $counter . "</td>";
                                    echo "<td>" . (isset($sr['prof_name']) ? htmlspecialchars($sr['prof_name']) : "-") . "</td>";
                                    echo "<td>" . (isset($sr['prof_phone']) ? htmlspecialchars($sr['prof_phone']) : "-") . "</td>";
                                    echo "<td>" . ($sr['booking_id'] ? $sr['booking_id'] : "-") . "</td>";
                                    echo "<td>";
                                    $s_color = $sr['status'] == 'Open' ? '#dbeafe' : '#d1fae5';
                                    echo "<span style='padding: 4px 8px; border-radius: 4px; background-color: $s_color;'>" . $sr['status'] . "</span></td>";
                                    echo "<td>" . (isset($sr['created_by_name']) ? htmlspecialchars($sr['created_by_name']) : "-") . "</td>";
                                    echo "<td>" . ($sr['deployed_at'] ? date('M d, Y H:i', strtotime($sr['deployed_at'])) : "-") . "</td>";
                                    echo "<td>" . date('M d, Y H:i', strtotime($sr['created_at'])) . "</td>";
                                    echo "<td><a href='#' class='action-btn view-btn'>View</a> <a href='#' class='action-btn edit-btn'>Edit</a></td>";
                                    echo "</tr>";
                                    $counter++;
                                }
                            } else {
                                echo "<tr><td colspan='9' style='text-align: center;'>No service requests found</td></tr>";
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