<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../frontend/index.php");
    exit();
}

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// DB Connection
$host = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "muscle_city_gym";
$conn = new mysqli($host, $dbUsername, $dbPassword, $dbName);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

// Get user information
$sql = "SELECT username, first_name, last_name, email, age, gender, phone, profile_picture, created_at FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Get membership information
$sql = "SELECT package_name, expiry_date, payment_method, status, created_at FROM memberships WHERE user_id = ? ORDER BY created_at DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$membership = $result->fetch_assoc();
$stmt->close();

// Calculate days remaining and membership status
$days_remaining = 0;
$membership_status = 'inactive';

// Define package durations
$package_durations = [
 'Basic Membership' => 30, 
 'Standard Membership' => 90,
 'Premium Membership' => 180
];

if ($membership) {
 $package_name = $membership['package_name'];
 $start_date = $membership['created_at']; // When the membership was created
 $expiry_date = $membership['expiry_date'];
} 

// Initialize default values to avoid "undefined variable" warnings
$package_name = '';
$start_date = '';
$expiry_date = '';

$package_name='';$start_date='';$expiry_date='';if($membership){$package_name=$membership['package_name'];$start_date=$membership['created_at'];$expiry_date=$membership['expiry_date'];if(empty($expiry_date)&&isset($package_durations[$package_name])){$start=new DateTime($start_date);$start->modify("+{$package_durations[$package_name]} days");$expiry_date=$start->format('Y-m-d');$membership['expiry_date']=$expiry_date;}}

// Check if expiry_date is set and calculate days remaining
if (!empty($expiry_date)) {
    $expiry = new DateTime($expiry_date);
    $today = new DateTime();
    $diff = $today->diff($expiry);

    if ($expiry >= $today) {
        $days_remaining = $diff->days;
        $membership_status = 'active';
    } else {
        $days_remaining = -$diff->days;
        $membership_status = 'expired';
    }
}

// Get package prices for reference
$package_prices = [
    'Basic Membership' => '₱850/month',
    'Standard Membership' => '₱2400/3 months',
    'Premium Membership' => '₱6500/6 months'
];

// Generate user initials
function getUserInitials($firstName, $lastName, $username) {
    if (!empty($firstName) && !empty($lastName)) {
        return strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1));
    } elseif (!empty($firstName)) {
        return strtoupper(substr($firstName, 0, 2));
    } else {
        return strtoupper(substr($username, 0, 2));
    }
}

$userInitials = getUserInitials($user['first_name'], $user['last_name'], $user['username']);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Muscle City Gym</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0a0a0a, #1a1a1a, #2a2a2a);
            color: #ffffff;
            min-height: 100vh;
            line-height: 1.6;
            overflow-x: hidden;
        }

        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
            min-height: 100vh;
        }

        .dashboard-header {
            background: linear-gradient(135deg, #FF6B35, #F7931E, #FF8C42);
            padding: 40px;
            border-radius: 25px;
            margin-bottom: 30px;
            box-shadow: 0 15px 40px rgba(255, 107, 53, 0.3);
            position: relative;
            overflow: hidden;
        }

        .dashboard-header::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .dashboard-header::after {
            content: '';
            position: absolute;
            bottom: -30px;
            left: -30px;
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            animation: float 8s ease-in-out infinite reverse;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        .profile-section {
            display: flex;
            align-items: center;
            gap: 30px;
            position: relative;
            z-index: 2;
        }

        .profile-picture {
            position: relative;
        }

        .profile-initials {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1a1a1a, #333333);
            border: 5px solid rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 38px;
            font-weight: 800;
            color: #FF6B35;
            cursor: pointer;
            transition: all 0.4s ease;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);
        }

        .profile-initials:hover {
            transform: scale(1.08) rotate(5deg);
            box-shadow: 0 15px 35px rgba(255, 107, 53, 0.5);
        }

        .profile-upload-btn {
            position: absolute;
            bottom: 5px;
            right: 5px;
            background: linear-gradient(135deg, #FF6B35, #F7931E);
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .profile-upload-btn:hover {
            background: linear-gradient(135deg, #F7931E, #FF6B35);
            transform: scale(1.15);
        }

        .user-info h1 {
            margin: 0 0 15px 0;
            font-size: 3em;
            font-weight: 800;
            text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.4);
            background: linear-gradient(45deg, #ffffff, #f0f0f0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .user-details {
            display: flex;
            gap: 25px;
            opacity: 0.95;
            flex-wrap: wrap;
        }

        .user-details span {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(255, 255, 255, 0.15);
            padding: 12px 18px;
            border-radius: 30px;
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .user-details span:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .main-content {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }

        .sidebar {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        .dashboard-card {
            background: linear-gradient(135deg, #1a1a1a, #2d2d2d, #1f1f1f);
            border-radius: 25px;
            padding: 35px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.6);
            border: 1px solid #333333;
            backdrop-filter: blur(10px);
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }

        .dashboard-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #FF6B35, #F7931E, #FF8C42);
            border-radius: 25px 25px 0 0;
        }

        .dashboard-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 50px rgba(255, 107, 53, 0.15);
            border-color: #FF6B35;
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #333333;
        }

        .card-header h3 {
            display: flex;
            align-items: center;
            gap: 15px;
            margin: 0;
            color: #FF6B35;
            font-size: 1.6em;
            font-weight: 700;
        }

        .membership-status {
            display: inline-block;
            padding: 12px 25px;
            border-radius: 35px;
            font-weight: 800;
            font-size: 0.95em;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 25px;
        }

        .status-active {
            background: linear-gradient(135deg, #27ae60, #2ecc71, #58d68d);
            color: #ffffff;
            box-shadow: 0 8px 20px rgba(39, 174, 96, 0.4);
        }

        .status-expired {
            background: linear-gradient(135deg, #e74c3c, #c0392b, #ec7063);
            color: #ffffff;
            box-shadow: 0 8px 20px rgba(231, 76, 60, 0.4);
        }

        .status-inactive {
            background: linear-gradient(135deg, #f39c12, #e67e22, #f8c471);
            color: #ffffff;
            box-shadow: 0 8px 20px rgba(243, 156, 18, 0.4);
        }

        .membership-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .info-item {
            padding: 25px;
            background: linear-gradient(135deg, #333333, #404040, #363636);
            border-radius: 18px;
            text-align: center;
            border: 1px solid #555555;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }

        .info-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 107, 53, 0.1), transparent);
            transition: left 0.6s ease;
        }

        .info-item:hover::before {
            left: 100%;
        }

        .info-item:hover {
            background: linear-gradient(135deg, #404040, #4d4d4d, #434343);
            border-color: #FF6B35;
            transform: translateY(-5px);
        }

        .info-item .label {
            font-size: 0.9em;
            color: #cccccc;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }

        .info-item .value {
            font-size: 1.4em;
            font-weight: 800;
            color: #FF6B35;
        }

        .days-remaining {
            text-align: center;
            padding: 35px;
            background: linear-gradient(135deg, #FF6B35, #F7931E, #FF8C42);
            color: white;
            border-radius: 25px;
            margin-bottom: 30px;
            box-shadow: 0 15px 35px rgba(255, 107, 53, 0.4);
            position: relative;
            overflow: hidden;
        }

        .days-remaining::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 120px;
            height: 120px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 8s ease-in-out infinite;
        }

        .days-number {
            font-size: 4em;
            font-weight: 900;
            margin-bottom: 10px;
            text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.4);
        }

        /* Enhanced Button Styles */
        .btn-group {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 25px;
        }

        .btn-primary, .btn-secondary, .btn-danger, .btn-warning {
            padding: 15px 25px;
            border: none;
            border-radius: 15px;
            cursor: pointer;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-align: center;
            transition: all 0.4s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9em;
            position: relative;
            overflow: hidden;
        }

        .btn-primary::before, .btn-secondary::before, .btn-danger::before, .btn-warning::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.6s ease;
        }

        .btn-primary:hover::before, .btn-secondary:hover::before, .btn-danger:hover::before, .btn-warning:hover::before {
            left: 100%;
        }

        .btn-primary {
            background: linear-gradient(135deg, #FF6B35, #F7931E);
            color: white;
            box-shadow: 0 8px 20px rgba(255, 107, 53, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(255, 107, 53, 0.4);
            background: linear-gradient(135deg, #F7931E, #FF6B35);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6c757d, #5a6268);
            color: white;
            box-shadow: 0 8px 20px rgba(108, 117, 125, 0.3);
        }

        .btn-secondary:hover {
            background: linear-gradient(135deg, #5a6268, #495057);
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(108, 117, 125, 0.4);
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            box-shadow: 0 8px 20px rgba(220, 53, 69, 0.3);
        }

        .btn-danger:hover {
            background: linear-gradient(135deg, #c82333, #bd2130);
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(220, 53, 69, 0.4);
        }

        .btn-warning {
            background: linear-gradient(135deg, #ffc107, #e0a800);
            color: #212529;
            box-shadow: 0 8px 20px rgba(255, 193, 7, 0.3);
        }

        .btn-warning:hover {
            background: linear-gradient(135deg, #e0a800, #d39e00);
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(255, 193, 7, 0.4);
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 12px;
            font-weight: 700;
            color: #FF6B35;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9em;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 18px;
            border: 2px solid #333333;
            border-radius: 15px;
            font-size: 1em;
            background: linear-gradient(135deg, #2d2d2d, #3d3d3d);
            color: #ffffff;
            transition: all 0.4s ease;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #FF6B35;
            box-shadow: 0 0 0 4px rgba(255, 107, 53, 0.15);
            background: linear-gradient(135deg, #3d3d3d, #4d4d4d);
            transform: translateY(-2px);
        }

        .quick-actions {
            display: grid;
            grid-template-columns: 1fr;
            gap: 15px;
        }

        .action-btn {
            padding: 20px;
            background: linear-gradient(135deg, #2d2d2d, #3d3d3d);
            border: 2px solid #333333;
            border-radius: 18px;
            text-align: left;
            cursor: pointer;
            transition: all 0.4s ease;
            text-decoration: none;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 18px;
            position: relative;
            overflow: hidden;
        }

        .action-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 107, 53, 0.1), transparent);
            transition: left 0.6s ease;
        }

        .action-btn:hover::before {
            left: 100%;
        }

        .action-btn:hover {
            border-color: #FF6B35;
            background: linear-gradient(135deg, #3d3d3d, #4d4d4d);
            transform: translateX(8px);
            box-shadow: 0 10px 25px rgba(255, 107, 53, 0.2);
        }

        .action-btn i {
            font-size: 1.6em;
            color: #FF6B35;
            width: 35px;
            text-align: center;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 18px;
        }

        .stat-card {
            background: linear-gradient(135deg, #2d2d2d, #3d3d3d);
            padding: 28px;
            border-radius: 18px;
            text-align: center;
            border-left: 5px solid #FF6B35;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 107, 53, 0.05), transparent);
            transition: right 0.6s ease;
        }

        .stat-card:hover::before {
            right: 100%;
        }

        .stat-card:hover {
            transform: scale(1.05);
            box-shadow: 0 15px 30px rgba(255, 107, 53, 0.25);
            border-left-color: #F7931E;
        }

        .stat-number {
            font-size: 2.8em;
            font-weight: 800;
            color: #FF6B35;
            margin-bottom: 12px;
        }

        .stat-label {
            color: #cccccc;
            font-size: 0.9em;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }

        .no-membership {
            text-align: center;
            padding: 60px;
            background: linear-gradient(135deg, #FF6B35, #F7931E);
            color: white;
            border-radius: 25px;
            box-shadow: 0 15px 35px rgba(255, 107, 53, 0.4);
        }

        .no-membership i {
            font-size: 4.5em;
            margin-bottom: 30px;
            opacity: 0.9;
        }

        .no-membership h3 {
            margin-bottom: 25px;
            font-size: 2em;
            font-weight: 800;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(8px);
        }

        .modal-content {
            background: linear-gradient(135deg, #1a1a1a, #2d2d2d);
            margin: 3% auto;
            padding: 45px;
            border-radius: 25px;
            width: 90%;
            max-width: 550px;
            position: relative;
            border: 1px solid #333333;
            box-shadow: 0 25px 70px rgba(0, 0, 0, 0.6);
        }

        .close {
            position: absolute;
            right: 30px;
            top: 25px;
            font-size: 35px;
            font-weight: bold;
            cursor: pointer;
            color: #cccccc;
            transition: all 0.3s ease;
        }

        .close:hover {
            color: #FF6B35;
            transform: scale(1.2) rotate(90deg);
        }

        .benefits-list {
            list-style: none;
            padding: 0;
        }

        .benefits-list li {
            padding: 15px 0;
            border-bottom: 1px solid #333333;
            display: flex;
            align-items: center;
            gap: 18px;
            transition: all 0.3s ease;
        }

        .benefits-list li:hover {
            color: #FF6B35;
            transform: translateX(5px);
        }

        .benefits-list li:last-child {
            border-bottom: none;
        }

        .benefits-list i {
            color: #27ae60;
            font-size: 1.3em;
        }

        .logout-btn {
            background: linear-gradient(135deg, #e74c3c, #c0392b) !important;
            color: white;
        }

        .logout-btn:hover {
            background: linear-gradient(135deg, #c0392b, #a93226) !important;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
            
            .profile-section {
                flex-direction: column;
                text-align: center;
                gap: 20px;
            }
            
            .user-details {
                flex-direction: column;
                gap: 12px;
            }
            
            .membership-info {
                grid-template-columns: 1fr;
            }

            .dashboard-container {
                padding: 15px;
            }

            .user-info h1 {
                font-size: 2.4em;
            }

            .days-number {
                font-size: 3.2em;
            }

            .btn-group {
                grid-template-columns: 1fr;
            }
        }

        /* Animations */
        .notification {
            animation: slideIn 0.5s ease;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #1a1a1a;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #FF6B35, #F7931E);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #F7931E, #FF6B35);
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div class="profile-section">
            <div class="profile-picture">
                <div class="profile-initials" onclick="openProfileModal()">
                    <?= $userInitials ?>
                </div>
                <button class="profile-upload-btn" onclick="openProfileModal()">
                    <i class="fas fa-camera"></i>
                </button>
            </div>
            <div class="user-info">
                <h1>Welcome back, <?= htmlspecialchars($user['first_name'] ?? $user['username']) ?>!</h1>
                <div class="user-details">
                    <span><i class="fas fa-user"></i> <?= htmlspecialchars($user['username']) ?></span>
                    <span><i class="fas fa-envelope"></i> <?= htmlspecialchars($user['email']) ?></span>
                    <span><i class="fas fa-calendar"></i> Member since <?= date('M Y', strtotime($user['created_at'])) ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Dashboard Grid -->
    <div class="dashboard-grid">
        <!-- Main Content -->
        <div class="main-content">
            <!-- Membership Status Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h3><i class="fas fa-id-card"></i> Membership Status</h3>
                </div>

                <?php if ($membership): ?>
                    <div class="membership-status status-<?= $membership_status ?>">
                        <?= ucfirst($membership_status) ?>
                    </div>

                    <?php if ($membership_status === 'active'): ?>
                        <div class="days-remaining">
                            <div class="days-number"><?= $days_remaining ?></div>
                            <div>Days Remaining</div>
                        </div>
                    <?php elseif ($membership_status === 'expired'): ?>
                        <div class="days-remaining" style="background: linear-gradient(135deg, #e74c3c, #c0392b, #ec7063); padding: 35px; border-radius: 25px; color: white;">
                            <div class="days-number" style="font-size: 4em; font-weight: 900;"><?= abs($days_remaining) ?></div>
                            <div>Days Expired</div>
                            <div style="font-size: 0.9em; margin-top: 5px;">
                                (Expired on <?= !empty($membership['expiry_date']) ? date('M d, Y', strtotime($membership['expiry_date'])) : 'N/A' ?>)
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="membership-info">
                        <div class="info-item">
                            <div class="label">Package</div>
                            <div class="value"><?= htmlspecialchars($membership['package_name']) ?></div>
                        </div>
                        <div class="info-item">
                            <div class="label">Price</div>
                            <div class="value"><?= $package_prices[$membership['package_name']] ?? 'N/A' ?></div>
                        </div>
                        <div class="info-item">
                            <div class="label">Payment Method</div>
                            <div class="value"><?= htmlspecialchars($membership['payment_method']) ?></div>
                        </div>
                        <div class="info-item">
                            <div class="label">Expiry Date</div>
                            <div class="value">
                                <?= !empty($membership['expiry_date']) ? date('M d, Y', strtotime($membership['expiry_date'])) : 'N/A' ?>
                            </div>
                        </div>
                    </div>

                    <!-- Enhanced Action Buttons -->
                    <div class="btn-group">
                        <?php if ($membership_status === 'expired' || $days_remaining <= 7): ?>
                            <a href="package.php" class="btn-primary">
                                <i class="fas fa-refresh"></i> Renew Membership
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($membership['package_name'] !== 'Premium Membership'): ?>
                            <a href="package.php" class="btn-warning" onclick="upgradeMembership()">
                                <i class="fas fa-arrow-up"></i> Upgrade Plan
                            </a>
                        <?php endif; ?>
                        
                        <button type="button" class="btn-danger" onclick="endSubscription()">
                            <i class="fas fa-times-circle"></i> End Subscription
                        </button>
                    </div>

                <?php else: ?>
                    <div class="no-membership">
                        <i class="fas fa-exclamation-triangle"></i>
                        <h3>No Active Membership</h3>
                        <p>Start your fitness journey today! Choose from our flexible membership packages.</p>
                        <a href="package.php" class="btn-primary">
                            <i class="fas fa-plus"></i> Get Membership
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Profile Management Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h3><i class="fas fa-user-edit"></i> Profile Management</h3>
                </div>
                
                <form id="profileForm" action="update_profile.php" method="POST" enctype="multipart/form-data">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label for="first_name">First Name</label>
                            <input type="text" name="first_name" id="first_name" value="<?= htmlspecialchars($user['first_name'] ?? '') ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="last_name">Last Name</label>
                            <input type="text" name="last_name" id="last_name" value="<?= htmlspecialchars($user['last_name'] ?? '') ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" name="username" id="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label for="age">Age</label>
                            <input type="number" name="age" id="age" value="<?= htmlspecialchars($user['age'] ?? '') ?>" min="13" max="120">
                        </div>
                        
                        <div class="form-group">
                            <label for="gender">Gender</label>
                            <select name="gender" id="gender">
                                <option value="">Select Gender</option>
                                <option value="male" <?= ($user['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Male</option>
                                <option value="female" <?= ($user['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Female</option>
                                <option value="other" <?= ($user['gender'] ?? '') === 'other' ? 'selected' : '' ?>>Other</option>
                                <option value="prefer_not_to_say" <?= ($user['gender'] ?? '') === 'prefer_not_to_say' ? 'selected' : '' ?>>Prefer not to say</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="text" name="phone" id="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 15px;">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i> Update Profile
                        </button>
                        <button type="button" class="btn-secondary" onclick="resetForm()">
                            <i class="fas fa-undo"></i> Reset
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Quick Actions -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
                </div>
                
                <div class="quick-actions">
                    <a href="package.php" class="action-btn">
                        <i class="fas fa-dumbbell"></i>
                        <div>View Packages</div>
                    </a>
                    
                    <a href="#" class="action-btn" onclick="changePassword()">
                        <i class="fas fa-key"></i>
                        <div>Change Password</div>
                    </a>
                    
                    <a href="../frontend/index.php" class="action-btn logout-btn" onclick="return confirm('Are you sure you want to logout?')">
                        <i class="fas fa-sign-out-alt"></i>
                        <div>Logout</div>
                    </a>
                </div>
            </div>

            <!-- Account Stats -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h3><i class="fas fa-chart-bar"></i> Account Stats</h3>
                </div>
                
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number"><?= $user_id ?></div>
                        <div class="stat-label">Member ID</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-number"><?= $membership ? '1' : '0' ?></div>
                        <div class="stat-label">Active Plans</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-number"><?= date('M d Y', strtotime($user['created_at'])) ?></div>
                        <div class="stat-label">Join Date</div>
                    </div>
                </div>
            </div>

            <!-- Member Benefits -->
            <?php if ($membership && $membership_status === 'active'): ?>
            <div class="dashboard-card">
                <div class="card-header">
                    <h3><i class="fas fa-gift"></i> Your Benefits</h3>
                </div>
                
                <?php
                $benefits = [];
                switch($membership['package_name']) {
                    case 'Basic Membership':
                        $benefits = [
                            'Gym access (Weekdays, 6 AM – 12 PM)',
                            'Access to basic video tutorials',
                            'Free gym towel (one-time gift)'
                        ];
                        break;
                    case 'Standard Membership':
                        $benefits = [
                            'Gym access (Mon–Sat, full hours)',
                            'Access to full video tutorial library',
                            'Free gym shirt'
                        ];
                        break;
                    case 'Premium Membership':
                        $benefits = [
                            'Unlimited gym access (7 days a week)',
                            'Access to all exclusive video content',
                            'Free gym kit (shirt, towel, water bottle)'
                        ];
                        break;
                }
                ?>
                
                <ul class="benefits-list">
                    <?php foreach($benefits as $benefit): ?>
                        <li>
                            <i class="fas fa-check"></i>
                            <?= htmlspecialchars($benefit) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Profile Picture Modal -->
<div id="profileModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeProfileModal()">&times;</span>
        <h2 style="color: #FF6B35; margin-bottom: 25px;">Update Profile Picture</h2>
        <form action="update_profile_picture.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="profile_picture">Choose new profile picture:</label>
                <input type="file" name="profile_picture" id="profile_picture" accept="image/*" required>
            </div>
            <div style="display: flex; gap: 15px;">
                <button type="submit" class="btn-primary">Upload</button>
                <button type="button" class="btn-secondary" onclick="closeProfileModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Change Password Modal -->
<div id="passwordModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closePasswordModal()">&times;</span>
        <h2 style="color: #FF6B35; margin-bottom: 25px;">Change Password</h2>
        <form action="change_password.php" method="POST">
            <div class="form-group">
                <label for="current_password">Current Password:</label>
                <input type="password" name="current_password" id="current_password" required>
            </div>
            <div class="form-group">
                <label for="new_password">New Password:</label>
                <input type="password" name="new_password" id="new_password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm New Password:</label>
                <input type="password" name="confirm_password" id="confirm_password" required>
            </div>
            <div style="display: flex; gap: 15px;">
                <button type="submit" class="btn-primary">Change Password</button>
                <button type="button" class="btn-secondary" onclick="closePasswordModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- End Subscription Modal -->
<div id="endSubscriptionModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEndSubscriptionModal()">&times;</span>
        <h2 style="color: #e74c3c; margin-bottom: 25px;">End Subscription</h2>
        <div style="text-align: center; margin-bottom: 30px;">
            <i class="fas fa-exclamation-triangle" style="font-size: 4em; color: #e74c3c; margin-bottom: 20px;"></i>
            <h3 style="color: #e74c3c; margin-bottom: 15px;">Are you sure?</h3>
            <p style="color: #cccccc; line-height: 1.6;">
                Ending your subscription will cancel your membership at the end of the current billing period.
            </p>
        </div>
        <form action="end_subscription.php" method="POST">
            <div class="form-group">
                <label for="cancellation_reason">Reason for cancellation (optional):</label>
                <select name="cancellation_reason" id="cancellation_reason">
                    <option value="">Select a reason</option>
                    <option value="too_expensive">Too expensive</option>
                    <option value="not_using">Not using enough</option>
                    <option value="moving">Moving location</option>
                    <option value="health_issues">Health issues</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div style="display: flex; gap: 15px;">
                <button type="submit" class="btn-danger" style="flex: 1;">
                    <i class="fas fa-times-circle"></i> End Subscription
                </button>
                <button type="button" class="btn-secondary" onclick="closeEndSubscriptionModal()">Keep Subscription</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Profile picture modal functions
    function openProfileModal() {
        document.getElementById('profileModal').style.display = 'block';
    }
    
    function closeProfileModal() {
        document.getElementById('profileModal').style.display = 'none';
    }
    
    // Password modal functions
    function changePassword() {
        document.getElementById('passwordModal').style.display = 'block';
    }
    
    function closePasswordModal() {
        document.getElementById('passwordModal').style.display = 'none';
    }

    // End subscription modal functions
    function endSubscription() {
        document.getElementById('endSubscriptionModal').style.display = 'block';
    }
    
    function closeEndSubscriptionModal() {
        document.getElementById('endSubscriptionModal').style.display = 'none';
    }

    // Upgrade membership function
    function upgradeMembership() {
        window.location.href = 'package.php';
    }
    
    // Close modals when clicking outside
    window.addEventListener('click', function(event) {
        const profileModal = document.getElementById('profileModal');
        const passwordModal = document.getElementById('passwordModal');
        const endSubscriptionModal = document.getElementById('endSubscriptionModal');
        
        if (event.target === profileModal) {
            profileModal.style.display = 'none';
        }
        if (event.target === passwordModal) {
            passwordModal.style.display = 'none';
        }
        if (event.target === endSubscriptionModal) {
            endSubscriptionModal.style.display = 'none';
        }
    });
    
    // Reset form function
    function resetForm() {
        document.getElementById('profileForm').reset();
    }
    
    // Form submission with AJAX (optional enhancement)
    document.getElementById('profileForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        try {
            const response = await fetch('update_profile.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                showNotification('Profile updated successfully!', 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showNotification(result.message || 'Update failed', 'error');
            }
        } catch (error) {
            showNotification('An error occurred', 'error');
        }
    });
    
    // Notification function
    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 20px 25px;
            border-radius: 15px;
            color: white;
            font-weight: 700;
            z-index: 10000;
            opacity: 1;
            transition: all 0.5s ease;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
            ${type === 'success' ? 'background: linear-gradient(135deg, #27ae60, #2ecc71);' : 'background: linear-gradient(135deg, #e74c3c, #c0392b);'}
        `;
        notification.textContent = message;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => notification.remove(), 500);
        }, 3000);
    }

    // Add smooth scrolling effect
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.dashboard-card');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            
            setTimeout(() => {
                card.style.transition = 'all 0.6s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 150);
        });

        // Add floating animation to profile initials
        const profileInitials = document.querySelector('.profile-initials');
        if (profileInitials) {
            setInterval(() => {
                profileInitials.style.transform = 'scale(1.02)';
                setTimeout(() => {
                    profileInitials.style.transform = 'scale(1)';
                }, 1000);
            }, 3000);
        }

        // Create particle effect
        function createParticle() {
            const particle = document.createElement('div');
            particle.style.cssText = `
                position: absolute;
                width: 4px;
                height: 4px;
                background: rgba(255, 255, 255, 0.6);
                border-radius: 50%;
                pointer-events: none;
                animation: particleFloat 4s linear infinite;
            `;
            
            particle.style.left = Math.random() * 100 + '%';
            particle.style.animationDelay = Math.random() * 4 + 's';
            
            const header = document.querySelector('.dashboard-header');
            if (header) {
                header.appendChild(particle);
                
                setTimeout(() => {
                    if (particle.parentNode) {
                        particle.remove();
                    }
                }, 4000);
            }
        }

        // Create particles periodically
        setInterval(createParticle, 800);

        // Add CSS animation for particles
        const style = document.createElement('style');
        style.textContent = `
            @keyframes particleFloat {
                0% {
                    transform: translateY(0) rotate(0deg);
                    opacity: 0;
                }
                10% {
                    opacity: 1;
                }
                90% {
                    opacity: 1;
                }
                100% {
                    transform: translateY(-100px) rotate(360deg);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    });

// Enhanced toggle sidebar functionality
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
    const sidebar = document.getElementById('adminSidebar');
    const mainContent = document.getElementById('mainContent');
    const toggleIcon = document.getElementById('toggleIcon');
    
    // Function to toggle sidebar
    function toggleSidebar() {
        const isCurrentlyCollapsed = sidebar.classList.contains('collapsed');
        
        sidebar.classList.toggle('collapsed');
        if (mainContent) {
            mainContent.classList.toggle('sidebar-collapsed');
        }
        
        // Update toggle button position and icon
        if (isCurrentlyCollapsed) {
            // Sidebar is being expanded
            sidebarToggleBtn.classList.remove('show-when-collapsed');
            sidebarToggleBtn.classList.add('show-when-expanded');
            toggleIcon.classList.remove('rotated');
            sidebarToggleBtn.style.left = '260px';
        } else {
            // Sidebar is being collapsed
            sidebarToggleBtn.classList.remove('show-when-expanded');
            sidebarToggleBtn.classList.add('show-when-collapsed');
            toggleIcon.classList.add('rotated');
            sidebarToggleBtn.style.left = '20px';
        }
        
        // Store state in localStorage
        const isCollapsed = sidebar.classList.contains('collapsed');
        localStorage.setItem('sidebarCollapsed', isCollapsed);
    }
    
    // Add click event listener to toggle button
    if (sidebarToggleBtn) {
        sidebarToggleBtn.addEventListener('click', toggleSidebar);
    }
    
    // Check for saved state on page load
    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    if (isCollapsed) {
        sidebar.classList.add('collapsed');
        if (mainContent) {
            mainContent.classList.add('sidebar-collapsed');
        }
        sidebarToggleBtn.classList.add('show-when-collapsed');
        toggleIcon.classList.add('rotated');
        sidebarToggleBtn.style.left = '20px';
    } else {
        sidebarToggleBtn.classList.add('show-when-expanded');
        sidebarToggleBtn.style.left = '260px';
    }
    
    // Settings dropdown functionality (if exists)
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
    
    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth <= 768) {
            // On mobile, always position toggle at left when collapsed
            if (sidebar.classList.contains('collapsed')) {
                sidebarToggleBtn.style.left = '20px';
            }
        } else {
            // On desktop, position based on sidebar state
            if (sidebar.classList.contains('collapsed')) {
                sidebarToggleBtn.style.left = '20px';
            } else {
                sidebarToggleBtn.style.left = '260px';
            }
        }
    });
});

</script>

</body>
</html>