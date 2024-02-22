<?php
session_start();

function checkAuth() {
    if(!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit();
    }
}


function getUserRequests($conn, $username) {
    $sql = "SELECT * FROM requests WHERE username='$username'";
    $result = $conn->query($sql);
    $requests = array();
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $requests[] = $row;
        }
    }
    return $requests;
}
?>
