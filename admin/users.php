<?php
require_once '../includes/config.php';
require_role(['Admin']);

$search = isset($_GET['search']) ? esc($_GET['search']) : '';
$role = isset($_GET['role']) ? esc($_GET['role']) : '';
$enabled = isset($_GET['enabled']) ? esc($_GET['enabled']) : '';
$from_date = isset($_GET['from_date']) ? esc($_GET['from_date']) : '';
$to_date = isset($_GET['to_date']) ? esc($_GET['to_date']) : '';

$where = "1=1";
if ($search) $where .= " AND (name LIKE '%$search%' OR email LIKE '%$search%' OR phone LIKE '%$search%')";
if ($role) $where .= " AND role = '$role'";
if ($enabled !== '') $where .= " AND enabled = " . ($enabled == '1' ? '1' : '0');
if ($from_date) $where .= " AND DATE(created_at) >= '$from_date'";
if ($to_date) $where .= " AND DATE(created_at) <= '$to_date'";

$users = $conn->query("
    SELECT u.*, 
           editor.name as updated_by_name
    FROM users u
    LEFT JOIN users editor ON u.updated_by = editor.id
    WHERE $where 
    ORDER BY u.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users - Servon Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>

        <div class="main-content">
            <div class="header">
                <h1>Users Management</h1>
                <div class="user-menu">
                    <span>Welcome, <?php echo $_SESSION['user_name']; ?></span>
                    <a href="../api/logout.php" class="btn btn-secondary">Logout</a>
                </div>
            </div>

            <div class="content">
                <div style="margin-bottom: 20px;">
                    <button class="btn btn-primary" onclick="openNewUserModal()">+ Add User</button>
                </div>

                <div class="table-container">
                    <div class="filter-section">
                        <form method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; width: 100%;">
                            <div class="filter-group">
                                <label>Search</label>
                                <input type="text" name="search" placeholder="Name or Email" value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                            <div class="filter-group">
                                <label>Role</label>
                                <select name="role">
                                    <option value="">All</option>
                                    <option value="Admin" <?php echo $role == 'Admin' ? 'selected' : ''; ?>>Admin</option>
                                    <option value="Sales" <?php echo $role == 'Sales' ? 'selected' : ''; ?>>Sales</option>
                                    <option value="Allocation" <?php echo $role == 'Allocation' ? 'selected' : ''; ?>>Allocation</option>
                                    <option value="Support" <?php echo $role == 'Support' ? 'selected' : ''; ?>>Support</option>
                                </select>
                            </div>
                            <div class="filter-group">
                                <label>Status</label>
                                <select name="enabled">
                                    <option value="">All</option>
                                    <option value="1" <?php echo $enabled == '1' ? 'selected' : ''; ?>>Enabled</option>
                                    <option value="0" <?php echo $enabled == '0' ? 'selected' : ''; ?>>Disabled</option>
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
                        <h3>All Users</h3>
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
                            $counter = 1;
                            if ($users->num_rows > 0) {
                                while ($user = $users->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $counter . "</td>";
                                    echo "<td>" . htmlspecialchars($user['name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($user['email']) . "</td>";
                                    echo "<td>" . htmlspecialchars($user['phone']) . "</td>";
                                    echo "<td>" . $user['role'] . "</td>";
                                    echo "<td>";
                                    $status_badge = $user['enabled'] ? '<span style="background-color: #d1fae5; padding: 4px 8px; border-radius: 4px;">Enabled</span>' : '<span style="background-color: #fee2e2; padding: 4px 8px; border-radius: 4px;">Disabled</span>';
                                    echo $status_badge . "</td>";
                                    echo "<td>" . date('M d, Y', strtotime($user['created_at'])) . "</td>";
                                    echo "<td>" . ($user['updated_by_name'] ? htmlspecialchars($user['updated_by_name']) : '-') . "</td>";
                                    echo "<td><a href='#' class='action-btn view-btn' data-id='" . $user['id'] . "' data-type='user' onclick='viewUser(" . $user['id'] . "); return false;'>View</a></td>";
                                    echo "</tr>";
                                    $counter++;
                                }
                            } else {
                                echo "<tr><td colspan='9' style='text-align: center;'>No users found</td></tr>";
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