<nav class="main-navbar">
    <ul class="nav-links">
        <li><a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">Home</a></li>
        <li><a href="about-us.php" class="<?= basename($_SERVER['PHP_SELF']) == 'about-us.php' ? 'active' : '' ?>">About Us</a></li>
        <li><a href="program.php" class="<?= basename($_SERVER['PHP_SELF']) == 'program.php' ? 'active' : '' ?>">Program</a></li>
        <li><a href="trainer.php" class="<?= basename($_SERVER['PHP_SELF']) == 'trainer.php' ? 'active' : '' ?>">Trainer</a></li>
        <li><a href="package.php" class="<?= basename($_SERVER['PHP_SELF']) == 'package.php' ? 'active' : '' ?>">Package</a></li>

        <!-- Show Login / Sign Up only if NOT on login/register pages -->
        <?php 
        $currentPage = basename($_SERVER['PHP_SELF']);
        $authPages = ['login.php', 'signup.php'];
        if (!in_array($currentPage, $authPages)): ?>
            <li><a href="../frontend/login.php" class="auth-btn">Login/SignUp</a></li>
        <?php endif; ?>
    </ul>

    <div class="search-container">
        <input type="text" placeholder="Search..." class="search-input">
        <button type="submit" class="search-button">
            <span class="search-icon">&#128269;</span>
        </button>
    </div>
</nav>