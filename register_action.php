<?php
session_start();
include('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstname = $_POST['userfname'];
    $surname = $_POST['usersurname'];
    $gender = $_POST['gender'];
    $email = $_POST['useremail'];
    $password = $_POST['userPass'];
    $confirmPassword = $_POST['confirmPass'];

    // Basic validation
    if (empty($firstname) || empty($surname) || empty($gender) || empty($email) || empty($password) || empty($confirmPassword)) {
        $_SESSION['error_message'] = 'All fields are required.';
        header('Location: register.php');
        exit();
    }

    if ($password !== $confirmPassword) {
        $_SESSION['error_message'] = 'Passwords do not match.';
        header('Location: register.php');
        exit();
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert into database
    $query = "INSERT INTO users (userfname, usersurname, gender, useremail, userPass) VALUES ($1, $2, $3, $4, $5)";
    $result = pg_query_params($conn, $query, array($firstname, $surname, $gender, $email, $hashedPassword));

    if ($result) {
        header('Location: login.php');
        exit();
    } else {
        $_SESSION['error_message'] = 'Error: ' . pg_last_error($conn);
        header('Location: register.php');
        exit();
    }
}
?>
