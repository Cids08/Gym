<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Package - Muscle City Gym</title>
    <link rel="stylesheet" href="../public/css/styles.css">
    <link rel="stylesheet" href="../public/css/package.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<?php include '../partials/header.php'; ?> 
<?php include '../partials/navbar.php'; ?>  

<!-- Package Section -->
<section id="package" class="info-section">
    <div class="container">
        <h2>Our Packages</h2>
        <p>We offer simple and flexible membership packages designed to match your routine and commitment level.</p>
        
        <div class="package-container">
    <!-- Basic Membership -->
    <div class="package-box">
        <h3>Basic Membership</h3>
        <p>₱850/month</p>
        <ul>
            <li>Gym access (Weekdays, 6 AM – 12 PM)</li>
            <li>Access to basic video tutorials</li>
            <li>Free gym towel (one-time gift)</li>
        </ul>
        <a href="#" class="btn-join" data-package="Basic Membership">Join Today</a>
    </div>
    
    <!-- Standard Membership -->
    <div class="package-box">
        <h3>Standard Membership</h3>
        <p>₱2400/3 months</p>
        <ul>
            <li>Gym access (Mon–Sat, full hours)</li>
            <li>Access to full video tutorial library</li>
            <li>Free gym shirt</li>
        </ul>
        <a href="#" class="btn-join" data-package="Standard Membership">Join Today</a>
    </div>
    
    <!-- Premium Membership -->
    <div class="package-box">
        <h3>Premium Membership</h3>
        <p>₱6500/6 months</p>
        <ul>
            <li>Unlimited gym access (7 days a week)</li>
            <li>Access to all exclusive video content</li>
            <li>Free gym kit (shirt, towel, water bottle)</li>
        </ul>
        <a href="#" class="btn-join" data-package="Premium Membership">Join Today</a>
    </div>
</div>
    </div>
</section>

<!-- Login Modal -->
<div id="loginModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <div class="form-container">
            <div id="loginForm" class="form-section active">
                <h2>Login</h2>
                <form id="loginFormElement" method="POST">
                    <input type="text" name="username" placeholder="Username" required><br>
                    <input type="password" name="password" placeholder="Password" required><br>
                    <button type="submit" class="btn-join">Login</button>
                </form>
                <p><a href="#" id="forgotPasswordLink">Forgot Password?</a></p>
                <p>Don't have an account? <a href="#" id="switchToSignup">Sign up</a></p>
            </div>

            <div id="signupForm" class="form-section inactive">
                <h2>Sign Up</h2>
                <form id="signupFormElement" method="POST">
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
                    <input type="text" name="phone" placeholder="Phone Number (optional)"><br>
                    <button type="submit" class="btn-join">Sign Up</button>
                </form>
                <p>Already have an account? <a href="#" id="switchToLogin">Login</a></p>
            </div>
        </div>
    </div>
</div>

<!-- Package Signup Modal -->
<div id="joinModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <div class="form-container">
            <h2>Complete Your Membership</h2>
            <form id="joinForm" action="join.php" method="POST">
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <div class="form-notice">
                        <p>You need to <a href="#" class="open-login-from-join">login</a> or complete your information below:</p>
                    </div>
                    
                    <input type="text" name="first_name" id="firstName" placeholder="First Name" required><br>
                    <input type="text" name="last_name" id="lastName" placeholder="Last Name" required><br>
                    <input type="email" name="email" id="email" placeholder="Email" required><br>
                    <input type="number" name="age" id="age" placeholder="Age" required><br>
                    
                    <div class="gender-selection">
                        <input type="radio" id="male" name="gender" value="Male" required>
                        <label for="male" class="gender-box">Male</label>
                        <input type="radio" id="female" name="gender" value="Female">
                        <label for="female" class="gender-box">Female</label>
                        <input type="radio" id="other" name="gender" value="Other">
                        <label for="other" class="gender-box">Other</label>
                    </div>
                    <br>
                <?php endif; ?>
                
                <!-- Selected Package -->
                <input type="hidden" name="package" id="selectedPackage">
                <div class="package-display">
                    <h3>Selected Package: <span id="packageDisplay"></span></h3>
                </div>

                <h3>Payment Method</h3>
                <div class="payment-methods">
                    <input type="radio" id="gcash" name="payment_method" value="GCash" required>
                    <label for="gcash" class="payment-box">GCash</label>
                    
                    <input type="radio" id="paymaya" name="payment_method" value="PayMaya">
                    <label for="paymaya" class="payment-box">PayMaya</label>
                </div><br>

                <div id="paymentFields" class="payment-fields">
                    <input type="text" name="phone_number" id="phoneNumber" placeholder="Enter your phone number" required><br>
                    <button type="button" id="generatePinButton" class="btn-join">Generate Pin Code</button><br>
                    <input type="text" name="pin_code" id="pinCode" placeholder="Pin Code" readonly><br>
                </div>

                <button type="button" id="submitButton" class="btn-join">Continue</button>
            </form>
        </div>
    </div>
</div>

<!-- Confirmation Section -->
<div id="confirmationSection" class="confirmation-section">
    <h2>Confirm Your Membership</h2>
    <div id="confirmationDetails"></div>
    <button id="editButton" class="btn-join">Edit</button>
    <button id="confirmButton" class="btn-join">Confirm and Pay</button>
</div>

<script>
    // DOM Elements
    const loginModal = document.getElementById('loginModal');
    const joinModal = document.getElementById('joinModal');
    const confirmationSection = document.getElementById('confirmationSection');
    const openJoinButtons = document.querySelectorAll('.package-box .btn-join');
    const openLoginButtons = document.querySelectorAll('.open-login, .open-login-from-join');
    const closeButtons = document.querySelectorAll('.close');
    const switchToSignup = document.getElementById('switchToSignup');
    const switchToLogin = document.getElementById('switchToLogin');
    const loginFormSection = document.getElementById('loginForm');
    const signupFormSection = document.getElementById('signupForm');
    const loginForm = document.getElementById('loginFormElement');
    const signupForm = document.getElementById('signupFormElement');
    const joinForm = document.getElementById('joinForm');
    const submitButton = document.getElementById('submitButton');
    const editButton = document.getElementById('editButton');
    const confirmButton = document.getElementById('confirmButton');
    const generatePinButton = document.getElementById('generatePinButton');
    const packageDisplay = document.getElementById('packageDisplay');
    const selectedPackageInput = document.getElementById('selectedPackage');
    const paymentFields = document.getElementById('paymentFields');

    // Initialize payment fields as hidden by default
    paymentFields.style.display = 'none';

    // Open modals
    openJoinButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            const package = button.getAttribute('data-package');
            selectedPackageInput.value = package;
            packageDisplay.textContent = package;
            
            if (<?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>) {
                // User is logged in, open join form directly
                joinModal.style.display = 'block';
            } else {
                // User not logged in, show login modal first
                loginModal.style.display = 'block';
            }
        });
    });

    openLoginButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            loginModal.style.display = 'block';
            if (joinModal.style.display === 'block') {
                joinModal.style.display = 'none';
            }
        });
    });

    // Close modals
    closeButtons.forEach(button => {
        button.addEventListener('click', () => {
            loginModal.style.display = 'none';
            joinModal.style.display = 'none';
        });
    });

    window.addEventListener('click', (e) => {
        if (e.target === loginModal) loginModal.style.display = 'none';
        if (e.target === joinModal) joinModal.style.display = 'none';
    });

    // Switch between login and signup forms
    switchToSignup.addEventListener('click', (e) => {
        e.preventDefault();
        loginFormSection.classList.remove('active');
        loginFormSection.classList.add('inactive');
        signupFormSection.classList.remove('inactive');
        signupFormSection.classList.add('active');
    });

    switchToLogin.addEventListener('click', (e) => {
        e.preventDefault();
        signupFormSection.classList.remove('active');
        signupFormSection.classList.add('inactive');
        loginFormSection.classList.remove('inactive');
        loginFormSection.classList.add('active');
    });

    // Handle Payment Method Selection
    document.querySelectorAll('input[name="payment_method"]').forEach(input => {
        input.addEventListener('change', function() {
            // Hide payment fields by default
            paymentFields.style.display = 'none';
            
            // Show payment fields only if GCash or PayMaya is selected
            if (this.value === 'GCash' || this.value === 'PayMaya') {
                paymentFields.style.display = 'block';
            }
        });
    });

    // Generate random PIN code
    generatePinButton.addEventListener('click', () => {
        const pinCode = Math.floor(100000 + Math.random() * 900000);
        document.getElementById('pinCode').value = pinCode;
    });

    // Handle form submission for confirmation
    submitButton.addEventListener('click', () => {
        // Validate form
        if (!joinForm.checkValidity()) {
            joinForm.reportValidity();
            return;
        }

        // Check if payment method is selected
        const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
        if (!paymentMethod) {
            showNotification('Please select a payment method', 'error');
            return;
        }

        // For GCash/PayMaya, validate phone and PIN
        if (paymentMethod.value === 'GCash' || paymentMethod.value === 'PayMaya') {
            const phoneNumber = document.getElementById('phoneNumber').value;
            const pinCode = document.getElementById('pinCode').value;
            
            if (!phoneNumber || !pinCode) {
                showNotification('Please enter phone number and generate PIN code', 'error');
                return;
            }
        }

        // Gather form data
        const formData = new FormData(joinForm);
        const confirmationDetails = document.getElementById('confirmationDetails');
        
        // Build confirmation HTML
        let html = '';
        for (const [key, value] of formData.entries()) {
            if (key === 'package') {
                html += `<p><strong>Package:</strong> ${value}</p>`;
            } else if (key === 'payment_method') {
                html += `<p><strong>Payment Method:</strong> ${value}</p>`;
            } else if (key === 'phone_number') {
                html += `<p><strong>Phone Number:</strong> ${value}</p>`;
            } else if (key === 'pin_code') {
                html += `<p><strong>PIN Code:</strong> ${value}</p>`;
            } else if (!key.startsWith('_')) {
                const label = key.replace('_', ' ');
                html += `<p><strong>${label.charAt(0).toUpperCase() + label.slice(1)}:</strong> ${value}</p>`;
            }
        }

        confirmationDetails.innerHTML = html;
        joinModal.style.display = 'none';
        confirmationSection.style.display = 'block';
    });

    // Edit button returns to form
    editButton.addEventListener('click', () => {
        confirmationSection.style.display = 'none';
        joinModal.style.display = 'block';
    });

    // Confirm button submits the form
    confirmButton.addEventListener('click', () => {
        joinForm.submit();
    });

    // AJAX form submissions for login/signup
    loginForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(loginForm);
        
        try {
            const response = await fetch('login.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                showNotification('Login successful!', 'success');
                setTimeout(() => {
                    loginModal.style.display = 'none';
                    window.location.reload();
                }, 1500);
            } else {
                showNotification(result.message || 'Login failed', 'error');
            }
        } catch (error) {
            showNotification('An error occurred', 'error');
        }
    });

    signupForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(signupForm);
        
        try {
            const response = await fetch('signup.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                showNotification('Signup successful!', 'success');
                setTimeout(() => {
                    loginModal.style.display = 'none';
                    window.location.reload();
                }, 1500);
            } else {
                showNotification(result.message || 'Signup failed', 'error');
            }
        } catch (error) {
            showNotification('An error occurred', 'error');
        }
    });

    // Notification function
    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => notification.remove(), 500);
        }, 3000);
    }
</script>
<script src="../public/js/index.js"></script>
<script src="../public/js/search.js"></script>

<?php include '../partials/footer.php'; ?>

</body>
</html>
