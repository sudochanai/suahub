<?php
session_start();

// Connect to the database
include 'db.php';

// Get form data
$email = $_POST['useremail'];
$password = $_POST['userpass'];

// Verify user
$query = "SELECT * FROM users WHERE useremail = '$email'";
$result = pg_query($conn, $query);
$user = pg_fetch_assoc($result);

if ($user && password_verify($password, $user['userpass'])) {
    $_SESSION['email'] = $user['useremail'];
    $_SESSION['role'] = $user['usertype'];
    $_SESSION['userid'] = $user['userid'];
    $_SESSION['username'] = $user['userfname'];
    header("Location: dashboard.php");
} else {
    echo "Invalid username or password.";
}

pg_close($conn);
?>
