<?php
session_start();
if (!isset($_SESSION["username"])) {
    header('location: ../index.php');
}
?>

<?php
// Connect to the database
include 'connection/db_connection.php';

$sql = "SELECT
    g.generator_id AS generator_id,
    g.site_id AS site_id,
    g.status AS status,
    g.installation_date AS installation_date,
    g.actual_run_hours AS actual_run_hours,
    g.latest_actual_run_hrs AS latest_actual_run_hrs,
    s.secondary_id AS secondary_id,
    s.site_name AS site_name
FROM 
    generators g
JOIN 
    sites s ON g.site_id = s.primary_id";
$result = $conn->query($sql);


if (!$result) {
    die("Query failed: " . $conn->error);
}

?>

<style>
    i {
        font-size: 20px;
    }

    .table a {
        padding: 1px 1px 1px 1px;
    }

    input[type="search"] {
        width: 100%;
        padding: 5px 5px 5px 5px;
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
    // Get the reference to the div element
    var divElement = document.getElementById("message-container");

    // Function to remove the div from the DOM
    function removeDiv() {
        divElement.parentNode.removeChild(divElement);
    }

    // Add the 'removeDiv' function as an event listener for 'transitionend' event
    divElement.addEventListener("transitionend", removeDiv);

    // Trigger the fade-out effect after a short delay
    setTimeout(function() {
        divElement.classList.add("fade-out");
    }, 2000); // Delay of 2 second (2000 milliseconds)
</script>

<?php include 'header_aside.php'; ?>


<!-- main -->
<main id="main" class="main">

    <?php if (isset($_SESSION['message'])) { ?>
        <div class="container" id="message-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-success d-flex justify-content-between align-items-center">
                        <?php echo $_SESSION['message']; ?>
                        <!-- add button for removing the alert message -->
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
                    <h2 class="text-center">List of all Generators</h2>
                    <hr>

                    <button onclick="window.location.href='export-sites.php?&format=csv'" class="btn btn-primary">Download as CSV</button>
                    <button onclick="window.location.href='export-sites.php?&format=excel'" class="btn btn-secondary">Download as Excel</button>
                    <hr>

                </div>

                <table class="table datatable table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">Generator ID</th>
                            <th scope="col">Site Name</th>
                            <th scope="col">Status</th>
                            <th scope="col">Projected RHrs</th>
                            <th scope="col">Age(Months)</th>
                            <th scope="col">Actual RHs</th>
                            <th scope="col">Latest Actual RHrs</th>

                        </tr>
                    </thead>
                    <tbody>

                        <?php

                        // Fetch all site_ids and count their occurrences
                        $site_id_counts = [];
                        $sql_count = "SELECT site_id, COUNT(*) as count FROM generators GROUP BY site_id";
                        $count_result = $conn->query($sql_count);

                        if ($count_result) {
                            while ($row = $count_result->fetch_assoc()) {
                                $site_id_counts[$row['site_id']] = $row['count'];
                            }
                        } else {
                            die("Query failed: " . $conn->error);
                        }

                        // Reset the main query result pointer
                        $result->data_seek(0);

                        while ($generator = $result->fetch_assoc()) {
                            $site_id = $generator['secondary_id'];
                            $count = $site_id_counts[$site_id] ?? 1; // Default to 1 if not found

                            // Determine the generator ID format
                            if ($count > 1) {
                                // If site_id appears more than once, use _G1, _G2, etc.
                                static $site_id_index = []; // Track index for each site_id
                                if (!isset($site_id_index[$site_id])) {
                                    $site_id_index[$site_id] = 1; // Initialize index for this site_id
                                }
                                $generator_id = $site_id . '_G' . $site_id_index[$site_id]++;
                            } else {
                                // If site_id appears only once, use _G1
                                $generator_id = $site_id . '_G1';
                            }
                        ?>
                            <tr>
                                <td>
                                    <form action="generator-details.php?generator_id=<?php echo htmlspecialchars($generator['generator_id']); ?>" method="POST" style="display:inline;">
                                        <?php $real_generator_id = $generator_id ?>
                                        <input type="hidden" name="real-generator-id" value="<?php echo htmlspecialchars($real_generator_id); ?>"> <!-- Example of additional data -->
                                        <button type="submit" title="More details" class="text-dark genid-deco" style="border: none; background: none; padding: 0;">
                                            <?php echo htmlspecialchars($real_generator_id); ?>
                                        </button>
                                    </form>
                                </td>
                                <td><?php echo $generator['site_name']; ?></td>
                                <td><?php echo $generator['status']; ?></td>
                                <?php
                                $generator_id = $generator['generator_id'];
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
                                } else {
                                    echo "Not available";
                                }
                                ?>
                                <td>
                                    <?php if (strtolower($generator['status']) === strtolower("Not Operational")) {
                                        echo "Stopped";
                                    } else {
                                        echo $cumulative_hours ?? "Not available";
                                    } ?>
                                </td>
                                <td><?php echo $genset_age; ?></td>
                                <td><?php echo $generator['actual_run_hours']; ?></td>
                                <td scope="row">
                                    <?php echo htmlspecialchars($generator['latest_actual_run_hrs']); ?>
                                    <a href="update-run-hour.php?generator_id=<?php echo htmlspecialchars($generator['generator_id']); ?>"
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

<!-- ======= Footer ======= -->
<?php include 'footer.php'; ?>