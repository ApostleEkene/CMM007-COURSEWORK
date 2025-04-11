<?php
session_start();
include '../config/db.php';

// CSRF Token Validation
if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error_message'] = "CSRF token validation failed.";
    header('Location: ../public/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize input to prevent XSS
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password']; // Password doesn't need sanitization, but should be securely hashed and compared
    $role = $_POST['role'];

    // Check for empty fields
    if (empty($email) || empty($password) || empty($role)) {
        $_SESSION['error_message'] = "Please fill in all fields.";
        header('Location: ../public/login.php');
        exit();
    }

    // Prepare and execute the SQL query securely
    $query = $conn->prepare("SELECT * FROM users WHERE email = ? AND role = ?");
    $query->bind_param('ss', $email, $role);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Regenerate session ID to prevent session fixation
            session_regenerate_id(true);
            
            // Store user information in the session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];

            // Redirect based on role
            if ($role === 'admin') {
                header('Location: ../admin/dashboard.php');
            } elseif ($role === 'user') {
                header('Location: ../user/dashboard.php');
            } else {
                $_SESSION['error_message'] = "Invalid credentials.";
                header('Location: ../public/login.php');
            }
            exit();
        } else {
            // Invalid password
            $_SESSION['error_message'] = "Invalid email or password.";
            header('Location: ../public/login.php');
            exit();
        }
    } else {
        // User not found
        $_SESSION['error_message'] = "User not found.";
        header('Location: ../public/login.php');
        exit();
    }
}
?>
