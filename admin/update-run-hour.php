<?php
include 'connection/db_connection.php';
session_start();
if (!isset($_SESSION["username"])) {
    header('location: ../index.php');
}

// Initialize the $generator variable
// $generator = [];

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $generator_id = $conn->real_escape_string($_GET['id']);
    $sql = "SELECT * FROM sites INNER JOIN generators ON sites.id=generators.sites_id WHERE generators.id='$generator_id'";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $generator = $result->fetch_assoc();
    }
}
?>

<?php include 'header_aside.php'; ?>

<!--main content-->
<main id="main" class="main">

    <?php if (isset($_SESSION['message'])) { ?>
        <div class="container" id="message-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-danger d-flex justify-content-between align-items-center">
                        <?php echo $_SESSION['message']; ?>
                        <!-- add button for removing the alert message -->
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        </div>
    <?php }
    unset($_SESSION['message']); ?>

    <section class="section">

        <!--form for adding a new Site to the Database-->
        <form method="POST" action="add-generator.php" enctype="multipart/form-data" class="row g-3">
            <div class="col-md-6">
                <label for="inputState" class="form-label">Type</label>
                <select id="inputState" class="form-select" name="type">
                    <option value="<?php echo isset($generator['Type']) ? $generator['Type'] : ''; ?>" selected>
                        <?php echo isset($generator['Type']) ? $generator['Type'] : ''; ?>
                    </option>
                    <option value="Central">AC</option>
                    <option value="Eastern">DC</option>
                </select>
            </div>

            <div class="col-md-6">
                <label for="inputName5" class="form-label">Make</label>
                <input type="text" class="form-control" id="inputName5" value="<?php echo isset($generator['Make']) ? $generator['Make'] : ''; ?>" name="make">
            </div>

            <div class="col-md-6">
                <label for="inputEmail5" class="form-label">Capacity</label>
                <input type="text" class="form-control" id="inputEmail5" value="<?php echo isset($generator['Capacity']) ? $generator['Capacity'] : ''; ?>" name="capacity">
            </div>

            <div class="col-md-6">
                <label for="inputPassword5" class="form-label">SN Engine</label>
                <input type="text" class="form-control" id="inputPassword5" value="<?php echo isset($generator['SN_engine']) ? $generator['SN_engine'] : ''; ?>" name="sn-engine">
            </div>

            <div class="col-md-6">
                <label for="inputAddress5" class="form-label">Date of Update</label>
                <input type="date" class="form-control" id="inputAddres5s" name="installation-date">
            </div>

            <div class="col-md-6">
                <label for="group" class="form-label">Site</label>
                <select name="site" id="group" class="form-control form-control-user" required>
                    <option value=""><?php echo isset($generator['site_name']) ? $generator['site_name'] : ''; ?></option>
                    <?php
                    $siteQuery = "SELECT * FROM sites";
                    $sites = $conn->query($siteQuery);
                    while ($site = $sites->fetch_assoc()) {
                    ?>
                        <option value="<?php echo $site['id'] ?>"> <?php echo $site['site_name'] ?> </option>
                    <?php
                    }
                    ?>
                </select>
            </div>

            <div class="col-md">
                <label for="inputAddress5" class="form-label">Update Actual Run Hour</label>
                <input type="number" class="form-control" id="inputAddres5s" name="actual-run-hour">
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form><!-- End Multi Columns Form -->

    </section>

</main><!-- End #main -->

<!-- ======= Footer ======= -->
<?php include 'footer.php'; ?>


<?php
// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize the form input
    $type = $conn->real_escape_string($_POST['type']);
    $make = $conn->real_escape_string($_POST['make']);
    $capacity = $conn->real_escape_string($_POST['capacity']);
    $sn_engine = $conn->real_escape_string($_POST['sn-engine']);
    $sn_alternator = $conn->real_escape_string($_POST['sn-alternator']);
    $phase = $conn->real_escape_string($_POST['phase']);
    $voltage = $conn->real_escape_string($_POST['voltage']);
    $iccid_sim_card = $conn->real_escape_string($_POST['iccid-simcard']);
    $msisdn_sim_card = $conn->real_escape_string($_POST['msisdn-sim-card']);
    $gateway_usbid = mysqli_real_escape_string($conn, $_POST['getway-usbid']);
    $site = $conn->real_escape_string($_POST['site']);

    $sql =  "INSERT INTO generators
  (Type,Make, Capacity, SN_engine,SN_alternator, Phase, Voltage, ICCID_sim_card, MSISDN_sim_card, Getway_USBID, site_id) 
  VALUES('$type','$make','$capacity','$sn_engine','$sn_alternator','$phase','$voltage','$iccid_sim_card',
  '$msisdn_sim_card','$gateway_usbid','$site')";

    if ($conn->query($sql) === TRUE) {
        $_SESSION['message'] = "Generator added successfully";
        echo "<script>window.location.href = 'view-generators.php';</script>";
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}
?>