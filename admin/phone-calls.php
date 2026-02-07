<?php
require_once '../includes/config.php';
require_role(['Admin', 'Sales']);

$search = isset($_GET['search']) ? esc($_GET['search']) : '';
$direction = isset($_GET['direction']) ? esc($_GET['direction']) : '';
$agent = isset($_GET['agent']) ? esc($_GET['agent']) : '';
$tag = isset($_GET['tag']) ? esc($_GET['tag']) : '';
$from_date = isset($_GET['from_date']) ? esc($_GET['from_date']) : '';
$to_date = isset($_GET['to_date']) ? esc($_GET['to_date']) : '';

$where = "1=1";
if ($search) $where .= " AND (customer_number LIKE '%$search%')";
if ($direction) $where .= " AND direction = '$direction'";
if ($tag) $where .= " AND tag = '$tag'";
if ($from_date) $where .= " AND DATE(created_at) >= '$from_date'";
if ($to_date) $where .= " AND DATE(created_at) <= '$to_date'";

$calls = $conn->query("SELECT c.*, u.name as agent_name FROM phone_calls c LEFT JOIN users u ON c.agent_id = u.id WHERE $where ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phone Calls - Servon Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>

        <div class="main-content">
            <div class="header">
                <h1>Phone Calls Management</h1>
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
                                <label>Number Search</label>
                                <input type="text" name="search" placeholder="Customer Number" value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                            <div class="filter-group">
                                <label>Direction</label>
                                <select name="direction">
                                    <option value="">All</option>
                                    <option value="Inbound" <?php echo $direction == 'Inbound' ? 'selected' : ''; ?>>Inbound</option>
                                    <option value="Outbound" <?php echo $direction == 'Outbound' ? 'selected' : ''; ?>>Outbound</option>
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
                        <h3>All Calls</h3>
                    </div>

                    <table>
                        <thead>
                            <tr>
                                <th>Serial No</th>
                                <th>Direction</th>
                                <th>Customer</th>
                                <th>Agent</th>
                                <th>Duration (sec)</th>
                                <th>Tag</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $counter = 1;
                            if ($calls->num_rows > 0) {
                                while ($call = $calls->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $counter . "</td>";
                                    echo "<td>" . $call['direction'] . "</td>";
                                    echo "<td>" . htmlspecialchars($call['customer_number']) . "</td>";
                                    echo "<td>" . ($call['agent_name'] ? htmlspecialchars($call['agent_name']) : "-") . "</td>";
                                    echo "<td>" . $call['duration'] . "</td>";
                                    echo "<td>" . ($call['tag'] ? htmlspecialchars($call['tag']) : "-") . "</td>";
                                    echo "<td>" . date('M d, Y H:i', strtotime($call['created_at'])) . "</td>";
                                    echo "<td>-</td>";
                                    echo "</tr>";
                                    $counter++;
                                }
                            } else {
                                echo "<tr><td colspan='8' style='text-align: center;'>No calls found</td></tr>";
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