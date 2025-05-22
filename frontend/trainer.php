<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trainer - Muscle City Gym</title>
    <link rel="stylesheet" href="../public/css/styles.css">
    <link rel="stylesheet" href="../public/css/trainer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<?php include '../partials/header.php'; ?> 
<?php include '../partials/navbar.php'; ?>  

<!-- Trainer Section -->
<section id="trainer">
    <div class="container">
        <h2>Meet Our Trainers</h2>
        <p>Our certified trainers are dedicated to helping you achieve your fitness goals.</p>

        <!-- Trainer Card with Image on Left and Info on Right -->
        <div class="trainer-card">
            <!-- Trainer Image (Circle) -->
            <div class="trainer-image" onclick="openFullscreenImage()">
                <img src="../public/images/trainer.png" alt="Joseph Alcantara" id="trainerPhoto">
            </div>

            <!-- Trainer Details on the Right -->
            <div class="trainer-info">
                <h3>Joseph Alcantara</h3>
                <p><strong>Senior Trainer</strong></p>
                <hr>

                <p><strong>WHAT I OFFER:</strong></p>
                <ul>
                    <li>Weight Loss</li>
                    <li>Fat Loss</li>
                    <li>Muscle Building</li>
                    <li>Strength and Conditioning</li>
                </ul>

                <p><strong>LET'S START YOUR FITNESS JOURNEY!</strong></p>
                <p><strong>Programs:</strong> 8, 12, 16, 18 Weeks</p>

                <!-- Social Media Links -->
                <div class="social-links">
                    <a href="https://www.instagram.com/josephalcantara_?igsh=YzljYTk1ODg3Zg==" target="_blank">
                        <i class="fab fa-instagram"></i> @JOSEPHALCANTARA
                    </a>
                    <a href="https://www.facebook.com/JosephAlcantara.19" target="_blank">
                        <i class="fab fa-facebook-square"></i> JOSEPH ALCANTARA
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Fullscreen Image Viewer (Hidden by Default) -->
<div id="fullscreenOverlay">
    <img id="fullscreenImage" src="../public/images/trainer.png" alt="Trainer Fullscreen">
    <span id="closeFullscreen">&times;</span>
</div>

<?php include '../partials/footer.php'; ?>

<script>
    // Open Fullscreen Image on Click
    function openFullscreenImage() {
        const fullscreenOverlay = document.getElementById('fullscreenOverlay');
        fullscreenOverlay.style.display = 'flex';
    }

    // Close Fullscreen Image Viewer
    const fullscreenOverlay = document.getElementById('fullscreenOverlay');
    fullscreenOverlay.addEventListener('click', function(event) {
        if (event.target === fullscreenOverlay) {
            fullscreenOverlay.style.display = 'none';
        }
    });

    document.getElementById('closeFullscreen').addEventListener('click', function () {
    document.getElementById('fullscreenOverlay').style.display = 'none';
});

</script>
<script src="../public/js/index.js"></script>
<script src="../public/js/search.js"></script>
</body>
</html>
