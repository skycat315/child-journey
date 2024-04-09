<?php
// Start session
session_start();

// Check if the user is logged in
if (!isset($_SESSION["username"])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Initialize success message variable
$success_message = "";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Establish a database connection
    require_once "db_connection.php";

    // Sanitize input and retrieve data
    $username = $_SESSION["username"];
    $memory = mysqli_real_escape_string($conn, $_POST["memory"]); // Sanitize input by escaping special characters in the "memory" field to prevent SQL injection
    $memory_date = $_POST["memory_date"];

    // Check if memory field is empty
    if ($memory === "") {
        $error_message = "Memory field cannot be empty";
    } else {
        // Get the child's birthdate
        $sql_birthdate = "SELECT child_birthdate FROM users WHERE username = '$username'";
        $result_birthdate = mysqli_query($conn, $sql_birthdate);

        if (!$result_birthdate) {
            $error_message = "Error: " . mysqli_error($conn);
        } else {
            // Fetch the child's birthdate
            $row_birthdate = mysqli_fetch_assoc($result_birthdate);
            $child_birthdate = $row_birthdate['child_birthdate'];

            // Check if memory date is before the child's birthdate
            if ($memory_date < $child_birthdate) {
                // Display an error message
                $error_message = "Memory date cannot be before the child's birthdate";
            } else {
                // SQL query to insert memory data into the database
                $sql = "INSERT INTO memories (username, memory, memory_date) VALUES ('$username', '$memory', '$memory_date')";

                // Execute the query
                if (mysqli_query($conn, $sql)) {
                    // Set success message
                    $success_message = "Memory added successfully!";
                    // Redirect to dashboard
                    header("Location: dashboard.php?success=1");
                    exit();
                } else {
                    // Error inserting memory, show error message
                    $error_message = "Error: " . $sql . "<br>" . mysqli_error($conn);
                }
            }
        }
    }

    // Close database connection
    mysqli_close($conn);

    // Redirect back to the dashboard with error message, if any
    header("Location: dashboard.php?error=" . urlencode($error_message));
    exit();
}
