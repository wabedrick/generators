<?php
session_start();
if (!isset($_SESSION["username"])) {
  header('location: ../index.php');
}
?>

<?php
// Connect to the database
include 'connection/db_connection.php';

// Retrieve the list of borrowers from the database
// $sql = 'SELECT * FROM sites ORDER BY starting_date DESC';
$sql = 'SELECT * FROM sites';
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
  }, 2000); // Delay of 2 second (2000 milliseconds)
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
          <h2>List of Available Sites</h2>
          <hr>
        </div>

        <table class="table datatable table-striped">
          <thead class="thead-dark">
            <tr>
              <th scope="col">Actions</th>
              <th scope="col">Site ID</th>
              <th scope="col">Site Name</th>
              <th scope="col">city</th>
              <th scope="col">Region</th>
              <th scope="col">Dependents</th>
              <th scope="col">Generators</th>
              <th scope="col">Category</th>
              <th scope="col">Class</th>
            </tr>
          </thead>
          <tbody>

            <?php while ($site = $result->fetch_assoc()) { ?>
              <tr>
                <td scope="row">
                  <!-- <a href="view-site.php?site_id=<?php echo $site['site_id']; ?>" title="view more" class="text-danger"><i class="bi bi-eye-fill"></i></a> -->
                  <a href="edit-site.php?site_id=<?php echo $site['site_id']; ?>" title="Edit" class="text-success"><i class="bi bi-pencil-square"></i></a>
                </td>
                <td><?php echo $site['site_id']; ?></td>
                <td><?php echo $site['site_name']; ?></td>
                <td><?php echo $site['city']; ?></td>
                <td><?php echo $site['region']; ?></td>
                <td><?php echo $site['dependents']; ?></td>
                <td><?php echo $site['number_of_generators']; ?></td>
                <td><?php echo $site['category']; ?></td>
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