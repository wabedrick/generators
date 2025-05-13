<?php
include 'connection/db_connection.php';
session_start();
if (!isset($_SESSION["username"])) {
    header('location: ../index.php');
}

// Get parameters from URL
$status = isset($_GET['status']) ? $_GET['status'] : '';
$interval = isset($_GET['interval']) ? $_GET['interval'] : '12 HOUR';
$items_per_page = isset($_GET['items_per_page']) ? (int)$_GET['items_per_page'] : 10; // Default to 10 items per page
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Default to first page

// Validate status
if ($status != 'Up' && $status != 'Down') {
    die("Invalid status parameter");
}

// Validate interval
$allowed_units = ['HOUR', 'DAY'];
$interval_parts = explode(' ', $interval);
if (count($interval_parts) !== 2 || !is_numeric($interval_parts[0]) || !in_array(strtoupper($interval_parts[1]), $allowed_units)) {
    die("Invalid interval format");
}

// Validate items_per_page
$valid_items_per_page = [5, 10, 20];
if (!in_array($items_per_page, $valid_items_per_page)) {
    $items_per_page = 10; // Reset to default if invalid
}

// First, get the total count for pagination
$count_sql = "SELECT COUNT(*) as total FROM sites s WHERE site_status = 'Active' AND active_site_status = ? 
              AND (downtime_date >= NOW() - INTERVAL $interval OR downtime_date IS NULL)";
$count_stmt = mysqli_prepare($conn, $count_sql);
mysqli_stmt_bind_param($count_stmt, 's', $status);
mysqli_stmt_execute($count_stmt);
$count_result = mysqli_stmt_get_result($count_stmt);
$total_records = mysqli_fetch_assoc($count_result)['total'];

// Calculate total pages
$total_pages = ceil($total_records / $items_per_page);

// Ensure current_page is within valid range
if ($current_page < 1) {
    $current_page = 1;
} elseif ($current_page > $total_pages) {
    $current_page = $total_pages > 0 ? $total_pages : 1;
}

// Calculate offset for pagination
$offset = ($current_page - 1) * $items_per_page;

// Prepare paginated query
$sql = "SELECT 
*, (SELECT COUNT(*) FROM dependencies sd WHERE s.primary_id = sd.site_id) AS dependent_sites_count, 
(SELECT COUNT(DISTINCT g.generator_id) FROM generators g WHERE s.primary_id = g.site_id) AS number_of_generators_count 
FROM sites s WHERE site_status = 'Active' AND active_site_status = ? AND (downtime_date >= NOW() - INTERVAL $interval OR downtime_date IS NULL)
    ORDER BY created_at DESC LIMIT ? OFFSET ?";

// Prepare statement
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'sii', $status, $items_per_page, $offset);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Check for errors
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

include 'header_aside.php';
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

    .pagination {
        justify-content: center;
        margin-top: 20px;
    }

    .items-per-page {
        width: auto;
        display: inline-block;
    }

    .page-controls {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 20px;
    }
</style>

<script>
    // Get the reference to the div element
    document.addEventListener('DOMContentLoaded', function() {
        var divElement = document.getElementById("message-container");

        if (divElement) {
            // Function to remove the div from the DOM
            function removeDiv() {
                divElement.parentNode.removeChild(divElement);
            }

            // Add the 'removeDiv' function as an event listener for 'transitionend' event
            divElement.addEventListener("transitionend", removeDiv);

            // Trigger the fade-out effect after a short delay
            setTimeout(function() {
                divElement.classList.add("fade-out");
            }, 2000); // Delay of 2 seconds (2000 milliseconds)
        }
    });
</script>

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
                    <h2 class="text-center">Sites with Status: <span class="badge <?php echo $status == 'Down' ? 'bg-danger' : 'bg-success'; ?>"><?php echo $status; ?></span></h2>
                    <p class="text-center">Time Interval: <?php echo htmlspecialchars($interval); ?></p>
                    <hr>

                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <button onclick="window.location.href='export-sites.php?status=<?php echo $status; ?>&interval=<?php echo $interval; ?>&format=csv'" class="btn btn-primary">Download as CSV</button>
                            <button onclick="window.location.href='export-sites.php?status=<?php echo $status; ?>&interval=<?php echo $interval; ?>&format=excel'" class="btn btn-secondary">Download as Excel</button>
                        </div>
                    </div>
                    <hr>
                </div>

                <div class="table-responsive">
                    <table class="table datatable table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">Site ID</th>
                                <th scope="col">Site Name</th>
                                <th scope="col">Dependents</th>
                                <th scope="col">Generators</th>
                                <th scope="col">Department</th>
                                <th scope="col">Priority</th>
                                <th scope="col">Class</th>
                                <?php
                                if ($status == 'Down') {
                                    echo '<th scope="col">Downtime Date</th>';
                                }
                                ?>
                                <th scope="col">Last Updated</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['secondary_id']); ?></td>
                                        <td><?php echo htmlspecialchars($row['site_name']); ?></td>
                                        <td>
                                            <?php echo htmlspecialchars($row['dependent_sites_count']); ?>
                                            <?php if ($row['dependent_sites_count'] > 0): ?>
                                                <a href="view-dependent-site.php?site_id=<?php echo $row['primary_id']; ?>"
                                                    title="View" class="text-"><i class="bi bi-eye"></i></a>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($row['number_of_generators_count']); ?>
                                            <?php if ($row['number_of_generators_count'] > 0): ?>
                                                <a href="view-generator.php?site_id=<?php echo $row['primary_id']; ?>"
                                                    title="View" class="text-"><i class="bi bi-eye"></i></a>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['department']); ?></td>
                                        <td><?php echo htmlspecialchars($row['tx_site_type']); ?></td>
                                        <td><?php echo htmlspecialchars($row['class']); ?></td>
                                        <td>
                                            <?php
                                            if ($row['active_site_status'] == 'Down') {
                                                echo $row['downtime_date'] ? date('Y-m-d H:i:s', strtotime($row['downtime_date'])) : 'N/A';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php echo date('Y-m-d H:i:s', strtotime($row['created_at'])); ?></td>
                                        <td>
                                            <a href="site_view.php?id=<?php echo $row['site_id']; ?>"
                                                title="View" class="text-info"><i class="bi bi-eye"></i></a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="11" class="text-center">No sites found with status <?php echo $status; ?> in the selected time interval.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <div class="d-flex justify-content-center">
                        <nav aria-label="Page navigation">
                            <ul class="pagination">
                                <!-- First page link -->
                                <li class="page-item <?php echo $current_page <= 1 ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?status=<?php echo urlencode($status); ?>&interval=<?php echo urlencode($interval); ?>&items_per_page=<?php echo $items_per_page; ?>&page=1" aria-label="First">
                                        <span aria-hidden="true">&laquo;&laquo;</span>
                                    </a>
                                </li>
                                <!-- Previous page link -->
                                <li class="page-item <?php echo $current_page <= 1 ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?status=<?php echo urlencode($status); ?>&interval=<?php echo urlencode($interval); ?>&items_per_page=<?php echo $items_per_page; ?>&page=<?php echo $current_page - 1; ?>" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>

                                <!-- Page numbers -->
                                <?php
                                $start_page = max(1, $current_page - 2);
                                $end_page = min($total_pages, $current_page + 2);

                                // Always show first page
                                if ($start_page > 1) {
                                    echo '<li class="page-item"><a class="page-link" href="?status=' . urlencode($status) . '&interval=' . urlencode($interval) . '&items_per_page=' . $items_per_page . '&page=1">1</a></li>';
                                    if ($start_page > 2) {
                                        echo '<li class="page-item disabled"><a class="page-link">...</a></li>';
                                    }
                                }

                                // Show page numbers
                                for ($i = $start_page; $i <= $end_page; $i++) {
                                    echo '<li class="page-item ' . ($i == $current_page ? 'active' : '') . '"><a class="page-link" href="?status=' . urlencode($status) . '&interval=' . urlencode($interval) . '&items_per_page=' . $items_per_page . '&page=' . $i . '">' . $i . '</a></li>';
                                }

                                // Always show last page
                                if ($end_page < $total_pages) {
                                    if ($end_page < $total_pages - 1) {
                                        echo '<li class="page-item disabled"><a class="page-link">...</a></li>';
                                    }
                                    echo '<li class="page-item"><a class="page-link" href="?status=' . urlencode($status) . '&interval=' . urlencode($interval) . '&items_per_page=' . $items_per_page . '&page=' . $total_pages . '">' . $total_pages . '</a></li>';
                                }
                                ?>

                                <!-- Next page link -->
                                <li class="page-item <?php echo $current_page >= $total_pages ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?status=<?php echo urlencode($status); ?>&interval=<?php echo urlencode($interval); ?>&items_per_page=<?php echo $items_per_page; ?>&page=<?php echo $current_page + 1; ?>" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                                <!-- Last page link -->
                                <li class="page-item <?php echo $current_page >= $total_pages ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?status=<?php echo urlencode($status); ?>&interval=<?php echo urlencode($interval); ?>&items_per_page=<?php echo $items_per_page; ?>&page=<?php echo $total_pages; ?>" aria-label="Last">
                                        <span aria-hidden="true">&raquo;&raquo;</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                <?php endif; ?>

                <!-- Display pagination info -->
                <div class="text-center mt-3">
                    <p class="text-muted">
                        Showing <?php echo ($total_records == 0) ? 0 : (($current_page - 1) * $items_per_page + 1); ?>
                        to <?php echo min($current_page * $items_per_page, $total_records); ?>
                        of <?php echo $total_records; ?> entries
                    </p>
                </div>
            </div>
        </div>
    </section>
</main><!-- End #main -->

<!-- ======= Footer ======= -->
<?php include 'footer.php'; ?>