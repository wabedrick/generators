<?php
include 'connection/db_connection.php';
session_start();
if (!isset($_SESSION["username"])) {
  header('location: ../index.php');
}

include 'header_aside.php';
?>

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
      <!-- <div class="col-md-6">
        <label for="inputState" class="form-label">Site ID</label>
        <select id="inputState" class="form-select" name="type">
          <option selected>Choose...</option>
          <option value="Central">AC</option>
          <option value="Eastern">DC</option>
        </select>
      </div> -->
      <div class="col-md-6">
        <label for="inputName5" class="form-label">Site ID</label>
        <input type="text" class="form-control" id="inputName5" name="site_id">
      </div>

      <div class="col-md-6">
        <label for="inputName5" class="form-label">Hybrid RBS Battery Type</label>
        <input type="text" class="form-control" id="inputName5" name="hybrid_rbs_battery_type">
      </div>

      <div class="col-md-6">
        <label for="inputEmail5" class="form-label">Number of Hybrid RBS Batteries</label>
        <input type="type" class="form-control" id="inputEmail5" name="No_of_hybrid_rbs_batteries">
      </div>

      <div class="col-md-6">
        <label for="inputPassword5" class="form-label">Battery Capacity</label>
        <input type="text" class="form-control" id="inputPassword5" name="battery_capacity">
      </div>

      <div class="col-md-4">
        <label for="inputAddress5" class="form-label">Power Type</label>
        <input type="text" class="form-control" id="inputAddres5s" name="power_type">
      </div>

      <div class="col-md-4">
        <label for="inputAddress5" class="form-label">GenSet Status</label>
        <input type="text" class="form-control" id="inputAddres5s" name="status">
      </div>

      <div class="col-md-4">
        <label for="inputAddress5" class="form-label">Installation Date</label>
        <input type="text" class="form-control" id="inputAddres5s" name="installation_date">
      </div>

      <div class="col-md-4">
        <label for="inputAddress5" class="form-label">Percentage Operation Time</label>
        <input type="text" class="form-control" id="inputAddres5s" name="percent_op_time">
      </div>

      <div class="col-md-4">
        <label for="inputAddress5" class="form-label">Brand</label>
        <input type="text" class="form-control" id="inputAddres5s" name="brand">
      </div>

      <div class="col-md-4">
        <label for="inputAddress5" class="form-label">Capacity</label>
        <input type="text" class="form-control" id="inputAddres5s" name="capacity">
      </div>

      <div class="col-md-4">
        <label for="inputAddress5" class="form-label">Engine</label>
        <input type="text" class="form-control" id="inputAddres5s" name="engine">
      </div>

      <div class="col-md-4">
        <label for="inputAddress5" class="form-label">Maintenance Scope</label>
        <input type="text" class="form-control" id="inputAddres5s" name="maitenance_scope">
      </div>

      <div class="col-md-4">
        <label for="inputAddress5" class="form-label">Number of AC</label>
        <input type="text" class="form-control" id="inputAddres5s" name="No_of_acc">
      </div>

      <div class="col-md-4">
        <label for="inputAddress5" class="form-label">Generator Mode</label>
        <input type="text" class="form-control" id="inputAddres5s" name="gen_mode">
      </div>

      <div class="col-md-4">
        <label for="inputAddress5" class="form-label">Initial Run Hours</label>
        <input type="number" class="form-control" id="inputAddres5s" name="run_hours">
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
  $site_id = $conn->real_escape_string($_POST['site_id']);
  $hybrid_rbs_battery_type = $conn->real_escape_string($_POST['hybrid_rbs_battery_type']);

  $No_of_hybrid_rbs_batteries = $conn->real_escape_string($_POST['No_of_hybrid_rbs_batteries']);
  $battery_capacity = $conn->real_escape_string($_POST['battery_capacity']);

  $power_type = $conn->real_escape_string($_POST['power_type']);
  $status = $conn->real_escape_string($_POST['status']);
  $installation_date = $conn->real_escape_string($_POST['installation_date']);

  $percent_op_time = $conn->real_escape_string($_POST['percent_op_time']);
  $brand = $conn->real_escape_string($_POST['brand']);
  $capacity = mysqli_real_escape_string($conn, $_POST['capacity']);

  $engine = $conn->real_escape_string($_POST['engine']);
  $maintenance_scope = $conn->real_escape_string($_POST['maintenance_scope']);
  $run_hours = $conn->real_escape_string($_POST['run_hours']);


  $sql =  "INSERT INTO generators
  (site_id, hybrid_rbs_battery_type, No_of_hybrid_rbs_batteries, battery_capacity, power_type, status, installation_date,
   percent_op_time, brand, capacity, engine, maintenance_scope, actual_run_hours) 
  VALUES('$site_id','$hybrid_rbs_battery_type','$No_of_hybrid_rbs_batteries','$battery_capacity','$power_type','$status','$installation_date',
  '$percent_op_time','$brand','$capacity','$engine', '$maintenance_scope', '$run_hours')";

  if ($conn->query($sql) === TRUE) {
    $_SESSION['message'] = "Generator added successfully";
    echo "<script>window.location.href = 'view-all-generators.php';</script>";
    exit;
  } else {
    echo "Error: " . $conn->error;
  }
}
?>