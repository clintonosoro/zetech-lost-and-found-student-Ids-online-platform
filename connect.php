<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'lostfound_zetech';

// Create a new MySQLi connection
$conn = new mysqli($host, $user, $password, $database);

// Check for connection errors
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}
?>
