<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "muscle_city_gym");

// Load current settings
$sql = "SELECT * FROM gym_settings LIMIT 1";
$result = $conn->query($sql);
$settings = $result->fetch_assoc();

// Load admin user data
$admin_id = intval($_SESSION['user_id']);
$user_sql = "SELECT * FROM users WHERE id = $admin_id";
$user_result = $conn->query($user_sql);
$admin = $user_result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Settings</title>
    <link rel="stylesheet" href="../public/css/settings.css">
    <link rel="stylesheet" href="../public/css/admin-dashboard.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="admin-settings">

<?php include '../partials/adminsidebar.php'; ?>

<!-- Main Content -->
<div class="main-content" id="mainContent">
    <div class="content-header">
        <h2 class="page-title">Settings</h2>
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

    <!-- Settings Content -->
    <div class="settings-container">
        <h1>Settings</h1>
        <p class="description">Manage gym details, admin account settings, and integrations.</p>

        <!-- Success Message -->
        <?php if (isset($_SESSION['settings_saved'])): ?>
            <div class="alert-success">
                <i class="fas fa-check-circle"></i>
                <?= $_SESSION['settings_saved'];
                unset($_SESSION['settings_saved']) ?>
            </div>
        <?php endif; ?>

        <!-- Error Message -->
        <?php if (isset($_SESSION['settings_error'])): ?>
            <div class="alert-error">
                <i class="fas fa-exclamation-triangle"></i>
                <?= $_SESSION['settings_error'];
                unset($_SESSION['settings_error']) ?>
            </div>
        <?php endif; ?>

        <form class="settings-form" method="post" action="settings_crud.php">
            <!-- Gym Information -->
            <div class="settings-section">
                <h2>Gym Information</h2>
                
                <div class="form-field">
                    <label for="gym-name" class="required">Gym Name:</label>
                    <input type="text" id="gym-name" name="gym_name" 
                           value="<?= htmlspecialchars($settings['gym_name'] ?? '') ?>" 
                           placeholder="e.g. Iron Forge Fitness" required>
                    <span class="field-helper">Enter your gym's official name</span>
                </div>

                <div class="form-field">
                    <label for="gym-location" class="required">Location:</label>
                    <input type="text" id="gym-location" name="gym_location" 
                           value="<?= htmlspecialchars($settings['gym_location'] ?? '') ?>" 
                           placeholder="e.g. 123 Main St, City, State" required>
                    <span class="field-helper">Full address including city and state</span>
                </div>

                <div class="form-field">
                    <label for="gym-contact" class="required">Contact Number:</label>
                    <input type="tel" id="gym-contact" name="gym_contact" 
                           value="<?= htmlspecialchars($settings['gym_contact'] ?? '') ?>" 
                           placeholder="e.g. (123) 456-7890" required>
                    <span class="field-helper">Primary contact number for the gym</span>
                </div>
            </div>

            <!-- Admin Account Settings -->
            <div class="settings-section">
                <h2>Admin Account</h2>
                
                <div class="form-field">
                    <label for="admin-username" class="required">Username:</label>
                    <input type="text" id="admin-username" name="admin_username" 
                           value="<?= htmlspecialchars($admin['username']) ?>" 
                           placeholder="Choose a username" required>
                    <span class="field-helper">Your login username</span>
                </div>

                <div class="form-field">
                    <label for="admin-name">Full Name:</label>
                    <input type="text" id="admin-name" name="admin_name" 
                           value="<?= htmlspecialchars($admin['name'] ?? '') ?>" 
                           placeholder="Your full name or display name">
                    <span class="field-helper">Used for display in the system</span>
                </div>

                <div class="form-field">
                    <label for="admin-email" class="required">Admin Email:</label>
                    <input type="email" id="admin-email" name="admin_email" 
                           value="<?= htmlspecialchars($admin['email']) ?>" 
                           placeholder="e.g. admin@example.com" required>
                    <span class="field-helper">This email will be used for system notifications</span>
                </div>

                <div class="form-field">
                    <label for="admin-password">Change Password:</label>
                    <input type="password" id="admin-password" name="admin_password" 
                           placeholder="Enter new password (leave blank to keep current)">
                    <span class="field-helper">Minimum 8 characters recommended</span>
                </div>
            </div>

            <!-- Integrations -->
            <div class="settings-section">
                <h2>Integrations</h2>
                
                <div class="form-field">
                    <label for="payment-gateway">Payment Gateway API Key:</label>
                    <input type="text" id="payment-gateway" name="payment_gateway" 
                           value="<?= htmlspecialchars($settings['payment_gateway_key'] ?? '') ?>" 
                           placeholder="Enter your payment gateway API key">
                    <span class="field-helper">Used for processing membership payments (optional)</span>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i> Save All Settings
                </button>
            </div>
        </form>
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

// Form validation
document.querySelector('.settings-form').addEventListener('submit', function(e) {
    let isValid = true;
    const requiredFields = document.querySelectorAll('input[required]');
    
    requiredFields.forEach(function(field) {
        const formField = field.closest('.form-field');
        
        if (!field.value.trim()) {
            formField.classList.add('error');
            isValid = false;
        } else {
            formField.classList.remove('error');
            formField.classList.add('success');
        }
    });
    
    if (!isValid) {
        e.preventDefault();
        alert('Please fill in all required fields.');
    }
});

// Real-time validation
document.querySelectorAll('input').forEach(function(input) {
    input.addEventListener('blur', function() {
        const formField = this.closest('.form-field');
        
        if (this.hasAttribute('required') && !this.value.trim()) {
            formField.classList.add('error');
            formField.classList.remove('success');
        } else if (this.value.trim()) {
            formField.classList.remove('error');
            formField.classList.add('success');
        }
    });
    
    input.addEventListener('input', function() {
        const formField = this.closest('.form-field');
        formField.classList.remove('error');
    });
});
</script>
</body>
</html>