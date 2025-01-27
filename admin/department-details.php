<?php
session_start();
if (!isset($_SESSION["username"])) {
    header('location: ../index.php');
}
?>

<?php
// Connect to the database
include 'connection/db_connection.php';
// Get the site class name
$department_name = isset($_GET['department']) ? $_GET['department'] : '';

// Adjusted SQL query to avoid combining dependents and generator counts
$sql = "SELECT
    s.site_id AS site_id,
    s.primary_id AS primary_id,
    s.secondary_id AS secondary_id,
    s.site_name AS site_name,
    s.site_auto_status AS site_auto_status,
    s.site_status AS site_status,
    s.tx_site_type AS tx_site_type,
    s.class AS class,
    (SELECT COUNT(*) FROM dependencies sd WHERE s.primary_id = sd.site_id) AS dependent_sites_count,
    (SELECT COUNT(DISTINCT g.generator_id) FROM generators g WHERE s.primary_id = g.site_id) AS number_of_generators_count
FROM 
    sites s
WHERE 
    s.department = '$department_name'";

$result = $conn->query($sql);

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
    }, 2000); // Delay of 2 seconds (2000 milliseconds)
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
                    <h2 class="text-center">List of Department <?php echo ($department_name != '' ? $department_name : 'Unknown'); ?> Sites</h2>
                    <hr>

                    <button onclick="window.location.href='export-sites.php?department=<?php echo $department_name; ?>&format=csv'" class="btn btn-primary">Download as CSV</button>
                    <button onclick="window.location.href='export-sites.php?department=<?php echo $department_name; ?>&format=excel'" class="btn btn-secondary">Download as Excel</button>
                    <hr>
                </div>

                <table class="table datatable table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">Edit</th>
                            <th scope="col">Site ID</th>
                            <th scope="col">Site Name</th>
                            <th scope="col">Dependents</th>
                            <th scope="col">Generators</th>
                            <th scope="col">Automation Status</th>
                            <th scope="col">Site Status</th>
                            <th scope="col">Priority</th>
                            <th scope="col">Class</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php while ($site = $result->fetch_assoc()) { ?>
                            <tr>
                                <td scope="row">
                                    <a href="edit-department-site.php?site_id=<?php echo $site['site_id']; ?>"
                                        title="Edit" class="text-success"><i class="bi bi-pencil-square"></i></a>
                                </td>
                                <td><?php echo $site['secondary_id']; ?></td>
                                <td><?php echo $site['site_name']; ?></td>
                                <td>
                                    <?php echo $site['dependent_sites_count']; ?>
                                    <?php if ($site['dependent_sites_count'] > 0): ?>
                                        <a href="view-dependent-site.php?site_id=<?php echo $site['primary_id']; ?>"
                                            title="View" class="text-"><i class="bi bi-eye"></i></a>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo $site['number_of_generators_count']; ?>
                                    <?php if ($site['number_of_generators_count'] > 0): ?>
                                        <a href="view-generator.php?site_id=<?php echo $site['primary_id']; ?>"
                                            title="View" class="text-"><i class="bi bi-eye"></i></a>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $site['site_auto_status']; ?></td>
                                <td><?php echo $site['site_status']; ?></td>
                                <td><?php echo $site['tx_site_type']; ?></td>
                                <td><?php echo $site['class']; ?></td>
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