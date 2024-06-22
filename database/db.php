<?php
require_once ('config.php');

$host = "localhost";
$db_name = "ride_nepal";
$username = "root";
$password = "";

// Database Connection
$conn = new mysqli($host, $username, $password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}