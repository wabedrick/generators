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
$sql = 'SELECT * FROM generators LEFT JOIN sites ON generators.site_id=sites.id';
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
          <h2>List of Available Generators</h2>
          <hr>
        </div>

        <table class="table datatable table-striped">
          <thead class="thead-dark">
            <tr>
              <th scope="col">Update Actual RHrs</th>
              <!-- <th scope="col">SN Engine</th>
              <th scope="col">SN Alternator</th>
              <th scope="col">Phase</th>
              <th scope="col">ICCID Sim Card</th>
              <th scope="col">MSISDN Sim Card</th> -->
              <th scope="col">State</th>
              <th scope="col">Generator ID</th>
              <th scope="col">Site ID</th>
              <th scope="col">Site Name</th>
              <th scope="col">Projected RHrs(monthly)</th>
              <th scope="col">Cumulative RHrs</th>
            </tr>
          </thead>
          <tbody>

            <?php while ($generator = $result->fetch_assoc()) { ?>
              <tr>
                <!-- <td><a href=""></a></td> -->
                <td scope="row">
                  <a href="update-run-hour.php?generator_id=<?php
                                                            $generator_sql = "SELECT id FROM generators";
                                                            $generator_result = $conn->query($generator_sql);
                                                            $generator_id = $generator_result->fetch_assoc();
                                                            echo $generator_id['id']; ?>"
                    title="Update" class="text-success"><i class="bi bi-pencil-square"></i></a>
                </td>

                <td><?php echo $generator['state']; ?></td>
                <td><?php echo $generator['site_id'] . '_G' . $generator['id']; ?></td>
                <td><?php echo $generator['site_id']; ?></td>
                <td><?php echo $generator['site_name']; ?></td>
                <td><?php
                    $generator_id = $generator['id'];
                    $sql = "SELECT generators.created_at FROM generators INNER JOIN sites ON generators.site_id=sites.id";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                      $row = $result->fetch_assoc();
                      $created_at = $row['created_at'];

                      // Calculate the difference in hours
                      date_default_timezone_set('Africa/Nairobi');
                      $current_time = new DateTime();
                      $created_time = new DateTime($created_at);
                      $interval = $current_time->diff($created_time);
                      // $interval_formated =  $interval->format('%y years, %m months, %d days, %h hours, %i minutes, %s seconds');
                      // Convert the entire interval to total hours
                      $cummulative_total_hours = ($interval->days * 24) + $interval->h;
                      // Convert the entire interval to total hours
                      $total_hours = ($interval->days * 24) + $interval->h + ($interval->i / 60) + ($interval->s / 3600);
                      $max_hours = 31 * 24;
                      if ($total_hours > $max_hours) {
                        $total_hours = $max_hours;
                      }
                      echo $total_hours;
                    } else {
                      echo "No item found with the given ID.";
                    }
                    ?></td>
                <td><?php echo $cummulative_total_hours; ?></td>
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