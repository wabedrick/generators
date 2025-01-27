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
    foreach ($_POST as $key => $value) {
        if ($key != 'generator_id' && $key != 'submit') { // Exclude ID and submit button
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

        if ($conn->query($update_sql)) {
            echo "<div class='alert alert-success'>Generator information updated successfully.</div>";
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
</style>

<main id="main" class="main">
    <form method="post">
        <input type="hidden" name="generator_id" value="<?php echo $generator_id; ?>">
        <div class="row generator-details">
            <h3 class="text-center" style="font-size: 1.5rem; text-decoration:underline;"><?php echo htmlspecialchars($generator['generator_id'] . ', ' . $generator['site_name']); ?></h3>
            <div class="col">
                <p><span class="label me-4">Projected Run Hours:</span> <?php echo htmlspecialchars($cumulative_hours); ?></p>
                <p><span class="label me-4">Generator Age:</span> <?php echo htmlspecialchars($genset_age) . ' months'; ?></p>
                <p><span class="label me-4">Actual Run Hours:</span><input class="editable" type="text" name="actual_run_hours" value="<?php echo htmlspecialchars($generator['actual_run_hours']); ?>"></p>
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
        <button type="submit" name="submit" class="btn btn-primary">Update</button>
    </form>
</main>

<?php include 'footer.php'; ?>