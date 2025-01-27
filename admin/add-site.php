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

    <form method="POST" action="add-site.php" enctype="multipart/form-data" class="row g-3">
      <div class="col-md-6">
        <label for="inputName5" class="form-label">Site ID</label>
        <input type="text" class="form-control" id="inputName5" name="site-id" required>
      </div>

      <div class="col-md-6">
        <label for="inputName5" class="form-label">Other ID</label>
        <input type="text" class="form-control" id="inputName5" name="other-id" required>
      </div>

      <div class="col-md-6">
        <label for="inputEmail5" class="form-label">Latitude</label>
        <input type="type" class="form-control" id="inputEmail5" name="lat">
      </div>

      <div class="col-md-6">
        <label for="inputPassword5" class="form-label">Longitude</label>
        <input type="text" class="form-control" id="inputPassword5" name="long">
      </div>

      <div class="col-md-4">
        <label for="inputAddress5" class="form-label">Site Name</label>
        <input type="text" class="form-control" id="inputAddres5s" name="site-name">
      </div>

      <div class="col-md-4">
        <label for="inputAddress5" class="form-label">City</label>
        <input type="text" class="form-control" id="inputAddres5s" name="city">
      </div>

      <div class="col-md-4">
        <label for="inputState" class="form-label">Region</label>
        <select id="inputState" class="form-select" name="region">
          <option selected>Choose...</option>
          <option value="Central">Central</option>
          <option value="Eastern">Eastern</option>
          <option value="Western">Western</option>
          <option value="Northern">Northern</option>
          <option value="Southern">Southern</option>
        </select>
      </div>

      <div class="col-md-3">
        <label for="inputAddress5" class="form-label">Dependents</label>
        <input type="number" class="form-control" id="inputAddres5s" name="dependents">
      </div>

      <div class="col-md-3">
        <label for="inputAddress5" class="form-label">Number Of Generators</label>
        <input type="number" class="form-control" id="inputAddres5s" name="number-of-generators">
      </div>

      <div class="col-md-3">
        <label for="inputState" class="form-label">Category</label>
        <select id="inputState" class="form-select" name="category">
          <option selected>Choose...</option>
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
        <select id="inputState" class="form-select" name="class">
          <option selected>Choose...</option>
          <option>A</option>
          <option>B</option>
          <option>C</option>
          <option>D</option>
        </select>
      </div>

      <div class="col-md-6">
        <label for="inputAddress5" class="form-label">Installation Date</label>
        <input type="date" class="form-control" id="inputAddres5s" name="installation-date">
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

  // $photoName = $_FILES['photo']['name'];
  // $imageFileType = strtolower(pathinfo($photoName, PATHINFO_EXTENSION));
  // $photoTempPath = $_FILES['photo']['tmp_name'];
  // $photoUploadPath = '../uploads/' . $firstname . "_" . $ninNumber . "." . $imageFileType;

  // Move the uploaded photo to the desired location
  // move_uploaded_file($photoTempPath, $photoUploadPath);
  // echo $installationDate;
  // exit();


  $checkQuery = "SELECT * FROM sites WHERE site_id = '$siteId'";
  $result = $conn->query($checkQuery);
  if ($result->num_rows > 0) {
    $_SESSION['message'] = "Site ID already exists.";
    echo "<script>window.location.href = 'add-site.php';</script>";
    exit;
  }

  $sql =  "INSERT INTO sites
  (site_id,other_id, latitude_code, longitude_code,site_name,city,region,dependents,category, class,number_of_generators, starting_date) 
  VALUES('$siteId','$otherId','$latitude','$longitude','$sitename','$city','$region',$number_of_dependents,
  '$category','$class', $number_of_generators,'$installationDate')";

  if ($conn->query($sql) === TRUE) {
    $_SESSION['message'] = "Site added successfully";
    echo "<script>window.location.href = 'view-sites.php';</script>";
    exit;
  } else {
    echo "Error: " . $conn->error;
  }
}
?>