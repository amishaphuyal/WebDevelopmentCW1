<?php
$servername = "localhost";
$username = "root";
$password = "";
$port = 3307;
$dbname = "hospital_management";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}else{
    
}
?>

