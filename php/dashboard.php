<?php
// Start session
session_start();

// Check if the user is logged in
if (!isset($_SESSION["username"])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Check if success parameter is set
$success_message = "";
if (isset($_GET["success"]) && $_GET["success"] == 1) {
    $success_message = "Memory added successfully!";
}

// Check if error parameter is set
$error_message = "";
if (isset($_GET["error"])) {
    $error_message = $_GET["error"];
}

// Establish a database connection
require_once "db_connection.php";

// Get the child's name for the logged-in user
$username = $_SESSION["username"];
$sql = "SELECT child_name FROM users WHERE username = '$username'";
$result = mysqli_query($conn, $sql);

// Check if the query was successful
if (!$result) {
    $error_message = "Error: " . mysqli_error($conn);
} else {
    // Check if there's any data returned from the query
    if (mysqli_num_rows($result) > 0) {
        // Fetch the row and extract the child's name
        $row = mysqli_fetch_assoc($result);
        // Assign the child's name to a variable
        $child_name = $row['child_name'];
    } else {
        // If no data is found, set a default child name
        $child_name = "Your Child";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Final Project | COMP1006</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>

<body>
    <div class="container">
        <!-- Welcome message with the user's name -->
        <h2>Welcome, <?php echo $_SESSION["username"]; ?>!</h2>
        <!-- Display memory adding success message if set -->
        <?php if (!empty($success_message)) : ?>
            <div class="success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <!-- Display date validation error message if set -->
        <?php if (!empty($error_message)) : ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <div class="form-container">
            <!-- Form for adding a memory, including the child's name -->
            <h3>Add <?php echo $child_name ?>'s First Memory</h3>
            <!-- Form submits data to add_memory.php -->
            <form action="add_memory.php" method="post">
                <div class="memory-input">
                    <label>First Day of</label>
                    <input type="text" name="memory" placeholder="Enter the memory" required>
                    <input type="date" name="memory_date" required>
                </div>
                <button type="submit">Add Memory</button>
            </form>
            <!-- Link to view the timeline -->
            <a href="timeline.php">View Timeline</a>
            <br>
            <!-- Link to log out -->
            <a href="../index.html">Logout</a>
        </div>
    </div>
</body>

</html>