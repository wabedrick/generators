<?php
session_start();
if (!isset($_SESSION["username"])) {
  header('location: ../index.php');
}
?>

<?php
include 'connection/db_connection.php';
$currentDate = date("Y-m-d");

$sql = "SELECT * FROM loans LEFT JOIN borrowers ON loans.borrower = borrowers.ninNumber
ORDER BY loan_number DESC";
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
          <h2>List of Loans</h2>
          <hr>
        </div>
        <table class="table datatable table-striped">
          <thead class="thead-dark">
            <tr>
              <th scope="col">View</th>
              <th scope="col">Loan Number</th>
              <th scope="col">Borrower</th>
              <th scope="col">Amount Requested</th>
              <th scope="col">Amount Approved</th>
              <th scope="col">Processing Fee</th>
              <th scope="col">Application Fee</th>
              <th scope="col">Loan Period</th>
              <th scope="col">Interest Rate</th>
              <th scope="col">Weekly Recovery</th>
              <th scope="col">Release Date</th>
              <th scope="col">Loan Status</th>

            </tr>
          </thead>
          <tbody>

            <?php while ($loan = $result->fetch_assoc()) { ?>
              <tr>
                <td scope="row">
                  <a href="#" title="Delete" class="text-primary"><i class="bi bi-eye-fill"></i></a>
                </td>
                <td><?php echo $loan['loan_number']; ?></td>
                <td><?php echo $loan['firstname'] . " - " . $loan['borrower']; ?>
                <td><?php echo $loan['amount_requested']; ?></td>
                <td><?php echo $loan['amount_approved']; ?></td>
                <td><?php echo $loan['processing_fee']; ?></td>
                <td><?php echo $loan['application_fee']; ?></td>
                <td><?php echo $loan['loan_period']; ?></td>
                <td><?php echo $loan['interest_rate']; ?></td>
                <td><?php echo $loan['weekly_recovery']; ?></td>
                <td><?php echo $loan['release_date']; ?></td>
                <td><?php echo $loan['loan_status']; ?></td>

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