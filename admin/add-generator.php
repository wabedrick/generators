<?php
include 'connection/db_connection.php';
session_start();
if (!isset($_SESSION["username"])) {
  header('location: ../index.php');
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
          <option selected>Choose...</option>
          <option value="Central">AC</option>
          <option value="Eastern">DC</option>
        </select>
      </div>

      <div class="col-md-6">
        <label for="inputName5" class="form-label">Make</label>
        <input type="text" class="form-control" id="inputName5" name="make">
      </div>

      <div class="col-md-6">
        <label for="inputEmail5" class="form-label">Capacity</label>
        <input type="type" class="form-control" id="inputEmail5" name="capacity">
      </div>

      <div class="col-md-6">
        <label for="inputPassword5" class="form-label">SN Engine</label>
        <input type="text" class="form-control" id="inputPassword5" name="sn-engine">
      </div>

      <div class="col-md-4">
        <label for="inputAddress5" class="form-label">SN Alternator</label>
        <input type="text" class="form-control" id="inputAddres5s" name="sn-alternator">
      </div>

      <!-- <div class="col-md-4">
        <label for="inputAddress5" class="form-label">City</label>
        <input type="text" class="form-control" id="inputAddres5s" name="city">
      </div> -->

      <div class="col-md-4">
        <label for="inputState" class="form-label">Phase</label>
        <select id="inputState" class="form-select" name="phase">
          <option selected>Choose...</option>
          <option value="Central">Single</option>
          <option value="Eastern">Double</option>
        </select>
      </div>

      <div class="col-md-4">
        <label for="inputState" class="form-label">Voltage</label>
        <select id="inputState" class="form-select" name="voltage">
          <option selected>Choose...</option>
          <option value="Central">220</option>
          <option value="Eastern">-48</option>
        </select>
      </div>

      <div class="col-md-4">
        <label for="inputAddress5" class="form-label">ICCID Sim Card</label>
        <input type="text" class="form-control" id="inputAddres5s" name="iccid-simcard">
      </div>

      <div class="col-md-4">
        <label for="inputAddress5" class="form-label">MSISDN Sim Card</label>
        <input type="text" class="form-control" id="inputAddres5s" name="msisdn-sim-card">
      </div>

      <div class="col-md-4">
        <label for="inputAddress5" class="form-label">Gateway USBID</label>
        <input type="text" class="form-control" id="inputAddres5s" name="gatway-usbid">
      </div>


      <!-- <div class="col-md-6">
        <label for="inputAddress5" class="form-label">Installation Date</label>
        <input type="date" class="form-control" id="inputAddres5s" name="installation-date">
      </div> -->

      <div class="col-md-6">
        <label for="group" class="form-label">Site</label>
        <select name="site" id="group" class="form-control form-control-user" required>
          <option value="">Select Site</option>

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

  // $installationDate = mysqli_real_escape_string($conn, $_POST['installation-date']);


  // $checkQuery = "SELECT * FROM sites WHERE site_id = '$siteId'";
  // $result = $conn->query($checkQuery);
  // if ($result->num_rows > 0) {
  //   $_SESSION['message'] = "Site ID already exists.";
  //   echo "<script>window.location.href = 'add-site.php';</script>";
  //   exit;
  // }

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