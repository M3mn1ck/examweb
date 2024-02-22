<?php

include_once '../configs/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
  
    $sql = "INSERT INTO users (username, password) VALUES ('$username', '$hashed_password')";
    if ($conn->query($sql) === TRUE) {

        header("Location: ../login.php");
        exit();
    } else {

        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}


$conn->close();
?>
