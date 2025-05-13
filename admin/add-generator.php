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

  <?php if (isset($_SESSION['message'])): ?>
    <div class="container" id="message-container">
      <div class="row">
        <div class="col-md-12">
          <div class="alert alert-<?php echo $_SESSION['message_type']; ?> d-flex justify-content-between align-items-center">
            <?php echo $_SESSION['message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        </div>
      </div>
    </div>
  <?php
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
  endif; ?>

  <section class="section">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Add New Generator</h5>

        <form method="POST" action="add-generator.php" enctype="multipart/form-data" class="row g-3 needs-validation" novalidate>
          <div class="col-md-6">
            <label for="site_id" class="form-label">Site ID *</label>
            <input type="text" class="form-control" id="site_id" name="site_id" required>
            <div class="invalid-feedback">
              Please provide a Site ID.
            </div>
          </div>

          <div class="col-md-6">
            <label for="hybrid_rbs_battery_type" class="form-label">Hybrid RBS Battery Type</label>
            <input type="text" class="form-control" id="hybrid_rbs_battery_type" name="hybrid_rbs_battery_type">
          </div>

          <div class="col-md-6">
            <label for="No_of_hybrid_rbs_batteries" class="form-label">Number of Hybrid RBS Batteries</label>
            <input type="number" class="form-control" id="No_of_hybrid_rbs_batteries" name="No_of_hybrid_rbs_batteries" min="0">
          </div>

          <div class="col-md-6">
            <label for="battery_capacity" class="form-label">Battery Capacity</label>
            <input type="text" class="form-control" id="battery_capacity" name="battery_capacity">
          </div>

          <div class="col-md-4">
            <label for="power_type" class="form-label">Power Type *</label>
            <select class="form-select" id="power_type" name="power_type" required>
              <option value="" selected disabled>Choose...</option>
              <option value="AC">AC</option>
              <option value="DC">DC</option>
              <option value="Hybrid">Hybrid</option>
            </select>
            <div class="invalid-feedback">
              Please select a power type.
            </div>
          </div>

          <div class="col-md-4">
            <label for="status" class="form-label">GenSet Status *</label>
            <select class="form-select" id="status" name="status" required>
              <option value="" selected disabled>Choose...</option>
              <option value="Operational">Operational</option>
              <option value="Non-Operational">Non-Operational</option>
              <option value="Maintenance">Maintenance</option>
            </select>
            <div class="invalid-feedback">
              Please select a status.
            </div>
          </div>

          <div class="col-md-4">
            <label for="installation_date" class="form-label">Installation Date *</label>
            <input type="date" class="form-control" id="installation_date" name="installation_date" required>
            <div class="invalid-feedback">
              Please provide an installation date.
            </div>
          </div>

          <div class="col-md-4">
            <label for="percent_op_time" class="form-label">Percentage Operation Time</label>
            <input type="number" class="form-control" id="percent_op_time" name="percent_op_time" min="0" max="100">
            <div class="invalid-feedback">
              Must be between 0-100.
            </div>
          </div>

          <div class="col-md-4">
            <label for="brand" class="form-label">Brand</label>
            <input type="text" class="form-control" id="brand" name="brand">
          </div>

          <div class="col-md-4">
            <label for="capacity" class="form-label">Capacity</label>
            <input type="text" class="form-control" id="capacity" name="capacity">
          </div>

          <div class="col-md-4">
            <label for="engine" class="form-label">Engine</label>
            <input type="text" class="form-control" id="engine" name="engine">
          </div>

          <div class="col-md-4">
            <label for="maintenance_scope" class="form-label">Maintenance Scope</label>
            <input type="text" class="form-control" id="maintenance_scope" name="maintenance_scope">
          </div>

          <div class="col-md-4">
            <label for="No_of_ac" class="form-label">Number of AC</label>
            <input type="number" class="form-control" id="No_of_ac" name="No_of_ac" min="0">
          </div>

          <div class="col-md-4">
            <label for="gen_mode" class="form-label">Generator Mode</label>
            <select class="form-select" id="gen_mode" name="gen_mode">
              <option value="" selected disabled>Choose...</option>
              <option value="Auto">Auto</option>
              <option value="Manual">Manual</option>
            </select>
          </div>

          <div class="col-md-4">
            <label for="run_hours" class="form-label">Initial Run Hours</label>
            <input type="number" class="form-control" id="run_hours" name="run_hours" min="0" step="0.01">
          </div>

          <div class="text-center">
            <button type="submit" class="btn btn-primary">Submit</button>
            <button type="reset" class="btn btn-secondary">Reset</button>
          </div>
        </form><!-- End Multi Columns Form -->

      </div>
    </div>
  </section>

</main><!-- End #main -->

<!-- ======= Footer ======= -->
<?php include 'footer.php'; ?>

<!-- Form Validation Script -->
<script>
  // Example starter JavaScript for disabling form submissions if there are invalid fields
  (() => {
    'use strict'

    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    const forms = document.querySelectorAll('.needs-validation')

    // Loop over them and prevent submission
    Array.from(forms).forEach(form => {
      form.addEventListener('submit', event => {
        if (!form.checkValidity()) {
          event.preventDefault()
          event.stopPropagation()
        }

        form.classList.add('was-validated')
      }, false)
    })
  })()
</script>

<?php
// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Initialize error flag
  $error = false;

  // Validate required fields
  $required_fields = ['site_id', 'power_type', 'status', 'installation_date'];
  foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
      $_SESSION['message'] = "Error: $field is required.";
      $_SESSION['message_type'] = 'danger';
      $error = true;
      break;
    }
  }

  if (!$error) {
    // Prepare data for insertion
    $site_id = $conn->real_escape_string(trim($_POST['site_id']));
    $hybrid_rbs_battery_type = $conn->real_escape_string(trim($_POST['hybrid_rbs_battery_type']));
    $No_of_hybrid_rbs_batteries = isset($_POST['No_of_hybrid_rbs_batteries']) ? (int)$_POST['No_of_hybrid_rbs_batteries'] : 0;
    $battery_capacity = $conn->real_escape_string(trim($_POST['battery_capacity']));
    $power_type = $conn->real_escape_string(trim($_POST['power_type']));
    $status = $conn->real_escape_string(trim($_POST['status']));
    $installation_date = $conn->real_escape_string(trim($_POST['installation_date']));
    $percent_op_time = isset($_POST['percent_op_time']) ? (int)$_POST['percent_op_time'] : null;
    $brand = $conn->real_escape_string(trim($_POST['brand']));
    $capacity = $conn->real_escape_string(trim($_POST['capacity']));
    $engine = $conn->real_escape_string(trim($_POST['engine']));
    $maintenance_scope = $conn->real_escape_string(trim($_POST['maintenance_scope']));
    $No_of_ac = isset($_POST['No_of_ac']) ? (int)$_POST['No_of_ac'] : 0;
    $gen_mode = $conn->real_escape_string(trim($_POST['gen_mode']));
    $run_hours = isset($_POST['run_hours']) ? (float)$_POST['run_hours'] : 0.0;

    // Prepare SQL statement
    $sql = "INSERT INTO generators (
      site_id, hybrid_rbs_battery_type, No_of_hybrid_rbs_batteries, battery_capacity, 
      power_type, status, installation_date, percent_op_time, brand, capacity, 
      engine, maintenance_scope, number_of_ac, gen_mode, actual_run_hours
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if ($stmt) {
      $stmt->bind_param(
        "ssisssssisssisd",
        $site_id,
        $hybrid_rbs_battery_type,
        $No_of_hybrid_rbs_batteries,
        $battery_capacity,
        $power_type,
        $status,
        $installation_date,
        $percent_op_time,
        $brand,
        $capacity,
        $engine,
        $maintenance_scope,
        $No_of_ac,
        $gen_mode,
        $run_hours
      );

      if ($stmt->execute()) {
        $_SESSION['message'] = "Generator added successfully!";
        $_SESSION['message_type'] = 'success';
        header('Location: view-all-generators.php');
        exit;
      } else {
        $_SESSION['message'] = "Error: " . $stmt->error;
        $_SESSION['message_type'] = 'danger';
      }
      $stmt->close();
    } else {
      $_SESSION['message'] = "Error preparing statement: " . $conn->error;
      $_SESSION['message_type'] = 'danger';
    }
  }

  // If there was an error, stay on the same page to show the message
  if ($error) {
    echo "<script>window.location.href = 'add-generator.php';</script>";
    exit;
  }
}
?>