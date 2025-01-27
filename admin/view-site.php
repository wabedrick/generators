<?php
include 'connection/db_connection.php';
session_start();
if (!isset($_SESSION["username"])) {
    header('location: ../index.php');
}

require_once('header_aside.php');

$nin = $_GET['ninNumber'];
$sql = "SELECT * FROM borrowers WHERE ninNumber = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $nin);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $request_error = 0;
    $row = $result->fetch_assoc();
    $firstname = $row['firstname'];
    $lastname = $row['lastname'];
    $groupid = $row['group_id'];
    $group = $conn->query("SELECT * FROM borrower_groups WHERE id = $groupid")->fetch_assoc()['name'] ?? 'Undefined Group';
} else {
    $request_error = 1;
}

$status_query = "SELECT * FROM loans INNER JOIN repayments ON loans.loan_number=repayments.loan
WHERE loans.borrower=repayments.borrower";

$result = $conn->query($status_query);

$current_date = date("Y-m-d");

while ($row = $result->fetch_assoc()) {
    if ($row['balance'] <= 0 && ($row['due_date'] > $current_date || $row['due_date'] < $current_date)) {
        $status = "Cleared";
    } else if ($row['due_date'] > $current_date && ($row['balance'] > 0 || $row['balance'] === null)) {
        $status = "Active";
    } else if ($row['due_date'] < $current_date && ($row['balance'] > 0 || $row['balance'] === null)) {
        $status = "Expired";
    } else{
        // Handle any other conditions or set a default status if necessary
        $status = "Unknown";
    }

    $loan_number = $row['loan_number'];
    $update_query = "UPDATE loans SET loan_status='$status' WHERE loan_number='$loan_number'";
    if ($conn->query($update_query) === TRUE) {
        // echo "Loan status updated successfully.";
    } else {
        echo "Error updating loan status: " . $conn->error;
    }
}



//Returning the repay infor
$repays = "SELECT * FROM repayments LEFT JOIN loans ON loans.loan_number = repayments.loan 
WHERE repayments.borrower = '$nin' AND loan_status='Active' OR loan_status='Expired' 
ORDER BY repayment_id DESC";
$result1 = $conn->query($repays);

//Sum of amount collected
try{
$repay_repay = "SELECT SUM(amount_collected) AS amountCollected FROM repayments 
LEFT JOIN loans ON  loans.loan_number = repayments.loan
WHERE repayments.borrower = '$nin' AND loan_status='Active' OR loan_status='Expired' ";
$result_repay = $conn->query($repay_repay);


if($result_repay !==null){
// if($result_repay->num_rows>0){

$row_repay = $result_repay->fetch_assoc();
$amount_collected = $row_repay['amountCollected'];

} else{
    $amount_collected = 0;
}
}
catch(Exception $e){

    echo "Error: ".$e->getMessage();

}


//Borrower Loan History
try{
    
    $sql4 = "SELECT * FROM loans LEFT JOIN repayments ON loans.loan_number=repayments.loan
    WHERE loans.borrower = '$nin' AND loan_status='Active' OR loan_status='Expired'";
    $result5 =  $conn->query($sql4);
    $loan_repay_total = $result5->fetch_assoc();

    if ($loan_repay_total && isset($loan_repay_total['total_amount_tobe_paid'])) {
        
        $total_loan_tobe_paid= $loan_repay_total['total_amount_tobe_paid'];
    } 
    else {
        $total_loan_tobe_paid = 0;
    }

    if ($loan_repay_total && isset($loan_repay_total['balance'])) {
        
        // $balance= $loan_repay_total['balance'];
       $balance = $total_loan_tobe_paid - $amount_collected;

    } 
    else {
        $balance = $total_loan_tobe_paid;
    }

    }
    
    catch (mysqli_sql_exception $e) {
        // Handle the error
        echo "Query failed: " . $e->getMessage();
    }

//Returning the values in the repayments
try{
$repay_sql = "SELECT SUM(amount_collected) AS total_amount_collected, date_collected, balance, 
amount_collected FROM repayments INNER JOIN loans WHERE borrower='$nin' AND loan_status='Active' ";
$result1 = $conn->query($repay_sql);


} catch(mysqli_sql_exception $e){
    echo "Error: ".$e->getMessage();
}

//Total loans
$sql1 = "SELECT COUNT(*) AS loanCount FROM loans WHERE borrower = '$nin'";
$result2 =  $conn->query($sql1);
$row_total = $result2->fetch_assoc();
$loanCount = $row_total['loanCount'];

//Cleared
$sql2 = "SELECT COUNT(*) AS loanCount1 FROM loans WHERE borrower = '$nin' AND loan_status = 'Cleared'";
$result3 =  $conn->query($sql2);
$row1 = $result3->fetch_assoc();
$loanCount1 = $row1['loanCount1'];

//Active
$sql3 = "SELECT COUNT(*) AS loanCount2 FROM loans WHERE borrower = '$nin' AND loan_status = 'Active'";
$result4 =  $conn->query($sql3);
$row2 = $result4->fetch_assoc();
$loanCount2 = $row2['loanCount2'];

//Loan History table information
// try{
// $sql6 = "SELECT SUM(amount_collected) AS total_amount_collected,loan_number,amount_approved, 
// loan_period, interest_rate, total_amount_tobe_paid, balance, loan_status FROM loans INNER JOIN repayments 
// ON loans.loan_number=repayments.loan WHERE loans.borrower ='$nin' ";
// $result6 =  $conn->query($sql4);
// // $loan_repay_total = $result5->fetch_assoc();
// }
// catch (mysqli_sql_exception $e) {
//     // Handle the error
//     echo "Query failed: " . $e->getMessage();
// }

try{
    
    $sql7 = "SELECT * FROM loans LEFT JOIN repayments ON loans.loan_number=repayments.loan
    WHERE loans.borrower = '$nin' ORDER BY loan_number  AND collection_date DESC";
    $result7 =  $conn->query($sql7);
    // $loan_repay_total = $result7->fetch_assoc();

    }
    
    catch (mysqli_sql_exception $e) {
        // Handle the error
        echo "Query failed: " . $e->getMessage();
    }
//Starting and end month times
$starting_month_date = date('Y-m-01');
$ending_month_date = date('Y-m-t');

?>

<!-- Main Content -->
<main id="main" class="main">

    <!-- align pagetitle on the left and button on the right -->
    <div class="d-flex justify-content-between">
        <div class="pagetitle float-left">
            <h1>View Borrower</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item active">View Borrower</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <div class="mt-2">
            <?php
            if ($request_error == 1) {
            ?>
                <h4 class="text-muted">Borrower Not Found</h4>
            <?php
                include 'footer.php';
                exit();
            }
            ?>
            <h4 class="text-dark">
                <?php
                echo $firstname . " " . $lastname . " - " . $nin
                ?>
            </h4>
        </div>

        <div class="mt-2">
            <h5 class="text-muted">Group:<?php echo $group ?></h5>
        </div>
    </div>
    </div>

    <section class="section dashboard">
        <div class="row">

            <!-- Left side columns -->
            <div class="col-lg-12">
                <div class="row">

                    <div class="col-xxl-4 col-xl-12 mt-3">

                        <div class="card info-card customers-card">

                            <div class="card-body">
                                <h5 class="card-title">Current Loan details <span>
                                <?php echo $starting_month_date . " to " . $ending_month_date; ?>
                                </span></h5>

                    <div class="progress">
                    <?php
                    // set_error_handler(function(){
                    //     throw new Exception("Divide By zero has Occured");
                    // });

                    try{
                        $percent = intdiv($amount_collected, $total_loan_tobe_paid);
                        $percentage =round($percent * 100, 1);
                    }
                    catch(DivisionByZeroError $e ){
                        echo "There is nothing to display";
                        $percent = 0;
                        $percentage = round( $percent* 100, 1);
                    }
                    
                    // restore_error_handler();
                    ?>
                    <div class="progress-bar" role="progressbar" style="<?php echo 'width: ' . $percentage . '%';  ?>" 
                    aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"><?php echo $percentage ?></div>
                  </div>
                  <h6 class="text-center text-small mt-2"><?php
                                                          echo number_format($amount_collected);
                                                          ?> / <?php echo  number_format($total_loan_tobe_paid); ?></h6>
                    </div>
                    <div class="d-flex justify-content-between" style="font-size: 15px;">
                        <p class="text-left mt-2 h5 text-success">Paid: <?php echo $amount_collected; ?>  <br> 
                        <!-- <span class="text-danger">Elapsed: 6weeks</span>  --> </p>
                        <p class="text-center mt-2 h5">Total: <?php echo $total_loan_tobe_paid; ?> <br>
                         <!-- <span class="text-danger">Total: 9weeks</span> --> </p>
                        <p class=" text-right mt-2 h5 text-danger">Bal: <?php echo $balance; ?> <br> 
                         <!-- <span class="text-danger">Remaining: 3weeks</span> --> </p>
                        </div>
                        <hr>
                        <div class="links" style="display:flex; justify-content:space-between; margin:0.5px; padding:0.5px">
                        <a class="btn btn-primary" href="add-savings.php" role="button">Add Savings</a>
                        <a class="btn btn-primary" href="add-repayments.php" role="button">Make Repayments</a>
                        <a class="btn btn-primary" href="add-Loan.php" role="button">Add Loan</a>
                        </div>
                        <hr>
                         <h4 class="text-center mt-5 text-primary">Repayments</h4>
                         <table class="table table-stripped">
                            <thead class="thead-dark">
                                 <tr>
                                    <th>Loan Number</th>
                                    <th>Date repaid</th>
                                    <th>Amount Repaid</th>
                                    <th>Loan Balance</th>
                                </tr>
                            </thead>
                        <tbody>
                          <?php 
                          if($result1 !== null){
                          while ($repay = $result1->fetch_assoc()) { ?>
                          <td><?php echo $repay['loan']; ?></td>
                          <td><?php echo $repay['collection_date']; ?></td>
                          <td><?php echo $repay['amount_collected']; ?></td>
                          <td><?php echo $repay['balance']; ?></td>
                          </tr>
                        <?php }
                          } 
                        ?>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div><!-- End Customers Card -->

                    <!-- Sales Card -->
                    <!-- <div class="col-xxl-4 col-md-4 mt-3"> -->
                    <div class="col-xxl-4 col-xl-12 mt-3">
                        <div class="card info-card sales-card">

                            <div class="card-body">
                                <h5 class="card-title">Total Loans</h5>

                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-cash"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6><?php echo $loanCount ?></h6>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div><!-- End Sales Card -->

                    <!-- Revenue Card -->
                    <div class="col-xxl-4 col-xl-12 mt-3">
                        <div class="card info-card revenue-card">

                            <div class="card-body">
                                <h5 class="card-title">Cleared</h5>

                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <!-- <span class="fs-4">UGX</span> -->
                                        <i class="bi bi-cash"></i>
                                    </div>
                                    <div class="ps-3">
                                    <h6><?php echo $loanCount1?></h6>

                                    </div>
                                </div>
                            </div>

                        </div>
                    </div><!-- End Revenue Card -->

                    <!-- Revenue Card -->
                    <div class="col-xxl-4 col-xl-12 mt-3">
                        <div class="card info-card revenue-card">

                            <div class="card-body">
                                <h5 class="card-title">Active</h5>

                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center 
                                    justify-content-center text-warning">
                                        <i class="bi bi-cash"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6><?php echo $loanCount2 ?></h6>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div><!-- End Revenue Card -->


                    <h2 class="text-center text-muted mt-3">Loan History</h2>

                    <table class="table datatable table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">Loan Number</th>
                                <th scope="col">Amount Taken</th>
                                <th scope="col">Period(Months)</th>
                                <th scope="col">Interest Rate</th>
                                <th scope="col">Amount To Be Paid Back</th>
                                <th scope="col">Amount Paid Back</th>
                                <th scope="col">Balance</th>
                                <!-- <th scope="col">End Date</th> -->
                                <th scope="col">Loan Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($loan = $result7->fetch_assoc()) { ?>

                            <td><?php echo $loan['loan_number']; ?></td>
                            <td><?php echo $loan['amount_approved']; ?></td>
                            <td><?php echo $loan['loan_period']; ?></td>
                            <td><?php echo $loan['interest_rate']; ?></td>
                            <td><?php echo $loan['total_amount_tobe_paid']; ?></td>
                            <td><?php echo $loan['total_amount_collected']; ?></td>
                            <td><?php echo $loan['balance']; ?></td>
                            <td><?php 
                            // if($loan['balance']==0){
                            //     $loan_status = 'Cleared';
                            //     echo $loan_status;

                            // }
                            // else{
                            //     echo $loan['loan_status'];
                            // }
                            echo $loan['loan_status'];
                            ?></td>


                            </tr>
                        <?php } ?>

                        </tbody>
                    </table>

                </div>
            </div><!-- End Left side columns -->


        </div>
    </section>

</main><!-- End #main -->

<!-- ======= Footer ======= -->
<?php include('footer.php'); ?>