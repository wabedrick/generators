<?php
session_start();
if (!isset($_SESSION["username"])) {
    header('location: ../index.php');
    exit; // Added exit statement after redirect
}

include 'connection/db_connection.php';
?>

<?php include 'header_aside.php'; ?>

<!--main content-->
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Withdraw From Your Savings</h1>
        <nav>
    </div><!-- End Page Title -->
    <section class="section">
        <div class="row">
            <div class="col-lg-6">
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <!-- Multi Columns Form -->
                <form action="withdraw.php" method="post" class="row g-3 mt-2">
                    <div class="form-group mt-1">
                        <label for="group">Borrower</label>
                        <select name="borrower" id="borrower" class="form-control form-control-user" required>
                            <option value="">Select Borrower</option>
                            <?php
                            $borrowerQuery = "SELECT * FROM borrowers LEFT JOIN savings 
                            ON borrowers.ninNumber = savings.borrower
                            ORDER BY firstname ASC";
                            $borrowers = $conn->query($borrowerQuery);
                            while ($borrower = $borrowers->fetch_assoc()) {
                                echo '<option value="' . $borrower['ninNumber'] . '">' . $borrower['firstname'] . " - " . $borrower['ninNumber'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="inputAddress2" class="form-label">Enter Amount</label>
                        <input type="number" class="form-control" id="inputAmount" name="amountWithdrew" placeholder="Amount to Withdraw">
                    </div>

                    <div class="col-md-6">
                        <label for="inputDate" class="form-label">Date Withdrew</label>
                        <input type="date" class="form-control" name="dateWithdrew" id="inputDate">
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form><!-- End Multi Columns Form -->
            </div>
        </div>
    </section>
</main><!-- End #main -->

<!-- ======= Footer ======= -->
<?php include 'footer.php'; ?>


<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $borrower = isset($_POST['borrower']) ? mysqli_real_escape_string($conn, $_POST['borrower']) : null;
        $amount = isset($_POST['amountWithdrew']) ? mysqli_real_escape_string($conn, $_POST['amountWithdrew']) : null;
        $dateWithdrew = isset($_POST['dateWithdrew']) ? mysqli_real_escape_string($conn, $_POST['dateWithdrew']) : null;

        $sql_total = "SELECT SUM(amount) AS amount, SUM(amount_withdrew) AS amount_withdrew, 
        total_amount, savings_id FROM savings WHERE borrower = '$borrower' 
        ORDER BY savings_id DESC LIMIT 1";
        $result = $conn->query($sql_total);
        $row = mysqli_fetch_assoc($result);

        $total_amount = $row['total_amount'];

        if ($total_amount < $amount) {    
          echo "<script>alert('Insufficient Funds in your Savings Account');</script>";
          // header("Location: withdraw.php");
          exit;   
  
        } else {

            $balance = $row['amount'] - $row['amount_withdrew']-$amount;

            $amount_withdrew = $amount;

            // Update the latest savings entry for the borrower
            $savings_id = $row['savings_id'];
            $sql_update = "INSERT INTO savings (borrower, amount_withdrew,date_withdrew, balance) 
            VALUES('$borrower', '$amount_withdrew', '$dateWithdrew', '$balance')";

            if ($conn->query($sql_update) === TRUE) {
                // Insert information about the amount withdrawn
                $query = "INSERT INTO withdraws (borrower, amount_withdrew, date_withdrew, balance) 
                VALUES ('$borrower', '$amount', '$dateWithdrew', '$balance')";

                if ($conn->query($query) === TRUE) {
                  session_start();
                  $_SESSION['message'] = "Successfully withdrawn Shs " . $amount . " from your Savings";
                  // echo "Successfully withdrawn Shs " . $amount . " from your Savings";
                  echo "<script>window.location.href = 'view-savings.php';</script>";
                    exit;
                } else {
                    echo "Error: Failed to insert withdrawal information.";
                }
            } else {
                echo "Error: Failed to update savings record.";
            }
            
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
