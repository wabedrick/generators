<?php

include 'connection/db_connection.php';
// Start a session
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // $generator_id = $conn->real_escape_string($_GET['generator_id']);
    $real_generator_id = $conn->real_escape_string($_POST['real-generator-id']);
    $sql = "SELECT * FROM sites 
            INNER JOIN generators ON sites.primary_id = generators.site_id 
            WHERE generators.generator_id = '$generator_id'";
    $result = $conn->query($sql);
    $generator = $result->fetch_assoc() ?: [];
}

?>

<style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
    }

    .generator-details {
        border: 1px solid #ccc;
        padding: 20px;
        border-radius: 5px;
        max-width: 100%;
        margin: auto;
    }

    .generator-details h2 {
        margin-top: 0;
    }

    .generator-details p {
        margin: 5px 0;
        font-size: 1.3rem;
    }

    .generator-details .label {
        font-weight: 600;
    }
</style>

<main id="main" class="main">
    <h1 class="text-center">GENERATOR DETAILED INFORMATION</h1>
    <div class="row generator-details">
        <?php
        $installation_sql = "SELECT installation_date FROM generators WHERE generator_id = '$generator_id'";
        $installation_result = $conn->query($installation_sql);

        if ($installation_result && $installation_result->num_rows > 0) {
            $row = $installation_result->fetch_assoc();
            $installation_date = $row['installation_date'];

            // Calculate runtime hours
            date_default_timezone_set('Africa/Nairobi');
            $current_time = new DateTime();
            $created_time = new DateTime($installation_date);
            $interval = $current_time->diff($created_time);

            $cumulative_hours = ($interval->days * 24) + $interval->h;

            // Getting the Generator age in months
            $genset_age = $cumulative_hours == 0 ? 0 : round($cumulative_hours / (24 * 30.42), 1);



            // echo $projected_hours;
        } else {
            echo "Not available";
        }
        ?>
        <h3 class="text-center" style="font-size: 1.5rem; text-decoration:underline;"><?php echo htmlspecialchars($real_generator_id); ?></h3>
        <div class="col">
            <p><span class="label">Projected Run Hours:</span> <?php echo htmlspecialchars($cumulative_hours); ?></p>
            <p><span class="label">Generator Age:</span> <?php echo htmlspecialchars($genset_age) . ' months'; ?></p>
            <p><span class="label">Actual Run Hours:</span> <?php echo htmlspecialchars($generator['actual_run_hours']); ?></p>
            <p><span class="label">Brand:</span> <?php echo htmlspecialchars($generator['brand']); ?></p>
            <p><span class="label">Capacity:</span> <?php echo htmlspecialchars($generator['capacity']); ?></p>
            <p><span class="label">Engine:</span> <?php echo htmlspecialchars($generator['engine']); ?></p>
        </div>
        <div class="col">
            <p><span class="label">Status:</span> <?php echo htmlspecialchars($generator['status']); ?></p>
            <p><span class="label">Battery Capacity:</span> <?php echo htmlspecialchars($generator['battery_capacity']); ?></p>
            <p><span class="label">Power Type:</span> <?php echo htmlspecialchars($generator['power_type']); ?></p>
            <p><span class="label">Operation Time(%):</span> <?php echo htmlspecialchars($generator['percent_op_time']); ?></p>
            <p><span class="label">Maintenance Scope:</span> <?php echo htmlspecialchars($generator['maintenance_scope']); ?></p>
            <p><span class="label">Generator Mode:</span> <?php echo htmlspecialchars($generator['gen_mode']); ?></p>
        </div>

    </div>

    <!-- Button to edit the Generator Information -->
    <!-- <a class="btn btn-success mt-4" href="edit-generator-details.php?generator_id=<?php echo htmlspecialchars($generator['generator_id']); ?>">
        Edit Information</a> -->


    <form action="edit-generator-details.php?generator_id=<?php echo htmlspecialchars($generator['generator_id']); ?>" method="POST" style="display:inline;">
        <input type="hidden" name="real-generator-id" value="<?php echo htmlspecialchars($real_generator_id); ?>"> <!-- Example of additional data -->
        <button type="submit" title="More details" class="btn btn-success mt-4">
            Edit Information
        </button>
    </form>
</main>

<?php include 'footer.php'; ?>