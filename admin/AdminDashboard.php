<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: ../frontend/login.php");
    exit();
}
if ($_SESSION['role'] !== 'admin') {
    header("Location: unauthorized.php");
    exit();
}

// Database connection
$db_host = "localhost";
$db_user = "root"; 
$db_pass = ""; 
$db_name = "muscle_city_gym";

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch Summary Data
$summary = [];

// Total Members
$sql = "SELECT COUNT(*) AS total_users FROM users WHERE role = 'user'";
$result = $conn->query($sql);
$summary['total_users'] = $result->fetch_assoc()['total_users'];

// Active Memberships
$sql = "SELECT COUNT(*) AS active_memberships FROM memberships WHERE status = 'active'";
$result = $conn->query($sql);
$summary['active_memberships'] = $result->fetch_assoc()['active_memberships'];

// Expired Memberships
$sql = "SELECT COUNT(*) AS expired_memberships FROM memberships WHERE status = 'expired'";
$result = $conn->query($sql);
$summary['expired_memberships'] = $result->fetch_assoc()['expired_memberships'];

// Monthly Revenue (current month)
$month = date('m');
$year = date('Y');
$sql = "SELECT SUM(amount) AS monthly_revenue FROM payments WHERE MONTH(payment_date) = ? AND YEAR(payment_date) = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $month, $year);
$stmt->execute();
$result = $stmt->get_result();
$revenue = $result->fetch_assoc();
$summary['monthly_revenue'] = number_format($revenue['monthly_revenue'] ?? 0, 2);

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/css/admin-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="admin-dashboard">

<!-- Sidebar Toggle Button -->
<button class="sidebar-toggle" id="sidebarToggle">
    <i class="fas fa-bars"></i>
</button>

<!-- Sidebar -->
<?php include '../partials/adminsidebar.php'; ?>

<!-- Main Content -->
<div class="main-content" id="mainContent">
    <div class="content-header">
        <h2 class="page-title">Dashboard Overview</h2>
        <div class="header-actions">
            <div class="current-date"><?php echo date('F j, Y'); ?></div>
            <div class="settings-dropdown">
                <button class="settings-btn">
                    <i class="fas fa-cog"></i>
                </button>
                <div class="dropdown-content">
                    <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
                    <a href="../frontend/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Welcome Section -->
    <div class="admin-welcome">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h2>
        <p>You have full access to manage users, reports, and settings.</p>
    </div>

    <!-- Summary Cards -->
    <div class="dashboard-summary">
        <div class="summary-card">
            <div class="summary-icon"><i class="fas fa-users"></i></div>
            <h4>Total Members</h4>
            <p><?php echo $summary['total_users']; ?></p>
        </div>
        <div class="summary-card">
            <div class="summary-icon"><i class="fas fa-user-check"></i></div>
            <h4>Active Memberships</h4>
            <p><?php echo $summary['active_memberships']; ?></p>
        </div>
        <div class="summary-card">
            <div class="summary-icon"><i class="fas fa-user-clock"></i></div>
            <h4>Expired Memberships</h4>
            <p><?php echo $summary['expired_memberships']; ?></p>
        </div>
        <div class="summary-card">
            <div class="summary-icon"><i class="fas fa-dollar-sign"></i></div>
            <h4>Monthly Revenue</h4>
            <p>$<?php echo $summary['monthly_revenue']; ?></p>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="admin-sections">
        <a href="users.php" class="admin-card">
            <div class="card-icon"><i class="fas fa-users"></i></div>
            <h3>Manage Users</h3>
            <p>View, edit, suspend, or delete user accounts.</p>
        </a>

        <a href="reports.php" class="admin-card">
            <div class="card-icon"><i class="fas fa-chart-line"></i></div>
            <h3>System Reports</h3>
            <p>Analyze traffic, logs, and system health stats.</p>
        </a>

        <a href="settings.php" class="admin-card">
            <div class="card-icon"><i class="fas fa-cogs"></i></div>
            <h3>Site Settings</h3>
            <p>Configure platform preferences and appearance.</p>
        </a>
    </div>
</div>

<script>
// Toggle sidebar functionality
document.getElementById('sidebarToggle').addEventListener('click', function() {
    const sidebar = document.getElementById('adminSidebar');
    const mainContent = document.getElementById('mainContent');
    
    sidebar.classList.toggle('collapsed');
    mainContent.classList.toggle('sidebar-collapsed');
    
    // Store state in localStorage
    const isCollapsed = sidebar.classList.contains('collapsed');
    localStorage.setItem('sidebarCollapsed', isCollapsed);
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
    
    if (settingsBtn && dropdownContent) {
        settingsBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdownContent.classList.toggle('show');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function() {
            dropdownContent.classList.remove('show');
        });
    }
});
</script>
</body>
</html>