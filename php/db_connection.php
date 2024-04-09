<?php
// Database configuration
$servername = "localhost"; // Database hostname
$username = "root"; // Database username
$password = "root"; // Database password
$database = "my_child_first_memory"; // Database name

// Create connection to the database server
$conn = mysqli_connect($servername, $username, $password, $database);

// Check connection
if (!$conn) {
    // If connection fails, terminate the script and display an error message
    die("Connection failed: " . mysqli_connect_error());
}
