<?php
session_start();
include '../config/db.php'; // Ensure you have DB connection setup

// CSRF Token Validation
if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error_message'] = "CSRF token validation failed.";
    header('Location: ../public/register.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize inputs
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $name = htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8');
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];

    // Check for empty fields
    if (empty($email) || empty($name) || empty($password) || empty($confirm_password) || empty($role)) {
        $_SESSION['error_message'] = "Please fill in all fields.";
        header('Location: ../public/register.php');
        exit();
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_message'] = "Invalid email format.";
        header('Location: ../public/register.php');
        exit();
    }

    // Validate password match
    if ($password !== $confirm_password) {
        $_SESSION['error_message'] = "Passwords do not match.";
        header('Location: ../public/register.php');
        exit();
    }

    // Hash the password securely
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if email already exists
    $query = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $query->bind_param('s', $email);
    $query->execute();
    $result = $query->get_result();
    if ($result->num_rows > 0) {
        $_SESSION['error_message'] = "Email is already taken.";
        header('Location: ../public/register.php');
        exit();
    }

    // Insert the new user into the database
    $query = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $query->bind_param('ssss',  $name, $email, $hashed_password, $role);
    $query->execute();

    // Check if the user was successfully inserted
    if ($query->affected_rows > 0) {
        $_SESSION['success_message'] = "Registration successful! You can now login.";
        $query -> close();
        header('Location: ../public/login.php');
        exit();
    } else {
        $_SESSION['error_message'] = "Something went wrong. Please try again.";
        $query -> close();
        header('Location: ../public/register.php');
        exit();
    }
}