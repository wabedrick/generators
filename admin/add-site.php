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
        <h5 class="card-title">Add New Site</h5>

        <form method="POST" action="add-site.php" enctype="multipart/form-data" class="row g-3 needs-validation" novalidate>
          <!-- Primary Site Information -->
          <div class="col-md-6">
            <label for="primary_id" class="form-label">Primary ID *</label>
            <input type="text" class="form-control" id="primary_id" name="primary_id" required>
            <div class="invalid-feedback">
              Please provide a Primary ID.
            </div>
          </div>

          <div class="col-md-6">
            <label for="secondary_id" class="form-label">Secondary ID *</label>
            <input type="text" class="form-control" id="secondary_id" name="secondary_id" required>
            <div class="invalid-feedback">
              Please provide a Secondary ID.
            </div>
          </div>

          <div class="col-md-6">
            <label for="rf_id" class="form-label">RF ID *</label>
            <input type="text" class="form-control" id="rf_id" name="rf_id" required>
            <div class="invalid-feedback">
              Please provide an RF ID.
            </div>
          </div>

          <div class="col-md-6">
            <label for="site_name" class="form-label">Site Name *</label>
            <input type="text" class="form-control" id="site_name" name="site_name" required>
            <div class="invalid-feedback">
              Please provide a Site Name.
            </div>
          </div>

          <!-- Location Information -->
          <div class="col-md-4">
            <label for="latitude" class="form-label">Latitude *</label>
            <input type="number" class="form-control" id="latitude" name="latitude" step="0.00000001" required>
            <div class="invalid-feedback">
              Please provide a valid latitude.
            </div>
          </div>

          <div class="col-md-4">
            <label for="longitude" class="form-label">Longitude *</label>
            <input type="number" class="form-control" id="longitude" name="longitude" step="0.00000001" required>
            <div class="invalid-feedback">
              Please provide a valid longitude.
            </div>
          </div>

          <div class="col-md-4">
            <label for="city_region" class="form-label">City/Region *</label>
            <input type="text" class="form-control" id="city_region" name="city_region" required>
            <div class="invalid-feedback">
              Please provide a City/Region.
            </div>
          </div>

          <!-- Site Classification -->
          <div class="col-md-4">
            <label for="tx_site_type" class="form-label">TX Site Type *</label>
            <select class="form-select" id="tx_site_type" name="tx_site_type" required>
              <option value="" selected disabled>Choose...</option>
              <option value="Regular">Regular</option>
              <option value="Hub">Hub</option>
              <option value="Special">Special</option>
            </select>
            <div class="invalid-feedback">
              Please select a TX Site Type.
            </div>
          </div>

          <div class="col-md-4">
            <label for="department" class="form-label">Department *</label>
            <select class="form-select" id="department" name="department" required>
              <option value="" selected disabled>Choose...</option>
              <option value="Nord">Nord</option>
              <option value="Sud">Sud</option>
              <option value="Ouest">Ouest</option>
              <option value="Est">Est</option>
              <option value="Centre">Centre</option>
            </select>
            <div class="invalid-feedback">
              Please select a Department.
            </div>
          </div>

          <div class="col-md-4">
            <label for="class" class="form-label">Class *</label>
            <select class="form-select" id="class" name="class" required>
              <option value="" selected disabled>Choose...</option>
              <option value="A">A</option>
              <option value="B">B</option>
              <option value="C">C</option>
              <option value="D">D</option>
              <option value="E">E</option>
            </select>
            <div class="invalid-feedback">
              Please select a Class.
            </div>
          </div>

          <div class="col-md-6">
            <label for="type_of_site" class="form-label">Type of Site *</label>
            <input type="text" class="form-control" id="type_of_site" name="type_of_site" required>
            <div class="invalid-feedback">
              Please provide a Type of Site.
            </div>
          </div>

          <div class="col-md-6">
            <label for="installation_date" class="form-label">Installation Date *</label>
            <input type="date" class="form-control" id="installation_date" name="installation_date" required>
            <div class="invalid-feedback">
              Please provide an Installation Date.
            </div>
          </div>

          <!-- Site Status Information -->
          <div class="col-md-4">
            <label for="site_auto_status" class="form-label">Site Auto Status</label>
            <input type="text" class="form-control" id="site_auto_status" name="site_auto_status">
          </div>

          <div class="col-md-4">
            <label for="site_status" class="form-label">Site Status *</label>
            <select class="form-select" id="site_status" name="site_status" required>
              <option value="" selected disabled>Choose...</option>
              <option value="Active">Active</option>
              <option value="Inactive">Inactive</option>
              <option value="Maintenance">Maintenance</option>
            </select>
            <div class="invalid-feedback">
              Please select a Site Status.
            </div>
          </div>

          <div class="col-md-4">
            <label for="active_site_status" class="form-label">Active Site Status</label>
            <input type="text" class="form-control" id="active_site_status" name="active_site_status">
          </div>

          <!-- Downtime Information -->
          <div class="col-md-4">
            <label for="downtime_date" class="form-label">Downtime Date</label>
            <input type="date" class="form-control" id="downtime_date" name="downtime_date">
          </div>

          <div class="col-md-4">
            <label for="downtime_duration" class="form-label">Downtime Duration (hours)</label>
            <input type="number" class="form-control" id="downtime_duration" name="downtime_duration" min="0">
          </div>

          <div class="col-md-4">
            <label for="created_at" class="form-label">Created At</label>
            <input type="datetime-local" class="form-control" id="created_at" name="created_at">
          </div>

          <!-- Additional Information -->
          <div class="col-12">
            <label for="problem_summary" class="form-label">Problem Summary</label>
            <textarea class="form-control" id="problem_summary" name="problem_summary" rows="3"></textarea>
          </div>

          <div class="col-12">
            <label for="comment" class="form-label">Comments</label>
            <textarea class="form-control" id="comment" name="comment" rows="3"></textarea>
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
  $required_fields = [
    'primary_id',
    'secondary_id',
    'rf_id',
    'site_name',
    'latitude',
    'longitude',
    'city_region',
    'tx_site_type',
    'department',
    'class',
    'type_of_site',
    'installation_date',
    'site_status'
  ];

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
    $primary_id = $conn->real_escape_string(trim($_POST['primary_id']));
    $secondary_id = $conn->real_escape_string(trim($_POST['secondary_id']));
    $rf_id = $conn->real_escape_string(trim($_POST['rf_id']));
    $site_name = $conn->real_escape_string(trim($_POST['site_name']));
    $tx_site_type = $conn->real_escape_string(trim($_POST['tx_site_type']));
    $department = $conn->real_escape_string(trim($_POST['department']));
    $city_region = $conn->real_escape_string(trim($_POST['city_region']));
    $latitude = (float)$_POST['latitude'];
    $longitude = (float)$_POST['longitude'];
    $class = $conn->real_escape_string(trim($_POST['class']));
    $type_of_site = $conn->real_escape_string(trim($_POST['type_of_site']));
    $installation_date = $conn->real_escape_string(trim($_POST['installation_date']));

    // Site status fields
    $site_auto_status = $conn->real_escape_string(trim($_POST['site_auto_status']));
    $site_status = $conn->real_escape_string(trim($_POST['site_status']));
    $active_site_status = $conn->real_escape_string(trim($_POST['active_site_status']));

    // Downtime fields
    $downtime_date = !empty($_POST['downtime_date']) ? $conn->real_escape_string(trim($_POST['downtime_date'])) : null;
    $downtime_duration = isset($_POST['downtime_duration']) ? (int)$_POST['downtime_duration'] : null;
    $created_at = !empty($_POST['created_at']) ? $conn->real_escape_string(trim($_POST['created_at'])) : date('Y-m-d H:i:s');

    // Additional fields
    $problem_summary = $conn->real_escape_string(trim($_POST['problem_summary']));
    $comment = $conn->real_escape_string(trim($_POST['comment']));

    // Check if site already exists
    $checkQuery = "SELECT * FROM sites WHERE primary_id = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("s", $primary_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      $_SESSION['message'] = "Error: Site with this Primary ID already exists.";
      $_SESSION['message_type'] = 'danger';
      $error = true;
    }
    $stmt->close();

    if (!$error) {
      // Prepare SQL statement for sites table (assuming both tables are merged or related)
      $sql = "INSERT INTO sites (
        primary_id, secondary_id, rf_id, site_name, tx_site_type, department, 
        city_region, latitude, longitude, class, type_of_site, installation_date,
        site_auto_status, site_status, active_site_status, downtime_date, 
        downtime_duration, created_at, problem_summary, comment
      ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

      $stmt = $conn->prepare($sql);
      if ($stmt) {
        $stmt->bind_param(
          "ssssssddssssssssiss",
          $primary_id,
          $secondary_id,
          $rf_id,
          $site_name,
          $tx_site_type,
          $department,
          $city_region,
          $latitude,
          $longitude,
          $class,
          $type_of_site,
          $installation_date,
          $site_auto_status,
          $site_status,
          $active_site_status,
          $downtime_date,
          $downtime_duration,
          $created_at,
          $problem_summary,
          $comment
        );

        if ($stmt->execute()) {
          $_SESSION['message'] = "Site added successfully!";
          $_SESSION['message_type'] = 'success';
          header('Location: view-sites.php');
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
  }

  // If there was an error, stay on the same page to show the message
  if ($error) {
    echo "<script>window.location.href = 'add-site.php';</script>";
    exit;
  }
}
?>