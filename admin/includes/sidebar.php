<div class="sidebar">
    <div class="logo">Servon</div>
    <ul class="sidebar-menu">
        <li><a href="dashboard.php">📊 Dashboard</a></li>

        <?php if ($_SESSION['user_role'] == 'Admin' || $_SESSION['user_role'] == 'Sales'): ?>
            <li><a href="phone-calls.php">☎️ Phone Calls</a></li>
        <?php endif; ?>

        <li><a href="leads.php">📞 Leads</a></li>
        <li><a href="follow-ups.php">📋 Follow Ups</a></li>
        <li><a href="payments.php">💳 Payments</a></li>

        <?php if ($_SESSION['user_role'] == 'Admin' || $_SESSION['user_role'] == 'Sales' || $_SESSION['user_role'] == 'Allocation'): ?>
            <li><a href="bookings.php">📅 Bookings</a></li>
        <?php endif; ?>

        <?php if ($_SESSION['user_role'] == 'Admin' || $_SESSION['user_role'] == 'Support'): ?>
            <li><a href="service-requests.php">🔧 Service Requests</a></li>
        <?php endif; ?>

        <?php if ($_SESSION['user_role'] == 'Admin' || $_SESSION['user_role'] == 'Allocation'): ?>
            <li><a href="professionals.php">👤 Professionals</a></li>
        <?php endif; ?>

        <?php if ($_SESSION['user_role'] == 'Admin'): ?>
            <li><a href="users.php">👥 Users</a></li>
        <?php endif; ?>
    </ul>
</div>