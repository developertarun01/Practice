<?php
require_once '../includes/config.php';
require_role(['Admin', 'Allocation']);

$search = isset($_GET['search']) ? esc($_GET['search']) : '';
$service = isset($_GET['service']) ? esc($_GET['service']) : '';
$status = isset($_GET['status']) ? esc($_GET['status']) : '';
$gender = isset($_GET['gender']) ? esc($_GET['gender']) : '';
$verify_status = isset($_GET['verify_status']) ? esc($_GET['verify_status']) : '';
$from_date = isset($_GET['from_date']) ? esc($_GET['from_date']) : '';
$to_date = isset($_GET['to_date']) ? esc($_GET['to_date']) : '';

$where = "1=1";
if ($search) $where .= " AND (name LIKE '%$search%' OR phone LIKE '%$search%')";
if ($service) $where .= " AND service = '$service'";
if ($status) $where .= " AND status = '$status'";
if ($gender) $where .= " AND gender = '$gender'";
if ($verify_status) $where .= " AND verify_status = '$verify_status'";
if ($from_date) $where .= " AND DATE(created_at) >= '$from_date'";
if ($to_date) $where .= " AND DATE(created_at) <= '$to_date'";

$professionals = $conn->query("SELECT * FROM professionals WHERE $where ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professionals - Servon Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
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
                <div style="margin-bottom: 20px;">
                    <button class="btn btn-primary" onclick="openNewProfessionalModal()">+ Add Professional</button>
                </div>

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
                                    <option value="">All</option>
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
                                    <option value="">All</option>
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
                            <div class="filter-group" style="display: flex; align-items: flex-end;">
                                <button type="submit" class="btn btn-primary" style="width: 100%; margin: 0;">Filter</button>
                            </div>
                        </form>
                    </div>

                    <div class="table-header">
                        <h3>All Professionals</h3>
                    </div>

                    <table>
                        <thead>
                            <tr>
                                <th>Serial No</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Service</th>
                                <th>Experience</th>
                                <th>Rating</th>
                                <th>Status</th>
                                <th>Verification</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $counter = 1;
                            if ($professionals->num_rows > 0) {
                                while ($prof = $professionals->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $counter . "</td>";
                                    echo "<td>" . htmlspecialchars($prof['name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($prof['phone']) . "</td>";
                                    echo "<td>" . htmlspecialchars($prof['service']) . "</td>";
                                    echo "<td>" . ($prof['experience'] ? $prof['experience'] . " yrs" : "-") . "</td>";
                                    echo "<td>⭐ " . $prof['rating'] . "</td>";
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
                                    echo "<td><a href='#' class='action-btn view-btn' data-id='" . $prof['id'] . "' data-type='professional' onclick='viewProfessional(" . $prof['id'] . "); return false;'>View</a></td>";
                                    echo "</tr>";
                                    $counter++;
                                }
                            } else {
                                echo "<tr><td colspan='10' style='text-align: center;'>No professionals found</td></tr>";
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