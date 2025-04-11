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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System - Login</title>
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
            
            <!-- Error message display -->
            <?php if (isset($error_message)): ?>
                <div class="message" style="text-align:center;color:red;padding:5px 2px">
                    <p><?php echo $error_message; ?></p>
                </div>
            <?php endif; ?>

            <form id="loginForm" action="../controllers/AuthController.php" method="POST">
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
                    <input type="username" id="username" name="username" placeholder="username" required autofocus>
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

                <button type="submit" class="login-btn">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
                <p class="text-center">New User?</p><br>
                <a href ="../public/register.php" class="btn-link"><i class="fas fa-user-plus"></i> Register</a>
                
                

            </form>
        </div>
    </div>

   
</body>
</html>
