<?php

session_start();
if (!isset($_SESSION["username"])) {
    header('location: ../index.php');
}
// Connect to the database
include 'connection/db_connection.php';

include 'header_aside.php';

// Retrieve the site ID from the GET request
$siteId = isset($_GET['site_id']) ? $_GET['site_id'] : null;

// Validate the input
if (!$siteId || !preg_match('/^[a-zA-Z0-9_-]+$/', $siteId)) {
    die("Invalid site ID");
}

$gen_sql = "SELECT generator_id FROM generators WHERE site_id = '$siteId'";
$gen_result = $conn->query($gen_sql);

// Validate the result
if (!$gen_result || $gen_result->num_rows === 0) {
    die("No generators found for the given site ID.");
}

// Create an array of generator IDs
$generatorIds = [];
while ($gen = $gen_result->fetch_assoc()) {
    $generatorIds[] = $gen['generator_id'];
}
$generatorIdList = implode("','", $generatorIds); // Prepare for SQL query

// Retrieve the list of generators and their details
$sql = "SELECT 
            g.status AS gen_status, 
            s.site_id AS site_id, 
            s.site_name AS site_name, 
            g.generator_id AS gen_id, 
            s.primary_id AS primary_id,
            s.secondary_id AS secondary_id,
            g.actual_run_hours AS actual_run_hours,
            g.latest_actual_run_hrs AS latest_actual_run_hrs 
        FROM 
            sites s 
        LEFT JOIN 
            generators g 
        ON 
            g.site_id = s.primary_id 
        WHERE 
            g.generator_id IN ('$generatorIdList')";
$result = $conn->query($sql);

// Check for query execution errors
if (!$result) {
    die("Database query failed: " . $conn->error);
}
?>

<style>
    i {
        font-size: 20px;
    }

    .table a {
        padding: 1px;
    }

    input[type="search"] {
        width: 100%;
        padding: 5px;
        border: 1px solid #ccc;
        border-radius: 4px;
        resize: vertical;
    }

    .genid-deco:hover {
        text-decoration: underline;
        color: green;
    }
</style>

<script>
    // Remove alert message after a short delay
    setTimeout(() => {
        const messageContainer = document.getElementById("message-container");
        if (messageContainer) {
            messageContainer.remove();
        }
    }, 2000); // Delay of 2 seconds
</script>

<!-- main -->
<main id="main" class="main">

    <?php if (isset($_SESSION['message'])) { ?>
        <div class="container" id="message-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-success d-flex justify-content-between align-items-center">
                        <?php echo $_SESSION['message']; ?>
                        <!-- Add button for removing the alert message -->
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        </div>
    <?php }
    unset($_SESSION['message']); ?>

    <section>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h2>List of Generators</h2>
                    <hr>
                </div>

                <table class="table datatable table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">Status</th>
                            <th scope="col">Generator ID</th>
                            <th scope="col">Site Name</th>
                            <th scope="col">Projected RHrs</th>
                            <th scope="col">Gen Age(Months)</th>
                            <th scope="col">Actual RHrs</th>
                            <th scope="col">Latest Actual RHrs</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $index = 1;
                        while ($generator = $result->fetch_assoc()) {
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($generator['gen_status']); ?></td>
                                <td>

                                    <form action="generator-details.php?generator_id=<?php echo htmlspecialchars($generator['gen_id']); ?>" method="POST" style="display:inline;">
                                        <?php $real_generator_id = $generator['secondary_id'] . '_G' . $index++ ?>
                                        <input type="hidden" name="real-generator-id" value="<?php echo htmlspecialchars($real_generator_id); ?>"> <!-- Example of additional data -->
                                        <button type="submit" title="More details" class="text-dark genid-deco" style="border: none; background: none; padding: 0;">
                                            <?php echo htmlspecialchars($real_generator_id); ?>
                                        </button>
                                    </form>

                                </td>
                                <td><?php echo htmlspecialchars($generator['site_name']); ?></td>
                                <!-- <td> -->
                                <?php
                                $generator_id = $generator['gen_id'];
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
                                <!-- </td> -->
                                <td><?php
                                    if (strtolower($generator['gen_status']) === strtolower("Not Operational")) {
                                        echo "Stopped";
                                    } else {
                                        echo $cumulative_hours ?? "Not available";
                                    }
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($genset_age); ?></td>
                                <td><?php echo htmlspecialchars($generator['actual_run_hours']); ?></td>
                                <td scope="row">
                                    <?php echo htmlspecialchars($generator['latest_actual_run_hrs']); ?>
                                    <a href="update-run-hour.php?generator_id=<?php echo htmlspecialchars($generator['gen_id']); ?>"
                                        title="Update" class="text-success"><i class="bi bi-pencil-square"></i></a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</main><!-- End #main -->

<!-- Footer -->
<?php include 'footer.php'; ?>