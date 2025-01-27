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
$sql = 'SELECT * FROM borrowers ORDER BY created_at DESC';
$result = $conn->query($sql);

?>

<style>
  i {
    /* padding: 0px 0px 0px 0px; */
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
          <h2>List of Borrowers</h2>
          <hr>
        </div>

        <table class="table datatable table-striped">
          <thead class="thead-dark">
            <tr>
              <th scope="col">Actions</th>
              <th scope="col">NIN</th>
              <th scope="col">First Name</th>
              <th scope="col">Last Name</th>
              <th scope="col">Title</th>
              <th scope="col">Gender</th>
              <th scope="col">Tel</th>
              <th scope="col">Email</th>
              <th scope="col">Location</th>
              <th scope="col">District</th>


            </tr>
          </thead>
          <tbody>

            <?php while ($borrower = $result->fetch_assoc()) { ?>
              <tr>
                <td scope="row">
                  <a href="#" title="Edit" class="text-success"><i class="bi bi-pencil-square"></i></a>
                  <a href="#" title="Delete" class="text-danger"><i class="bi bi-trash-fill"></i></a>
                </td>
                <td><?php echo $borrower['ninNumber']; ?></td>
                <td><?php echo $borrower['firstname']; ?></td>
                <td><?php echo $borrower['lastname']; ?></td>
                <td><?php echo $borrower['title']; ?></td>
                <td><?php echo $borrower['gender']; ?></td>
                <td><?php echo $borrower['phone']; ?></td>
                <td><?php echo $borrower['email']; ?></td>
                <td><?php echo $borrower['location']; ?></td>
                <td><?php echo $borrower['district']; ?></td>
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