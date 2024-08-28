<?php
session_start();
if(!isset($_SESSION["username"])){
    header('location: ../index.php');
}
?>

<?php
include 'connection/db_connection.php';

$current_date = date('Y-m-d');

$sql = "SELECT * FROM loans INNER JOIN borrowers ON loans.borrower = borrowers.ninNumber
INNER JOIN repayments ON loans.loan_number = repayments.loan WHERE loans.loan_status = 'Inactive'";
$result = $conn->query($sql);

$row = $result->fetch_assoc();

$extra_charge = 10/100 * ($row['amount_approved']);

session_start();
?>
<?php include 'header_aside.php'; ?>


<!--main content-->
  <main id="main" class="main">

    <?php if(isset($_SESSION['message'])) {?>
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
    <?php } unset($_SESSION['message']); ?>


  <section>
  <div class="container">
    <h1>List of Loans</h1>
    <table class="table datatable table-striped">
      <thead class="thead-dark">
        <tr>
          <th scope="col">View</th>
          <th scope="col">Loan Number</th>
          <!--<th scope="col">Loan Type</th>-->
          <th scope="col">Borrower</th>
          <th scope="col">Amount Taken</th>
          <th scope="col">Amount Paid</th>
          <th scope="col">Balance</th>
          <th scope="col">Time Due</th>
          <th scope="col">Extra Charge</th>
         
        </tr>
      </thead>
      <tbody>

        <?php while ($loan = $result -> fetch_assoc()) { ?>
          <tr>
          <td scope="row">
              <a href="#" title="Delete" class="text-primary"><i class="bi bi-eye-fill"></i></a>
            </td>
            <td><?php echo $loan['loan_number']; ?></td>
            <td><?php echo $loan['firstname']." - ". $loan['borrower']; ?>
            <td><?php echo $loan['amount_approved']; ?></td>
            <td><?php echo $loan['amount_collected']; ?></td>
            <td><?php echo $loan['balance']; ?></td>
            <td><?php echo $loan['processing_fee']; ?></td>
            <td><?php echo $extra_charge; ?></td>

          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>

  </section>
    </section>

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
<?php include 'footer.php'; ?>