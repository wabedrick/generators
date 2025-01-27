<?php
session_start();
if (!isset($_SESSION["username"])) {
  header('location: ../index.php');
}
?>

<?php
include 'connection/db_connection.php';

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

  .middle {
    padding-top: 50px;
  }
</style>

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
          <h2>List of Collaterals</h2>
          <hr>
        </div>

        <table class="table datatable table-striped">
          <thead class="thead-dark">
            <tr>
              <th scope="col">Loan Number</th>
              <th scope="col">Borrower</th>
              <th scope="col">Collateral Name</th>
              <th scope="col">Serial Number</th>
              <th scope="col">Collateral Photo</th>
            </tr>
          </thead>
          <tbody>
            <?php
            // Assuming you already have a database connection established

            // Fetch collateral details from the database
            $sql = "SELECT * FROM collaterals INNER JOIN borrowers ON collaterals.borrower = borrowers.ninNumber
            INNER JOIN loans ON collaterals.loan = loans.loan_number";
            $result = mysqli_query($conn, $sql);

            if ($result && mysqli_num_rows($result) > 0) {
              while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td class='middle'>" . $row['loan_number'] . "</td>";
                echo "<td class='middle'>" . $row['firstname'] . ' - ' . $row['ninNumber'] . "</td>";
                echo "<td class='middle'>" . $row['collateral_name'] . "</td>";
                echo "<td class='middle'>" . $row['serial_number'] . "</td>";
                echo "<td><a href='" . $row['collateral_photo'] . "' target='_blank'>View photo</a></td>";
                echo "</tr>";
              }
            } else {
              echo "<tr><td colspan='5'>No collateral records found.</td></tr>";
            }

            mysqli_close($conn);
            ?>
          </tbody>
        </table>
      </div>
    </div>

  </section>
  </section>

</main><!-- End #main -->

<!-- ======= Footer ======= -->
<?php include 'footer.php'; ?>