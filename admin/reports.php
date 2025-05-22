<?php
session_start();

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$page = isset($_GET['page']) ? $_GET['page'] : 'signups';  // Default report is "Sign-ups"
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>System Reports - Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/css/admin-dashboard.css">
    <link rel="stylesheet" href="../public/css/reports.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css ">
</head>
<body class="admin-dashboard">

<?php include '../partials/adminsidebar.php'; ?>

<!-- Main Content -->
<div class="main-content" id="mainContent">
    <div class="content-header">
        <h2 class="page-title">System Reports</h2>
        <div class="header-actions">
            <div class="current-date"><?php echo date('F j, Y'); ?></div>
            <div class="settings-dropdown">
                <button class="settings-btn">
                    <i class="fas fa-cog"></i>
                </button>
                <div class="dropdown-content">
                    <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
                    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>
        </div>
    </div>

    <div class="admin-container">
        <!-- Navigation for report sections -->
        <div class="reports-nav">
            <a href="?page=signups" class="<?= $page === 'signups' ? 'active' : '' ?>">
                <i class="fas fa-user-plus"></i> Sign-ups
            </a>
            <a href="?page=checkin" class="<?= $page === 'checkin' ? 'active' : '' ?>">
                <i class="fas fa-calendar-check"></i> Check-in Activity
            </a>
            <a href="?page=revenue" class="<?= $page === 'revenue' ? 'active' : '' ?>">
                <i class="fas fa-dollar-sign"></i> Revenue Trends
            </a>
            <a href="?page=membership" class="<?= $page === 'membership' ? 'active' : '' ?>">
                <i class="fas fa-id-card-alt"></i> Membership Status
            </a>
        </div>

        <!-- Content for selected report -->
        <div class="report-content">
            <?php
            if ($page === 'signups') {
                include 'reports-signups.php';
            } elseif ($page === 'checkin') {
                include 'reports-checkin.php';
            } elseif ($page === 'revenue') {
                include 'reports-revenue.php';
            } elseif ($page === 'membership') {
                include 'reports-membership.php';
            } else {
                echo "<p>Report page not found.</p>";
            }
            ?>
        </div>
    </div>
</div>

<script>
// Toggle sidebar functionality
document.getElementById('sidebarToggle').addEventListener('click', function() {
    const sidebar = document.getElementById('adminSidebar');
    const mainContent = document.getElementById('mainContent');

    sidebar.classList.toggle('collapsed');
    mainContent.classList.toggle('sidebar-collapsed');

    localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
});

// Check for saved state on page load
document.addEventListener('DOMContentLoaded', function() {
    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    if (isCollapsed) {
        document.getElementById('adminSidebar').classList.add('collapsed');
        document.getElementById('mainContent').classList.add('sidebar-collapsed');
    }

    // Settings dropdown functionality
    const settingsBtn = document.querySelector('.settings-btn');
    const dropdownContent = document.querySelector('.dropdown-content');

    settingsBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        dropdownContent.classList.toggle('show');
    });

    document.addEventListener('click', function() {
        dropdownContent.classList.remove('show');
    });
});
</script>

</body>
</html>