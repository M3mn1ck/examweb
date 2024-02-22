<?php
$servername = "localhost"; 
$username = "a0915167_23"; 
$password = "cz6XIekx"; 
$dbname = "a0915167_23"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
