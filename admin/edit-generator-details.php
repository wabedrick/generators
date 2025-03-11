<?php

include 'connection/db_connection.php';
session_start();
if (!isset($_SESSION["username"])) {
    header('location: ../index.php');
}

include 'header_aside.php';

$generator = [];
if (isset($_GET['generator_id'])) {
    $generator_id = $conn->real_escape_string($_GET['generator_id']);
} else {
    die("Error: Generator ID not provided in the URL.");
}

// Fetch generator data
$sql = "SELECT * FROM sites 
        INNER JOIN generators ON sites.primary_id = generators.site_id 
        WHERE generators.generator_id = '$generator_id'";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    $generator = $result->fetch_assoc();
} else {
    die("Error: Generator not found.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updated_data = [];
    $real_generator_id = mysqli_real_escape_string($conn, $_POST['real-generator-id']);
    $updated_actual_run_hour = mysqli_real_escape_string($conn, $_POST['latest_actual_run_hrs']);
    foreach ($_POST as $key => $value) {
        // Exclude 'generator_id', 'real-generator-id', and 'submit' from the update
        if ($key != 'generator_id' && $key != 'real-generator-id' && $key != 'actual_run_hours' && $key != 'submit') {
            $updated_data[$key] = $conn->real_escape_string($value);
        }
    }

    if (!empty($updated_data)) {
        $update_sql = "UPDATE generators SET ";
        $updates = [];
        foreach ($updated_data as $key => $value) {
            $updates[] = "`$key` = '$value'";
        }
        $update_sql .= implode(", ", $updates);
        $update_sql .= " WHERE generator_id = '$generator_id'";

        // Debug the query
        echo "<pre>$update_sql</pre>"; // Optional for debugging

        $sql = "SELECT installation_date FROM generators WHERE generator_id='$generator_id'";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $installationDate = $row['installation_date'];

            // Calculate the difference in hours
            date_default_timezone_set('Africa/Nairobi');
            $current_time = new DateTime();

            $created_time = new DateTime($installationDate);
            $interval = $current_time->diff($created_time);

            $cummulative_total_hours = ($interval->days * 24) + $interval->h;
        } else {
            echo "No item found with the given ID.";
            $cummulative_total_hours = 0;
        }

        if ($conn->query($update_sql)) {
            // Fetch current actual run time and projected run time
            $sql = "SELECT actual_run_hours, site_id FROM generators WHERE generator_id='$generator_id'";
            $result = $conn->query($sql);
            if ($result && $result->num_rows > 0) {
                $generator_data = $result->fetch_assoc();

                $run_hour_update_date = new DateTime();
                $run_hour_update_date = $run_hour_update_date->format('Y-m-d H:i:s');

                $siteID = $generator_data['site_id'];
                $current_actual_run_time = $generator_data['actual_run_hours'];
                $projected_run_time = $cummulative_total_hours;

                // Calculate new total actual run time
                $new_total_actual_run_time = $current_actual_run_time + $updated_actual_run_hour;

                // Check if new total exceeds projected run time
                if ($new_total_actual_run_time > $projected_run_time) {
                    $_SESSION['message'] = "Error: The total actual run time ($new_total_actual_run_time) exceeds the projected run time ($projected_run_time).";
                    $_SESSION['message_type'] = "danger";
                } else {
                    // Update query if validation passes

                    $sql = "UPDATE generators 
                    SET actual_run_hours='$new_total_actual_run_time', latest_actual_run_hrs='$updated_actual_run_hour', 
                    rhr_update_date = '$run_hour_update_date'
                    WHERE generator_id='$generator_id'";
                }
            } else {
                $_SESSION['message'] = "Error: Generator not found.";
                $_SESSION['message_type'] = "danger";
            }
            echo "<div id='success-message' class='alert text-center alert-success'>Generator information updated successfully.</div>";
            $real_generator_id = mysqli_real_escape_string($conn, $_POST['real-generator-id']);
        } else {
            echo "<div class='alert alert-danger'>Error updating generator information: " . $conn->error . "</div>";
        }
    }
}

//Calculate generator age
$installation_sql = "SELECT installation_date FROM generators WHERE generator_id = '$generator_id'";
$installation_result = $conn->query($installation_sql);
if ($installation_result && $installation_result->num_rows > 0) {
    $row = $installation_result->fetch_assoc();
    $installation_date = $row['installation_date'];

    date_default_timezone_set('Africa/Nairobi');
    $current_time = new DateTime();
    $created_time = new DateTime($installation_date);
    $interval = $current_time->diff($created_time);

    $cumulative_hours = ($interval->days * 24) + $interval->h;
    $genset_age = $cumulative_hours == 0 ? 0 : round($cumulative_hours / (24 * 30.42), 1);
} else {
    $cumulative_hours = "N/A";
    $genset_age = "N/A";
}

?>

<style>
    /* ... (Existing styles) */
    .editable {
        border: 1px solid #ccc;
        padding: 5px;
        border-radius: 3px;
        margin: 2px 0;
    }

    .editable:focus {
        outline: none;
        box-shadow: 0 0 5px rgba(0, 123, 255, .5);
        border: 1px solid #80bdff;
    }


    /* The success message to fadeout after some time in a way that's nice */
    .fade-out {
        opacity: 0;
        transition: opacity 1s ease-out;
        /* Fade out over 1 second */
    }
</style>

<script>
    function removeSuccessMessage() {
        const successMessage = document.getElementById('success-message');
        if (successMessage) {
            setTimeout(() => {
                successMessage.classList.add('fade-out'); // Add fade-out class
                setTimeout(() => {
                    successMessage.remove(); // Remove the message after the fade-out completes
                }, 1000); // Wait for the fade-out transition to finish
            }, 4000); // Start fade-out after 9 seconds (total 10 seconds)
        }
    }

    window.onload = removeSuccessMessage;
</script>

<main id="main" class="main">
    <form method="post">
        <input type="hidden" name="generator_id" value="<?php echo $generator_id; ?>">
        <div class="row generator-details">
            <h3 class="text-center" style="font-size: 1.5rem; text-decoration:underline;"><?php echo htmlspecialchars($real_generator_id); ?></h3>
            <div class="col">
                <input type="hidden" name="real-generator-id" value="<?php echo htmlspecialchars($real_generator_id ?? ''); ?>">
                <p><span class="label me-4">Projected Run Hours:</span> <?php echo htmlspecialchars($cumulative_hours); ?></p>
                <p><span class="label me-4">Generator Age:</span> <?php echo htmlspecialchars($genset_age) . ' months'; ?></p>
                <p><span class="label me-4">Actual Run Hours:</span><input class="editable" type="text" name="actual_run_hours" value="<?php echo htmlspecialchars($generator['actual_run_hours']); ?>"></p>
                <p><span class="label me-4">Latest Actual Run Hrs:</span><input class="editable" type="text" name="latest_actual_run_hrs" value="<?php echo htmlspecialchars($generator['latest_actual_run_hrs']); ?>"></p>
                <p><span class="label me-4">Brand:</span><input class="editable" type="text" name="brand" value="<?php echo htmlspecialchars($generator['brand']); ?>"></p>
                <p><span class="label me-4">Capacity:</span><input class="editable" type="text" name="capacity" value="<?php echo htmlspecialchars($generator['capacity']); ?>"></p>
                <p><span class="label me-4">Engine:</span><input class="editable" type="text" name="engine" value="<?php echo htmlspecialchars($generator['engine']); ?>"></p>
            </div>
            <div class="col">
                <p><span class="label me-4">Status:</span><input class="editable" type="text" name="status" value="<?php echo htmlspecialchars($generator['status']); ?>"></p>
                <p><span class="label me-4">Battery Capacity:</span><input class="editable" type="text" name="battery_capacity" value="<?php echo htmlspecialchars($generator['battery_capacity']); ?>"></p>
                <p><span class="label me-4">Power Type:</span><input class="editable" type="text" name="power_type" value="<?php echo htmlspecialchars($generator['power_type']); ?>"></p>
                <p><span class="label me-4">Operation Time(%):</span><input class="editable" type="text" name="percent_op_time" value="<?php echo htmlspecialchars($generator['percent_op_time']); ?>"></p>
                <p><span class="label me-4">Maintenance Scope:</span><input class="editable" type="text" name="maintenance_scope" value="<?php echo htmlspecialchars($generator['maintenance_scope']); ?>"></p>
                <p><span class="label me-4">Generator Mode:</span><input class="editable" type="text" name="gen_mode" value="<?php echo htmlspecialchars($generator['gen_mode']); ?>"></p>
            </div>
        </div>
        <button type="submit" name="submit" class="btn btn-primary text-center">Update</button>
    </form>
</main>

<?php include 'footer.php'; ?>