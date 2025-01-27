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
$sql = 'SELECT s.site_name AS site_name, 
        g.generator_id AS generator_id, 
        s.primary_id AS primary_id,
        COALESCE(g.status, "N/A") AS gen_status
        FROM generators g 
        LEFT JOIN sites s ON g.site_id = s.primary_id';

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
              <th scope="col">Status</th>
              <th scope="col">Generator ID</th>
              <th scope="col">Site ID</th>
              <th scope="col">Site Name</th>
              <th scope="col">Projected RHrs(monthly)</th>
              <th scope="col">Cumulative RHrs</th>
              <th scope="col">Actual RHrs</th>
            </tr>
          </thead>
          <tbody>

            <?php while ($generator = $result->fetch_assoc()) { ?>
              <tr>
                <!-- <td><a href=""></a></td> -->
                <td scope="row">
                  <a href="update-run-hour.php?generator_id=<?php
                                                            $generator_sql = "SELECT generator_id FROM generators";
                                                            $generator_result = $conn->query($generator_sql);
                                                            $generator_id = $generator_result->fetch_assoc();
                                                            echo $generator_id['generator_id']; ?>"
                    title="Update" class="text-success"><i class="bi bi-pencil-square"></i></a>
                </td>

                <td><?php echo $generator['gen_status']; ?></td>
                <td><?php echo $generator['primary_id'] . '_G' . $generator['generator_id']; ?></td>
                <td><?php echo $generator['primary_id']; ?></td>
                <td><?php echo $generator['site_name']; ?></td>

                <td><?php
                    // $generator_id = $generator['generator_id'];
                    $sql = "SELECT generators.installation_date FROM generators INNER JOIN sites ON generators.site_id=sites.primary_id";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                      $row = $result->fetch_assoc();
                      $installation_date = $row['installation_date'];

                      // Check if installation_date is valid and matches mm/dd/yyyy format
                      $date_object = DateTime::createFromFormat('m/d/Y', $installation_date);

                      if ($date_object && $date_object->format('m/d/Y') === $installation_date) {
                        // Valid date, proceed with calculations
                        date_default_timezone_set('Africa/Nairobi');
                        $current_time = new DateTime(); // Current time
                        $created_time = $date_object;   // Parsed installation date

                        $interval = $current_time->diff($created_time);

                        // Calculate total hours
                        $total_hours = ($interval->days * 24) + $interval->h + ($interval->i / 60) + ($interval->s / 3600);
                        $max_hours = 31 * 24; // Maximum threshold for hours

                        // Ensure total_hours does not exceed max_hours
                        if ($total_hours > $max_hours) {
                          $total_hours = $max_hours;
                        }

                        echo "Total Hours: " . round($total_hours, 2);
                      } else {
                        // Invalid date format or value
                        echo "Invalid installation date format: " . htmlspecialchars($installation_date);
                      }
                    } else {
                      echo "No item found with the given ID.";
                    }
                    ?>

                </td>
                <td><?php echo $cummulative_total_hours; ?></td>
                <td><?php echo $generator['actual_run_hours']; ?></td>
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