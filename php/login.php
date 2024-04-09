<?php

session_start();

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Establish a database connection
    require_once "db_connection.php";

    // Get username and password from the form
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Prepare statement to retrieve hashed password from the database
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);

    // Execute the query
    $stmt->execute();

    // Store the result
    $result = $stmt->get_result();

    // If there's exactly one existing user with matching username
    if ($result->num_rows == 1) {
        // Retrieve user information
        $row = $result->fetch_assoc();

        // Verify the hashed password
        if (password_verify($password, $row["password"])) {
            // Set session variables
            $_SESSION["id"] = $row["id"];
            $_SESSION["username"] = $row["username"];
            // Redirect to dashboard.php
            header("Location: dashboard.php");
            exit();
        } else {
            // Invalid password, show error message
            $error_message = "Invalid username or password.";
        }
    } else {
        // User does not exist, show error message
        $error_message = "Invalid username or password.";
    }

    // Close statement
    $stmt->close();
    // Close connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Final Project | COMP1006</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>

<body>
    <div class="container">
        <h1>Login</h1>
        <div class="error">
            <?php // Display error message if it's set
            if (isset($error_message)) echo $error_message; ?>
        </div>
        <!-- Login form -->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <!-- Link to register page -->
        <p>Don't have an account? <a href="register.php">Register here</a>.</p>
    </div>
</body>

</html>