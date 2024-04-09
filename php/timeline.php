<?php
// Start session
session_start();

// Check if the user is logged in
if (!isset($_SESSION["username"])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Establish a database connection
require_once "db_connection.php";

// Get the child's name and birthdate for the logged-in user
$username = $_SESSION["username"];
$sql_child_info = "SELECT child_name, child_birthdate FROM users WHERE username = '$username'";
$result_child_info = mysqli_query($conn, $sql_child_info);

// Check if the query was successful
if (!$result_child_info) {
    echo "Error: " . mysqli_error($conn);
    exit();
}

// Check if there's a registered child associated with the user
if (mysqli_num_rows($result_child_info) > 0) {

    // Retrieve child information
    $row_child_info = mysqli_fetch_assoc($result_child_info);
    $child_name = $row_child_info['child_name'];
    $child_birthdate = $row_child_info['child_birthdate'];
} else {
    $child_name = "Your Child"; // Default to generic text if child's name is not found
    $child_birthdate = null;
}

// SQL query to retrieve memories for the logged-in user, sorted by date
$sql_memories = "SELECT memory_date, memory FROM memories WHERE username = '$username' ORDER BY memory_date ASC";
$result_memories = mysqli_query($conn, $sql_memories);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timeline | Final Project | COMP1006</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>

<body>
    <div class="container">
        <h2>Timeline of <?php echo $child_name ?>'s First Memories</h2>
        <div class="timeline">
            <?php
            // Check if memories are found
            if (mysqli_num_rows($result_memories) > 0) {
                // Loop through memories and display them
                while ($row_memories = mysqli_fetch_assoc($result_memories)) {
                    // Format the memory date
                    $memory_date = date("F j, Y", strtotime($row_memories['memory_date']));
                    $memory_description = $row_memories['memory'];

                    // Calculate the child's age at the time of the memory
                    if ($child_birthdate) {
                        $birthdate = new DateTime($child_birthdate);
                        $memory_date_obj = new DateTime($row_memories['memory_date']);
                        $age = $birthdate->diff($memory_date_obj);
                        $child_age_years = $age->y;
                        $child_age_months = $age->m;
                        $child_age_days = $age->d;

                        // Assign a class based on the remainder of the child's age divided by 7 to change the color
                        $class_name = "child-" . ($child_age_years % 7);
                    } else {
                        $child_age_years = "";
                        $child_age_months = "";
                        $child_age_days = "";
                    }

                    // Output the memory item with the assigned class
                    echo "<div class='timeline-item $class_name'>";
                    echo "<p><emphasis>$memory_description</emphasis><br>$memory_date<br>($child_age_years ";
                    echo ($child_age_years == 1) ? "year" : "years";
                    echo ", $child_age_months ";
                    echo ($child_age_months == 1) ? "month" : "months";
                    echo ", $child_age_days ";
                    echo ($child_age_days == 1) ? "day" : "days";
                    echo ")</p>";
                    echo "</div>";
                }
            } else {
                echo "<p>No memories found.</p>";
            }
            ?>
        </div>
        <br>
        <!-- Link to dashboard page -->
        <a href="dashboard.php">Back to Dashboard</a>
        <br>
        <!-- Link to home page -->
        <a href="../index.html">Logout</a>
    </div>
</body>

</html>