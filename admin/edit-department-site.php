<?php
session_start();
if (!isset($_SESSION["username"])) {
    header('location: ../index.php');
}

include 'connection/db_connection.php';

include 'header_aside.php';



if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['site_id'])) {
    $site_id = $conn->real_escape_string($_GET['site_id']);
    $sql = "SELECT * FROM sites WHERE site_id='$site_id'";
    $result = $conn->query($sql);
    $site = $result->fetch_assoc();

    $site_status = $site['site_status'];
}

?>

<!--main content-->
<main id="main" class="main">
    <section class="section">
        <div class="row">
            <div class="col-lg-6">

            </div>
        </div>
        <h3 style="font-weight:bold; text-align:center;">Editting Site <?php echo $site['site_name']; ?></h3>
        <div class="card">
            <div class="card-body">
                <form method="POST" action="edit-site.php" enctype="multipart/form-data" class="row g-3" style="margin-top:1.5rem; margin-bottom:1.5rem;">

                    <div class="col-md-4">
                        <label for="inputName5" class="form-label">Primary ID</label>
                        <input type="text" class="form-control" id="inputName5" name="primary-id" value="<?php echo $site['primary_id']; ?>" required>
                    </div>

                    <div class="col-md-4">
                        <label for="inputName5" class="form-label">Secondary ID</label>
                        <input type="text" class="form-control" id="inputName5" name="secondary-id" value="<?php echo $site['secondary_id']; ?>" required>
                    </div>

                    <div class="col-md-4">
                        <label for="inputEmail5" class="form-label">Latitude</label>
                        <input type="type" class="form-control" id="inputEmail5" name="lat" value="<?php echo $site['latitude']; ?>">
                    </div>

                    <div class="col-md-4">
                        <label for="inputPassword5" class="form-label">Longitude</label>
                        <input type="text" class="form-control" id="inputPassword5" name="long" value="<?php echo $site['longitude']; ?>">
                    </div>

                    <div class="col-md-4">
                        <label for="inputAddress5" class="form-label">Site Name</label>
                        <input type="text" class="form-control" id="inputAddres5s" name="site-name" value="<?php echo $site['site_name']; ?>">
                    </div>

                    <div class="col-md-4">
                        <label for="inputAddress5" class="form-label">TX Site Type</label>
                        <input type="text" class="form-control" id="inputAddres5s" name="tx-site-type" value="<?php echo $site['tx_site_type']; ?>">
                    </div>

                    <div class="col-md-4">
                        <label for="inputState" class="form-label">Region</label>
                        <select id="inputState" class="form-select" name="region" value="<?php echo $site['city_region']; ?>">
                            <option selected><?php echo $site['city_region']; ?></option>
                            <option value="Central">Central</option>
                            <option value="Eastern">Eastern</option>
                            <option value="Western">Western</option>
                            <option value="Northern">Northern</option>
                            <option value="Southern">Southern</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="inputAddress5" class="form-label">Department</label>
                        <input type="text" class="form-control" id="inputAddres5s" name="department" value="<?php echo $site['department']; ?>">
                    </div>

                    <div class="col-md-4">
                        <label for="inputState" class="form-label">Site Automation Status</label>
                        <select id="inputState" class="form-select" name="site-auto-status" value="<?php echo $site['site_auto_status']; ?>">
                            <option selected><?php echo $site['site_auto_status']; ?></option>
                            <option value="MANUAL">Manual</option>
                            <option value="AUTOMATIC">Automatic</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="inputState" class="form-label">Site Status</label>
                        <select id="inputState" class="form-select" name="site-status" value="<?php echo $site['site_status']; ?>">
                            <option selected><?php echo $site['site_status']; ?></option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="inputState" class="form-label">Class</label>
                        <select id="inputState" class="form-select" name="class" value="<?php echo $site['class']; ?>">
                            <option selected><?php echo $site['class']; ?></option>
                            <option>A</option>
                            <option>B</option>
                            <option>C</option>
                            <option>D</option>
                            <option>E</option>
                            <option>Others</option>
                        </select>
                    </div>

                    <?php if ($site_status == 'Active'): ?>

                        <div class="col-md-4">
                            <label for="inputState" class="form-label">Current Status</label>
                            <select id="inputState" class="form-select" name="current-status" value="<?php echo $site['active_site_status']; ?>">
                                <option selected><?php echo $site['active_site_status']; ?></option>
                                <option value="Up">Up</option>
                                <option value="Down">Down</option>
                            </select>
                        </div>
                    <?php endif; ?>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form><!-- End Multi Columns Form -->

            </div>
        </div>

        </div>

        </div>
        </div>

        </div>
        </div>
    </section>

</main><!-- End #main -->

<?php include 'footer.php' ?>

<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Validate and sanitize the form input
    $siteId = $conn->real_escape_string($_POST['site-id']);
    $primaryId = $conn->real_escape_string($_POST['primary-id']);
    $secondaryId = $conn->real_escape_string($_POST['secondary-id']);

    $latitude = $conn->real_escape_string($_POST['lat']);
    $longitude = $conn->real_escape_string($_POST['long']);

    $sitename = $conn->real_escape_string($_POST['site-name']);
    $txSiteType = $conn->real_escape_string($_POST['tx-site-type']);
    $region = $conn->real_escape_string($_POST['region']);

    $department = $conn->real_escape_string($_POST['department']);
    $siteAutoStatus = $conn->real_escape_string($_POST['site-auto-status']);

    $siteStatus = mysqli_real_escape_string($conn, $_POST['site-status']);
    $class = mysqli_real_escape_string($conn, $_POST['class']);
    $current_status = mysqli_real_escape_string($conn, $_POST['current-status']);


    $query = "UPDATE sites SET primary_id='$primaryId', secondary_id='$secondaryId', latitude='$latitude', longitude='$longitude', 
  site_name='$sitename', tx_site_type='$txSiteType', city_region='$region', department='$department', site_auto_status='$siteAutoStatus',
    site_status='$siteStatus', class='$class', active_site_status = '$current_status' WHERE site_id='$siteId'";

    if ($conn->query($query) === TRUE) {
        session_start();
        // $_SESSION['message'] = "Loan Updated successfully";
        echo "<script>window.location.href = 'department-details.php?department=" . urlencode($department) . "';</script>";
        exit;
    } else {
        echo "Error: " . $query . "<br>" . $conn->error;
    }
}

?>