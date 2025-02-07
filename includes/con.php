<?php
$servername = "localhost";
$username = "root";
$password = "";
$port = 3306;  //check if your working port number for mysql is the same. 
$dbname = "hospital_management";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}else{
    
}
?>

