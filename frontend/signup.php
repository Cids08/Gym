<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Muscle City Fitness Gym</title>
    <link rel="stylesheet" href="../public/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<?php include '../partials/header.php'; ?>
<?php include '../partials/navbar.php'; ?>

<div class="login-container">
    <div class="login-content">
        <h1>Sign Up</h1>
        <?php
            // Display error messages if they exist
            if (isset($_SESSION['signup_error'])) {
                echo '<div class="error-message">' . $_SESSION['signup_error'] . '</div>';
                unset($_SESSION['signup_error']);
            }

            // Display array of error messages
            if (isset($_SESSION['signup_errors']) && is_array($_SESSION['signup_errors'])) {
                echo '<div class="error-message">';
                foreach ($_SESSION['signup_errors'] as $error) {
                    echo $error . '<br>';
                }
                echo '</div>';
                unset($_SESSION['signup_errors']);
            }

            // Retain form data on error
            $form_data = isset($_SESSION['signup_data']) ? $_SESSION['signup_data'] : [
                'first_name' => '', 
                'last_name' => '', 
                'username' => '', 
                'email' => '', 
                'age' => '', 
                'gender' => '', 
                'phone' => ''
            ];
            // Clear the session data after using it
            if (isset($_SESSION['signup_data'])) {
                unset($_SESSION['signup_data']);
            }
        ?>
        <div id="signupForm" class="form-section active">
            <form action="process-signup.php" method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <input type="text" name="first_name" placeholder="First Name" required>
                    </div>
                    <div class="form-group">
                        <input type="text" name="last_name" placeholder="Last Name" required>
                    </div>
                </div>
                <input type="text" name="username" placeholder="Username" required><br>
                <input type="password" name="password" placeholder="Password" required><br>
                <input type="email" name="email" placeholder="Email" required><br>
                <div class="form-row">
                    <div class="form-group">
                        <input type="number" name="age" placeholder="Age" min="13" max="120" required>
                    </div>
                    <div class="form-group">
                        <select name="gender" required>
                            <option value="" disabled selected>Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                            <option value="prefer_not_to_say">Prefer not to say</option>
                        </select>
                    </div>
                </div>
                <input type="text" name="phone" placeholder="Phone Number"><br>
                <button type="submit" class="login-btn">Sign Up</button>
            </form>
            <p>Already have an account? <a href="login.php" class="yellow-link">Login</a></p>
        </div>
    </div>
</div>

<?php include '../partials/footer.php'; ?>

<script src="../public/js/index.js"></script>
</body>
</html>