<?php
require_once '../includes/config.php';
require_role(['Admin', 'Sales', 'Allocation', 'Support']);

// Get follow-ups - admin sees all, others see only their own
$where = "1=1";
if ($_SESSION['user_role'] != 'Admin') {
    $where .= " AND user_id = " . $_SESSION['user_id'];
}

$follow_ups = $conn->query("SELECT f.*, l.name, l.phone, u.name as user_name FROM follow_ups f LEFT JOIN leads l ON f.lead_id = l.id LEFT JOIN users u ON f.user_id = u.id WHERE $where ORDER BY f.created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Follow Ups - Servon Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>

        <div class="main-content">
            <div class="header">
                <h1>Follow Ups Management</h1>
                <div class="user-menu">
                    <span>Welcome, <?php echo $_SESSION['user_name']; ?></span>
                    <a href="../api/logout.php" class="btn btn-secondary">Logout</a>
                </div>
            </div>

            <div class="content">
                <div class="table-container">
                    <div class="table-header">
                        <h3>All Follow Ups</h3>
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
                            $counter = 1;
                            if ($follow_ups->num_rows > 0) {
                                while ($fu = $follow_ups->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $counter . "</td>";
                                    echo "<td>" . (isset($fu['name']) ? htmlspecialchars($fu['name']) : "-") . "</td>";
                                    echo "<td>" . (isset($fu['phone']) ? htmlspecialchars($fu['phone']) : "-") . "</td>";
                                    echo "<td>" . $fu['direction'] . "</td>";
                                    echo "<td>" . $fu['channel'] . "</td>";
                                    echo "<td>" . (isset($fu['comments']) ? htmlspecialchars(substr($fu['comments'], 0, 50)) . "..." : "-") . "</td>";
                                    echo "<td>" . ($fu['reminder_at'] ? date('M d, Y H:i', strtotime($fu['reminder_at'])) : "-") . "</td>";
                                    echo "<td>" . date('M d, Y H:i', strtotime($fu['created_at'])) . "</td>";
                                    echo "<td><a href='#' class='action-btn view-btn'>View</a></td>";
                                    echo "</tr>";
                                    $counter++;
                                }
                            } else {
                                echo "<tr><td colspan='9' style='text-align: center;'>No follow ups found</td></tr>";
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