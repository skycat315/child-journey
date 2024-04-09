<?php
// Start session
session_start();

// Establish a database connection
require_once "db_connection.php";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Sanitize and retrieve each piece of information from the form to prevent SQL injection
    $username = mysqli_real_escape_string($conn, $_POST["username"]);
    $password = mysqli_real_escape_string($conn, $_POST["password"]);
    $confirm_password = mysqli_real_escape_string($conn, $_POST["confirm_password"]);
    $child_name = mysqli_real_escape_string($conn, $_POST["child_name"]);
    $child_birthdate = $_POST["child_birthdate"];

    // Validate password match
    if ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        // Check if username already exists
        $sql_check_username = "SELECT * FROM users WHERE username = '$username'";
        $result_check_username = mysqli_query($conn, $sql_check_username);
        if (mysqli_num_rows($result_check_username) > 0) {
            $error_message = "Username already exists. Please choose a different one.";
        } else {
            // Encrypt the password using a secure hashing algorithm
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert user data into the database
            $sql_insert_user = "INSERT INTO users (username, password, child_name, child_birthdate) VALUES ('$username', '$hashed_password', '$child_name', '$child_birthdate')";
            if (mysqli_query($conn, $sql_insert_user)) {
                // Registration successful, display success message
                $success_message = "Registration successful! You can now login.";
            } else {
                // Registration error, display error message
                $error_message = "Error: " . $sql_insert_user . "<br>" . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Final Project | COMP1006</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>

<body>
    <div class="container">
        <h2>Register</h2>
        <?php
        // Display a success message if registration is successful
        if (isset($success_message)) : ?>
            <div class="success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php
        // Display an error message if registration fails
        if (isset($error_message)) : ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <div class="form-container">
            <!-- Registration form -->
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="input-group">
                    <label>Username</label>
                    <input type="text" name="username" required>
                </div>
                <div class="input-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <div class="input-group">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" required>
                </div>
                <div class="input-group">
                    <label>Child's Name</label>
                    <input type="text" name="child_name" required>
                </div>
                <div class="input-group">
                    <label>Child's Birthdate</label>
                    <input type="date" name="child_birthdate" required>
                </div>
                <button type="submit">Register</button>
            </form>
            <!-- Link to login page -->
            <span>Already have an account? <a href="login.php">Login</a></span>
        </div>
    </div>
</body>

</html>