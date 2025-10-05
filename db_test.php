<?php
$servername = "127.0.0.1";
$username = "motorsss";
$password = "3uVTKeramGAUcolpi07z";
$dbname = "motorsss";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully to the database!";
$conn->close();
?>