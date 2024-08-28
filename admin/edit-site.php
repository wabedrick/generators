<?php
session_start();
if (!isset($_SESSION["username"])) {
  header('location: ../index.php');
}

include 'connection/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['site_id'])) {
  $site_id = $conn->real_escape_string($_GET['site_id']);
  // $borrower = $conn->real_escape_string($_GET['borrower']);
  $sql = "SELECT * FROM sites WHERE site_id='$site_id'";
  $result = $conn->query($sql);
  $site = $result->fetch_assoc();
}

?>

<?php include 'header_aside.php' ?>

<!--main content-->
<main id="main" class="main">

  <div class="pagetitle">
    <!-- <h1>Add Loan</h1> -->
    <nav>

  </div><!-- End Page Title -->
  <section class="section">
    <div class="row">
      <div class="col-lg-6">

      </div>
    </div>

    <div class="card">
      <div class="card-body">

        <form method="POST" action="edit-site.php" enctype="multipart/form-data" class="row g-3">
          <div class="col-md-6">
            <label for="inputName5" class="form-label">Site ID</label>
            <input type="text" class="form-control" id="inputName5" name="site-id" value="<?php echo $site['site_id']; ?>" required>
          </div>

          <div class="col-md-6">
            <label for="inputName5" class="form-label">Other ID</label>
            <input type="text" class="form-control" id="inputName5" name="other-id" value="<?php echo $site['other_id']; ?>" required>
          </div>

          <div class="col-md-6">
            <label for="inputEmail5" class="form-label">Latitude</label>
            <input type="type" class="form-control" id="inputEmail5" name="lat" value="<?php echo $site['latitude_code']; ?>">
          </div>

          <div class="col-md-6">
            <label for="inputPassword5" class="form-label">Longitude</label>
            <input type="text" class="form-control" id="inputPassword5" name="long" value="<?php echo $site['longitude_code']; ?>">
          </div>

          <div class="col-md-4">
            <label for="inputAddress5" class="form-label">Site Name</label>
            <input type="text" class="form-control" id="inputAddres5s" name="site-name" value="<?php echo $site['site_name']; ?>">
          </div>

          <div class="col-md-4">
            <label for="inputAddress5" class="form-label">City</label>
            <input type="text" class="form-control" id="inputAddres5s" name="city" value="<?php echo $site['city']; ?>">
          </div>

          <div class="col-md-4">
            <label for="inputState" class="form-label">Region</label>
            <select id="inputState" class="form-select" name="region" value="<?php echo $site['region']; ?>">
              <option selected><?php echo $site['region']; ?></option>
              <option value="Central">Central</option>
              <option value="Eastern">Eastern</option>
              <option value="Western">Western</option>
              <option value="Northern">Northern</option>
              <option value="Southern">Southern</option>
            </select>
          </div>

          <div class="col-md-3">
            <label for="inputAddress5" class="form-label">Dependents</label>
            <input type="number" class="form-control" id="inputAddres5s" name="dependents" value="<?php echo $site['dependents']; ?>">
          </div>

          <div class="col-md-3">
            <label for="inputAddress5" class="form-label">Number Of Generators</label>
            <input type="number" class="form-control" id="inputAddres5s" name="number_of_generators" value="<?php echo $site['number_of_generators']; ?>">
          </div>

          <div class="col-md-3">
            <label for="inputState" class="form-label">Category</label>
            <select id="inputState" class="form-select" name="category" value="<?php echo $site['category']; ?>">
              <option selected><?php echo $site['category']; ?></option>
              <option value="High">High</option>
              <option value="Moderate">Moderate</option>
              <option value="Low">Low</option>
              <option value="MM">MM</option>
              <option value="Hub>5">Hub>5</option>
              <option value="Hub>5+MM">Hub>5+MM</option>
              <option value="Hub<5">Hub< 5 </option>
            </select>
          </div>

          <div class="col-md-3">
            <label for="inputState" class="form-label">Class</label>
            <select id="inputState" class="form-select" name="class" value="<?php echo $site['class']; ?>">
              <option selected><?php echo $site['class']; ?></option>
              <option>A</option>
              <option>B</option>
              <option>C</option>
              <option>D</option>
            </select>
          </div>

          <div class="col-md-6">
            <label for="inputAddress5" class="form-label">Installation Date</label>
            <input type="date" class="form-control" id="inputAddres5s" name="installation-date" value="<?php echo $site['starting_date']; ?>">
          </div>

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
  $otherId = $conn->real_escape_string($_POST['other-id']);

  $latitude = $conn->real_escape_string($_POST['lat']);
  $longitude = $conn->real_escape_string($_POST['long']);

  $sitename = $conn->real_escape_string($_POST['site-name']);
  $city = $conn->real_escape_string($_POST['city']);
  $region = $conn->real_escape_string($_POST['region']);

  $number_of_dependents = $conn->real_escape_string($_POST['dependents']);
  $number_of_generators = $conn->real_escape_string($_POST['number-of-generators']);

  $category = mysqli_real_escape_string($conn, $_POST['category']);
  $class = mysqli_real_escape_string($conn, $_POST['class']);
  $installationDate = mysqli_real_escape_string($conn, $_POST['installation-date']);


  $query = "UPDATE sites SET site_id='$siteId', other_id='$otherId', latitude_code='$latitude',
    amount_requested='$amount_requested', amount_approved='$amount_approved', processing_fee='$processing_fee',
    longitude_code='$longitude', site_name='$sitename', city='$city',
    region='$region', dependents='$number_of_dependents', number_of_generators='$number_of_generators',
    category='$category', class='$class', starting_date='$installationDate' 
    WHERE site_id='$siteId'";

  if ($conn->query($query) === TRUE) {
    session_start();
    // $_SESSION['message'] = "Loan Updated successfully";
    echo "<script>window.location.href = 'view-sites.php';</script>";
    exit;
  } else {
    echo "Error: " . $query . "<br>" . $conn->error;
  }
}

?>