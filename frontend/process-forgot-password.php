<?php
session_start();

// Database connection
$db_host = "localhost";
$db_user = "root"; 
$db_pass = ""; 
$db_name = "muscle_city_gym";


// Create connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    $_SESSION['reset_error'] = "Connection failed: " . $conn->connect_error;
    header("Location: forgot-password.php");
    exit();
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get email and sanitize
    $email = $conn->real_escape_string($_POST['email']);
    
    // Validate email
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['reset_error'] = "Please enter a valid email address";
        header("Location: forgot-password.php");
        exit();
    }
    
    // Check if email exists in database
    $sql = "SELECT id, username, email FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Generate a unique token
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token expires in 1 hour
        
        // Store token in database
        $update_sql = "UPDATE users SET reset_token = ?, reset_token_expires = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ssi", $token, $expires, $user['id']);
        
        if ($update_stmt->execute()) {
            // Send email with reset link (using PHP mail function)
            $reset_link = "http://{$_SERVER['HTTP_HOST']}/path/to/reset-password.php?token=$token";
            $to = $user['email'];
            $subject = "Password Reset - Muscle City Fitness Gym";
            
            $message = "
            <html>
            <head>
                <title>Password Reset</title>
            </head>
            <body>
                <p>Hello {$user['username']},</p>
                <p>We received a request to reset your password for your Muscle City Fitness Gym account.</p>
                <p>Please click on the link below to reset your password:</p>
                <p><a href='$reset_link'>Reset Password</a></p>
                <p>This link will expire in 1 hour.</p>
                <p>If you did not request this, please ignore this email and your password will remain unchanged.</p>
                <p>Regards,<br>Muscle City Fitness Gym Team</p>
            </body>
            </html>
            ";
            
            // Always set content-type when sending HTML email
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: noreply@musclecityfitness.com" . "\r\n";
            
            // For production, you should use a proper email sending library or service
            $mail_sent = mail($to, $subject, $message, $headers);
            
            if ($mail_sent) {
                $_SESSION['reset_success'] = "Password reset instructions have been sent to your email";
            } else {
                // Email failed to send (but don't reveal this to the user for security)
                $_SESSION['reset_success'] = "If the email exists in our system, password reset instructions will be sent";
            }
        } else {
            // Don't reveal database errors to the user
            $_SESSION['reset_success'] = "If the email exists in our system, password reset instructions will be sent";
        }
        
        $update_stmt->close();
    } else {
        // For security reasons, don't reveal if the email exists or not
        $_SESSION['reset_success'] = "If the email exists in our system, password reset instructions will be sent";
    }
    
    $stmt->close();
    
    header("Location: forgot-password.php");
    exit();
}

// Close connection
$conn->close();
?>