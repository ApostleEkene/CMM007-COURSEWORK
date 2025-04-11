<?php
// Start session and regenerate ID for session security
session_start();
session_regenerate_id(true);




// CSRF Token Generation (if not already set)
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Secure random token
}

// Handle error messages (Sanitize user input for XSS prevention)
if (isset($_SESSION['error_message'])) {
    $error_message = htmlspecialchars($_SESSION['error_message'], ENT_QUOTES, 'UTF-8');
    unset($_SESSION['error_message']);
}

// Handle success messages (Sanitize user input for XSS prevention)
if (isset($_SESSION['success_message'])) {
    $success_message = htmlspecialchars($_SESSION['success_message'], ENT_QUOTES, 'UTF-8');
    unset($_SESSION['success_message']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System - Register</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/common.css">
    <link rel="stylesheet" href="../assets/css/login.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <h1>Library Management System</h1>
            
            <!-- Success/Error message display -->
            <?php if (isset($error_message)): ?>
                <div class="message" style="text-align:center;color:red;padding:5px 2px">
                    <p><?php echo $error_message; ?></p>
                </div>
            <?php endif; ?>
            
            <?php if (isset($success_message)): ?>
                <div class="message" style="text-align:center;color:green;padding:5px 2px">
                    <p><?php echo $success_message; ?></p>
                </div>
            <?php endif; ?>

            <form id="registerForm" action="../controllers/RegisterController.php" method="POST" onsubmit="return validateForm()">
                <!-- CSRF Token -->
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                <div class="form-group">
                    <select id="roleSelect" name='role' required>
                        <option value="">Select Role</option>
                        <option value="admin">Admin</option>
                        <option value="user">User</option>
                    </select>
                </div>

                <div class="form-group">
                    <i class="fas fa-user"></i>
                    <label for="username" class="sr-only">Username</label>
                    <input type="text" id="username" name="username" placeholder="Username" required autofocus>
                </div>

                <div class="form-group">
                    <i class="fas fa-user"></i>
                    <label for="email" class="sr-only">Email</label>
                    <input type="email" id="email" name="email" placeholder="Email" required autofocus>
                </div>

                <div class="form-group">
                    <i class="fas fa-lock"></i>
                    <label for="password" class="sr-only">Password</label>
                    <input type="password" id="password" name="password" placeholder="Password" required>
                </div>

                <div class="form-group">
                    <i class="fas fa-lock"></i>
                    <label for="confirm_password" class="sr-only">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
                </div>

                <button type="submit" class="login-btn">
                    <i class="fas fa-user-plus"></i> Register
                </button>

                <p class="text-center">Already Registered?</p><br>
                <a href ="../public/login.php" class="btn-link"><i class="fas fa-user-plus"></i>Login</a>
            </form>
            
        </div>
    </div>

    <script>
        function validateForm() {
            // Get form elements
            const email = document.getElementById('email').value;
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const role = document.getElementById('roleSelect').value;

            // Basic validation for empty fields
            if (!role || !email || !username || !password || !confirmPassword) {
                showError('Please fill in all fields.');
                return false;
            }

            // Validate email format
            const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
            if (!emailRegex.test(email)) {
                showError('Please enter a valid email address.');
                return false;
            }

            // Validate that passwords match
            if (password !== confirmPassword) {
                showError('Passwords do not match.');
                return false;
            }

            // Optional: Add password strength validation here if needed (e.g., min length, special characters, etc.)
            
            return true; // Submit the form if all validations pass
        }

        function showError(message) {
            // Create error message element if it doesn't exist
            let errorDiv = document.querySelector('.error-message');
            if (!errorDiv) {
                errorDiv = document.createElement('div');
                errorDiv.className = 'error-message';
                document.querySelector('.login-card').insertBefore(
                    errorDiv,
                    document.querySelector('form')
                );
            }
            
            // Show error message
            errorDiv.textContent = message;
            
            // Use setTimeout to ensure the transition triggers
            setTimeout(() => {
                errorDiv.classList.add('show');
            }, 50)
            
            // Hide error message after 3 seconds
            setTimeout(() => {
                errorDiv.classList.remove('show');
                setTimeout(() => {
                    errorDiv.remove();
                }, 500);
            }, 3000);
        }
    </script>
</body>
</html>