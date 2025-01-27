<?php
include 'connection/db_connection.php';
session_start();
if (!isset($_SESSION["username"])) {
    header('location: ../index.php');
}

include 'header_aside.php';
// Initialize $generator to an empty array to avoid undefined variable warnings
$generator = [];

// Fetch generator details for the form
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['generator_id'])) {
    $generator_id = $conn->real_escape_string($_GET['generator_id']);
    $sql = "SELECT * FROM sites 
            INNER JOIN generators ON sites.primary_id = generators.site_id 
            WHERE generators.generator_id = '$generator_id'";
    $result = $conn->query($sql);
    $generator = $result->fetch_assoc() ?: [];
}
?>

<main id="main" class="main">

    <?php if (isset($_SESSION['message'])) { ?>
        <div class="container" id="message-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-<?php echo $_SESSION['message_type']; ?> d-flex justify-content-between align-items-center">
                        <?php echo $_SESSION['message']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        </div>
    <?php }
    unset($_SESSION['message']);
    unset($_SESSION['message_type']); ?>

    <section class="section">
        <!-- Form to update the generator -->
        <form method="POST" action="" enctype="multipart/form-data" class="row g-3">
            <input type="hidden" name="generator_id" value="<?php echo isset($generator['generator_id']) ? $generator['generator_id'] : ''; ?>">

            <div class="col-md-6">
                <label for="inputAddress5" class="form-label">Site ID</label>
                <input type="text" class="form-control" id="inputAddres5s" name="site-id" value="<?php echo isset($generator['site_id']) ? $generator['site_id'] : ''; ?>">
            </div>

            <div class="col-md-6">
                <label for="group" class="form-label">Site Name</label>
                <select name="site" id="group" class="form-control form-control-user" required>
                    <option value="<?php echo isset($generator['site_name']) ? $generator['site_name'] : ''; ?>" selected>
                        <?php echo isset($generator['site_name']) ? $generator['site_name'] : 'Select a site'; ?>
                    </option>
                    <?php
                    $siteQuery = "SELECT * FROM sites";
                    $sites = $conn->query($siteQuery);
                    while ($site = $sites->fetch_assoc()) {
                    ?>
                        <option value="<?php echo $site['id']; ?>"><?php echo $site['site_name']; ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="col-md-6">
                <label for="inputAddress5" class="form-label">Generator ID</label>
                <input type="text" class="form-control" id="inputAddres5s" name="generator_id"
                    value="<?php echo isset($generator['site_id']) && isset($generator['generator_id']) ? htmlspecialchars($generator['site_id'] . '_G' . $generator['generator_id']) : ''; ?>" required>
            </div>

            <div class="col-md-6">
                <label for="inputAddress5" class="form-label">Update Actual Run Hour</label>
                <input type="number" class="form-control" id="inputAddres5s" name="actual-run-hour"
                    value="<?php echo isset($generator['latest_actual_run_hrs']) ? htmlspecialchars($generator['latest_actual_run_hrs']) : ''; ?>" required>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </section>

</main>

<?php include 'footer.php'; ?>

<?php

include 'connection/db_connection.php';
// Update the generator record if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $generator_id = $conn->real_escape_string($_POST['generator_id']);
    $updated_actual_run_hour = $conn->real_escape_string($_POST['actual-run-hour']);

    // Extracting the numbers from the strings, I am extracting the last numbers
    // If the string has other numbers inbetween
    preg_match('/\d+(?!.*\d)/', $generator_id, $matches);
    $real_gen_id = intval($matches[0]);
    // echo $number;
    // die();

    $sql = "SELECT installation_date FROM generators WHERE generator_id='$real_gen_id'";
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

    // Fetch current actual run time and projected run time
    $sql = "SELECT actual_run_hours, site_id FROM generators WHERE generator_id='$real_gen_id'";
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
                    WHERE generator_id='$real_gen_id'";
            if ($conn->query($sql) === TRUE) {
                $_SESSION['message'] = "Generator Actual Run Hours updated successfully";
                $_SESSION['message_type'] = "success";
                echo "<script>window.location.href = 'view-generator.php?site_id=$siteID';</script>";
                exit;
            } else {
                echo "Error: " . $conn->error;
            }
        }
    } else {
        $_SESSION['message'] = "Error: Generator not found.";
        $_SESSION['message_type'] = "danger";
    }
}
