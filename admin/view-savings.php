<?php
session_start();
if (!isset($_SESSION["username"])) {
  header('location: ../index.php');
}
?>

<?php
include 'connection/db_connection.php';
$sql = "SELECT * FROM savings LEFT JOIN borrowers ON savings.borrower = borrowers.ninNumber
ORDER BY savings.savings_id DESC";
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
          <h2>List of Savings</h2>
          <hr>
        </div>

        <table class="table datatable table-striped">
          <thead class="thead-dark">
            <tr>
              <!-- <th scope="col">View</th> -->
              <!-- <th scope="col">Savings ID</th> -->
              <th scope="col">Borrower</th>
              <th scope="col">Latest Savings</th>
              <th scope="col">Totat Amount Saved</th>
              <th scope="col">Date Saved</th>
              <th scope="col">Amount Withdrew</th>
              <th scope="col">Date Withdrew</th>
              <th scope="col">Balance</th>

            </tr>
          </thead>
          <tbody>

            <?php while ($saving = $result->fetch_assoc()) { ?>
              <tr>
                <!-- <td scope="row">
                  <a href="#" title="Delete" class="text-primary"><i class="bi bi-eye-fill"></i></a>
                </td> -->

                <td><?php echo $saving['firstname'] . " - " . $saving['borrower']; ?></td>
                <td><?php echo $saving['amount']; ?></td>
                <td><?php echo $saving['total_amount'] ?></td>
                <td><?php echo $saving['date_saved']; ?></td>
                <td><?php echo $saving['amount_withdrew']; ?></td>
                <td><?php echo $saving['date_withdrew']; ?></td>
                <td><?php echo $saving['balance']; ?></td>


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