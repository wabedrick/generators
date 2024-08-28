<?php
session_start();
if (!isset($_SESSION["username"])) {
  header('location: ../index.php');
}

include 'connection/db_connection.php';

$sql = "SELECT * FROM administrative ORDER BY administrative_id DESC";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

?>

<?php include 'header_aside.php'; ?>

<!--main-->
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
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
        <h2>View all the monthly administrative Costs Here</h2>
        <hr>
        </div>
        
        <table class="table datatable table-striped">
          <thead class="thead-dark">
            <tr>
              <th scope="col">Edit</th>
              <th scope="col">Month</th>
              <th scope="col">Household</th>
              <th scope="col">Amount Invested</th>
              <th scope="col">Day Invested</th>
              <th scope="col">View Monthly Profit</th>
            </tr>
          </thead>
          <tbody>

            <?php while ($admin = $result->fetch_assoc()) { ?>
              <tr>

              <td scope="row"><a href="edit-investment.php?id=<?php echo $admin['administrative_id']; ?>" 
              title="edit"><i class="bi bi-pencil-square"></i></a></td>
                <td><?php echo $admin['year_month']; ?></td>
                <td><?php echo $admin['household']; ?></td>
                <td><?php echo $admin['investment']; ?></td>
                <td><?php echo $admin['date_stored']; ?></td>
                <td scope="row"><a href="view-household-profit-share.php?id=<?php echo $admin['household'];?>" title="View"><i class="bi bi-eye-fill"></i></a></td>

              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>
  </section>

</main><!-- End #main -->

<!-- ======= Footer ======= -->
<?php include 'footer.php'; ?>