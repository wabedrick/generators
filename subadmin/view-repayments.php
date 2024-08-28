<?php
session_start();
if (!isset($_SESSION["username"])) {
  header('location: ../index.php');
}
?>

<?php
include 'connection/db_connection.php';

$sql = "SELECT * FROM repayments as r
INNER JOIN borrowers as b ON r.borrower = b.ninNumber
INNER JOIN loans as l ON r.loan = l.loan_number ORDER BY r.repayment_id DESC";

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
          <h2>List of Recoveries</h2>
          <hr>
        </div>
        
        <table class="table datatable table-striped">
          <thead class="thead-dark">
            <tr>
              <th scope="col">Collection Date</th>
              <th scope="col">Borrower NIN</th>
              <th scope="col">Borrower Name</th>
              <th scope="col">Loan Number</th>
              <th scope="col">Amount Collected</th>
              <th scope="col">Total Amount Collected</th>
              <th scope="col">Collected By</th>
              <th scope="col">Amount Taken</th>
              <th scope="col">Balance</th>
            </tr>
          </thead>
          <tbody>

            <?php while ($row = $result->fetch_assoc()) { ?>
              <tr>
                <td><?php echo $row['collection_date']; ?></td>
                <td><?php echo $row['borrower']; ?></td>
                <td><?php echo $row['firstname']; ?> <?php echo $row['lastname']; ?></td>
                <td><?php echo $row['loan_number']; ?>
                <td><?php echo $row['amount_collected']; ?></td>
                <td><?php echo $row['total_amount_collected']; ?></td>
                <td><?php echo $row['collected_by']; ?></td>
                <td><?php echo $row['amount_approved']; ?></td>
                <td><?php echo $row['balance']; ?></td>

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