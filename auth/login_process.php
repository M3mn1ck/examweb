<?php
if ($_SERVER["REQUEST_METHOD"] != "POST") {

    header("Location: login.php");
    exit();
}


include_once '../configs/config.php';

$username = $_POST['username'];
$password = $_POST['password'];


$sql = "SELECT * FROM users WHERE username='$username'";
$result = $conn->query($sql);

if ($result->num_rows == 1) {

    $row = $result->fetch_assoc();
    if (password_verify($password, $row['password'])) {

        session_start();
        $_SESSION['username'] = $username;
        header("Location: ../profile/profile.php");
        exit();
    } else {

        header("Location: ../login.php?error=wrong_password");
        exit();
    }
} else {
   
    header("Location: ../login.php?error=user_not_found");
    exit();
}


?>