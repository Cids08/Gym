<?php
session_start();

// Debugging - show session data
echo "<!-- SESSION: ";
print_r($_SESSION);
echo " -->";

// Check login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../frontend/login.php");
    exit();
}

// Connect to database
$conn = new mysqli("localhost", "root", "", "muscle_city_gym");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

// Debugging - show user ID
echo "<!-- User ID: $user_id -->";

// Check if user is already subscribed
$sql_check = "SELECT * FROM memberships WHERE user_id = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("i", $user_id);
$stmt_check->execute();
$stmt_check->store_result();

$is_subscribed = $stmt_check->num_rows > 0;

// Debugging - show how many rows found
echo "<!-- Rows found: " . $stmt_check->num_rows . " -->";

$stmt_check->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Package Selection - Muscle City Gym</title>
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
            text-align: center;
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

        .dashboard-header h1 {
            font-size: 3.5em;
            font-weight: 900;
            margin-bottom: 15px;
            text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.4);
            position: relative;
            z-index: 2;
        }

        .dashboard-header p {
            font-size: 1.3em;
            opacity: 0.95;
            position: relative;
            z-index: 2;
            max-width: 600px;
            margin: 0 auto;
        }

        .packages-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .package-card {
            background: linear-gradient(135deg, #1a1a1a, #2d2d2d, #1f1f1f);
            border-radius: 25px;
            padding: 40px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.6);
            border: 1px solid #333333;
            backdrop-filter: blur(10px);
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
            text-align: center;
        }

        .package-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #FF6B35, #F7931E, #FF8C42);
            border-radius: 25px 25px 0 0;
        }

        .package-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 25px 60px rgba(255, 107, 53, 0.25);
            border-color: #FF6B35;
        }





        .package-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 25px;
            background: linear-gradient(135deg, #FF6B35, #F7931E);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2em;
            color: white;
            box-shadow: 0 10px 25px rgba(255, 107, 53, 0.3);
            transition: all 0.4s ease;
        }

        .package-card:hover .package-icon {
            transform: scale(1.1) rotate(10deg);
            box-shadow: 0 15px 35px rgba(255, 107, 53, 0.5);
        }

        .package-title {
            font-size: 2.2em;
            font-weight: 800;
            color: #FF6B35;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .package-price {
            font-size: 2.8em;
            font-weight: 900;
            color: #ffffff;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .package-duration {
            font-size: 1.1em;
            color: #cccccc;
            margin-bottom: 35px;
            opacity: 0.8;
        }

        .package-features {
            list-style: none;
            margin-bottom: 40px;
            text-align: left;
        }

        .package-features li {
            padding: 12px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 1.05em;
            transition: all 0.3s ease;
        }

        .package-features li:hover {
            color: #FF6B35;
            transform: translateX(5px);
        }

        .package-features li:last-child {
            border-bottom: none;
        }

        .package-features i {
            color: #27ae60;
            font-size: 1.2em;
            width: 20px;
            text-align: center;
        }

        .package-btn {
            width: 100%;
            padding: 18px 30px;
            background: linear-gradient(135deg, #FF6B35, #F7931E);
            color: white;
            border: none;
            border-radius: 15px;
            font-size: 1.2em;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(255, 107, 53, 0.3);
        }

        .package-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.6s ease;
        }

        .package-btn:hover::before {
            left: 100%;
        }

        .package-btn:hover {
            background: linear-gradient(135deg, #F7931E, #FF6B35);
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(255, 107, 53, 0.5);
        }

        .package-btn:active {
            transform: translateY(-1px);
        }

        .comparison-section {
            background: linear-gradient(135deg, #1a1a1a, #2d2d2d, #1f1f1f);
            border-radius: 25px;
            padding: 50px;
            margin-top: 50px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.6);
            border: 1px solid #333333;
            position: relative;
            overflow: hidden;
        }

        .comparison-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #FF6B35, #F7931E, #FF8C42);
        }

        .comparison-title {
            text-align: center;
            font-size: 2.5em;
            font-weight: 800;
            color: #FF6B35;
            margin-bottom: 40px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .comparison-table {
            width: 100%;
            border-collapse: collapse;
            background: rgba(255, 255, 255, 0.02);
            border-radius: 15px;
            overflow: hidden;
        }

        .comparison-table th,
        .comparison-table td {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .comparison-table th {
            background: linear-gradient(135deg, #FF6B35, #F7931E);
            color: white;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .comparison-table tr:hover {
            background: rgba(255, 107, 53, 0.1);
        }

        .check-icon {
            color: #27ae60;
            font-size: 1.3em;
        }

        .cross-icon {
            color: #e74c3c;
            font-size: 1.3em;
        }

        .back-btn {
            position: fixed;
            top: 30px;
            left: 30px;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #FF6B35, #F7931E);
            color: white;
            border: none;
            border-radius: 50%;
            font-size: 1.5em;
            cursor: pointer;
            transition: all 0.4s ease;
            box-shadow: 0 8px 20px rgba(255, 107, 53, 0.3);
            z-index: 1000;
        }

        .back-btn:hover {
            transform: scale(1.1) rotate(-10deg);
            box-shadow: 0 12px 30px rgba(255, 107, 53, 0.5);
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .packages-grid {
                grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .packages-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .dashboard-container {
                padding: 15px;
            }
            
            .dashboard-header {
                padding: 30px 20px;
            }
            
            .dashboard-header h1 {
                font-size: 2.5em;
            }
            
            .dashboard-header p {
                font-size: 1.1em;
            }
            
            .package-card {
                padding: 30px 25px;
            }
            
            .comparison-section {
                padding: 30px 20px;
            }
            
            .comparison-table {
                font-size: 0.85em;
            }
            
            .comparison-table th,
            .comparison-table td {
                padding: 12px 8px;
            }
            
            .back-btn {
                width: 50px;
                height: 50px;
                font-size: 1.2em;
                top: 20px;
                left: 20px;
            }
        }

        @media (max-width: 480px) {
            .dashboard-header h1 {
                font-size: 2em;
            }
            
            .package-price {
                font-size: 2.2em;
            }
            
            .package-title {
                font-size: 1.8em;
            }
            
            .comparison-table {
                font-size: 0.75em;
            }
            
            .comparison-table th,
            .comparison-table td {
                padding: 8px 4px;
            }
        }

        /* Animation for cards */
        .package-card {
            opacity: 0;
            transform: translateY(50px);
            animation: fadeInUp 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
        }

        .package-card:nth-child(1) { animation-delay: 0.2s; }
        .package-card:nth-child(2) { animation-delay: 0.4s; }
        .package-card:nth-child(3) { animation-delay: 0.6s; }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
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
</head>
<body>
    <button class="back-btn" onclick="goBack()">
        <i class="fas fa-arrow-left"></i>
    </button>
    <div class="dashboard-container">
        <!-- Header Section -->
        <div class="dashboard-header">
            <h1>Choose Your Plan</h1>
            <p>Select the perfect membership package that fits your fitness goals and lifestyle.</p>
        </div>

        <!-- Packages Grid -->
        <div class="packages-grid">

            <!-- Basic Membership -->
            <div class="package-card">
                <div class="package-icon"><i class="fas fa-dumbbell"></i></div>
                <h3 class="package-title">Basic</h3>
                <div class="package-price">₱850</div>
                <div class="package-duration">per month</div>
                <ul class="package-features">
                    <li><i class="fas fa-check"></i>Gym access (Weekdays, 6 AM – 12 PM)</li>
                    <li><i class="fas fa-check"></i>Access to basic video tutorials</li>
                    <li><i class="fas fa-check"></i>Free gym towel (one-time gift)</li>
                    <li><i class="fas fa-check"></i>Locker room access</li>
                    <li><i class="fas fa-check"></i>Basic equipment usage</li>
                </ul>
                <?php if ($is_subscribed): ?>
                    <button class="package-btn" disabled>Already Subscribed</button>
                <?php else: ?>
                    <button class="package-btn" onclick="selectPackage('Basic')">Get Started</button>
                <?php endif; ?>
            </div>

            <!-- Standard Membership -->
            <div class="package-card">
                <div class="package-icon"><i class="fas fa-fire"></i></div>
                <h3 class="package-title">Standard</h3>
                <div class="package-price">₱2400</div>
                <div class="package-duration">per 3 months</div>
                <ul class="package-features">
                    <li><i class="fas fa-check"></i>Gym access (Mon–Sat, full hours)</li>
                    <li><i class="fas fa-check"></i>Full video tutorial library</li>
                    <li><i class="fas fa-check"></i>Free gym shirt</li>
                    <li><i class="fas fa-check"></i>Group fitness classes</li>
                    <li><i class="fas fa-check"></i>Personal trainer consultation</li>
                </ul>
                <?php if ($is_subscribed): ?>
                    <button class="package-btn" disabled>Already Subscribed</button>
                <?php else: ?>
                    <button class="package-btn" onclick="selectPackage('Standard')">Choose Plan</button>
                <?php endif; ?>
            </div>

            <!-- Premium Membership -->
            <div class="package-card">
                <div class="package-icon"><i class="fas fa-crown"></i></div>
                <h3 class="package-title">Premium</h3>
                <div class="package-price">₱6500</div>
                <div class="package-duration">per 6 months</div>
                <ul class="package-features">
                    <li><i class="fas fa-check"></i>Unlimited gym access (7 days a week)</li>
                    <li><i class="fas fa-check"></i>All exclusive video content</li>
                    <li><i class="fas fa-check"></i>Free gym kit (shirt, towel, water bottle)</li>
                    <li><i class="fas fa-check"></i>Priority booking for classes</li>
                    <li><i class="fas fa-check"></i>Monthly body composition analysis</li>
                </ul>
                <?php if ($is_subscribed): ?>
                    <button class="package-btn" disabled>Already Subscribed</button>
                <?php else: ?>
                    <button class="package-btn" onclick="selectPackage('Premium')">Go Premium</button>
                <?php endif; ?>
            </div>

        </div>

        <!-- Comparison Section -->
        <div class="comparison-section">
            <h2 class="comparison-title">Compare Features</h2>
            <table class="comparison-table">
                <thead>
                    <tr>
                        <th>Features</th>
                        <th>Basic</th>
                        <th>Standard</th>
                        <th>Premium</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Gym Access Hours</strong></td>
                        <td>Weekdays 6AM-12PM</td>
                        <td>Mon-Sat Full Hours</td>
                        <td>24/7 Access</td>
                    </tr>
                    <tr>
                        <td><strong>Video Tutorials</strong></td>
                        <td><i class="fas fa-check check-icon"></i></td>
                        <td><i class="fas fa-check check-icon"></i></td>
                        <td><i class="fas fa-check check-icon"></i></td>
                    </tr>
                    <tr>
                        <td><strong>Group Classes</strong></td>
                        <td><i class="fas fa-times cross-icon"></i></td>
                        <td><i class="fas fa-check check-icon"></i></td>
                        <td><i class="fas fa-check check-icon"></i></td>
                    </tr>
                    <tr>
                        <td><strong>Personal Trainer</strong></td>
                        <td><i class="fas fa-times cross-icon"></i></td>
                        <td>1 Session</td>
                        <td>Monthly Sessions</td>
                    </tr>
                    <tr>
                        <td><strong>Priority Support</strong></td>
                        <td><i class="fas fa-times cross-icon"></i></td>
                        <td><i class="fas fa-times cross-icon"></i></td>
                        <td><i class="fas fa-check check-icon"></i></td>
                    </tr>
                    <tr>
                        <td><strong>Free Merchandise</strong></td>
                        <td>Towel</td>
                        <td>Shirt</td>
                        <td>Complete Kit</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function selectPackage(packageName) {
            window.location.href = "../frontend/join.php?package=" + packageName;
        }

        function goBack() {
            window.history.back();
        }
    </script>
</body>
</html>