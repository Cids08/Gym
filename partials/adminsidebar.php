
<!-- Fixed Sidebar Toggle Button (outside sidebar) -->
<button class="sidebar-toggle" id="sidebarToggleBtn">
    <i class="fas fa-chevron-left" id="toggleIcon"></i>
</button>

<div class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-header">
        <!-- Admin Profile -->
        <div class="sidebar-profile">
            <div class="profile-pic">
                <?php 
                $name = $_SESSION['name'] ?? 'Admin';
                $initials = strtoupper(substr($name, 0, 1));
                if (strpos($name, ' ') !== false) {
                    $initials .= strtoupper(substr($name, strpos($name, ' ') + 1, 1));
                }
                echo '<span class="profile-initials">' . $initials . '</span>';
                ?>
            </div>
            <div class="profile-info">
                <span class="profile-name"><?=$_SESSION['name'] ?? 'Admin'?></span>
                <span class="profile-role"><?=$_SESSION['role'] ?? 'Admin'?></span>
            </div>
        </div>
    </div>
    
    <!-- Navigation Links -->
    <ul class="nav-links">
        <?php $currentPage = basename($_SERVER['PHP_SELF']); ?>
        <li><a href="AdminDashboard.php" class="<?= $currentPage == 'AdminDashboard.php' ? 'active' : '' ?>">
            <i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="users.php" class="<?= $currentPage == 'users.php' ? 'active' : '' ?>">
            <i class="fas fa-users"></i> Users</a></li>
        <li><a href="membership-plans.php" class="<?= $currentPage == 'membership-plans.php' ? 'active' : '' ?>">
            <i class="fas fa-id-card"></i> Membership</a></li>
        <li><a href="payments.php" class="<?= $currentPage == 'payments.php' ? 'active' : '' ?>">
            <i class="fas fa-credit-card"></i> Payments</a></li>
        <li><a href="reports.php" class="<?= $currentPage == 'reports.php' ? 'active' : '' ?>">
            <i class="fas fa-chart-bar"></i> Reports</a></li>
        <li><a href="settings.php" class="<?= $currentPage == 'settings.php' ? 'active' : '' ?>">
            <i class="fas fa-cog"></i> Settings</a></li>
    </ul>
</div>