<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gym Website</title>
    <link rel="stylesheet" href="../public/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
   
</head>
<body>

<?php include '../partials/header.php'; ?>
<?php include '../partials/navbar.php'; ?>

<section id="home">
    <div class="content">
        <?php if (!isset($_SESSION['user_id'])): ?>
            <button class="login-btn open-login" id="joinNowBtn">Join Now</button>
        <?php endif; ?>
    </div>
</section>

<section id="announcement-section">
    <div class="announcement-box">
        <div class="announcement-content">
            <h2>Big News from Muscle City!</h2>
            <p>Get ready for our exclusive summer event! Enjoy free classes, competitions, and a chance to win premium gym merch. Don't miss out!</p>
            <a href="about-us.php" class="cta-button">Learn More</a>
        </div>
        <img src="../public/images/Announcement.png" alt="Announcement" class="announcement-image">
    </div>
</section>

<section id="muscle-city-section">
    <div class="muscle-city-intro-box">
        <h2>Muscle City Gym – Your Neighborhood Fitness Hub in the Philippines!
        </h2>
        <p>At Muscle City Gym, we're committed to helping you reach your fitness goals with modern equipment, experienced local trainers, and a supportive community atmosphere. Whether you're just starting out or looking to level up your workouts, we're here to guide you every step of the way. Proudly serving our local community, we make fitness accessible, effective, and enjoyable. Come train with us and experience the difference!</p>
    </div>
</section>

<div id="imageModal" class="modal">
    <span class="close" id="closeModal">&times;</span>
    <img class="modal-content" id="fullImage">
    <div id="caption"></div>
</div>

<?php include '../partials/footer.php'; ?>

<script src="../public/js/index.js"></script>
<script src="../public/js/search.js"></script>
</body>
</html>