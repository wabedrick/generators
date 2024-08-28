<?php
include 'connection/db_connection.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] ==='POST') {
  // Validate and sanitize the form input
  $loan_type = $conn->real_escape_string($_POST['loanType']);
  $borrower = $conn->real_escape_string($_POST['borrower']);
  $distributed_by = $conn->real_escape_string($_POST['distributedBy']);
  $amount_requested = $conn->real_escape_string($_POST['amountRequested']);
  $amount_approved = $conn->real_escape_string($_POST['amountApproved']);
  $processing_fee = $conn->real_escape_string($_POST['processingFee']);
  $application_fee = $conn->real_escape_string($_POST['applicationFee']);
  $loan_period = $conn->real_escape_string($_POST['loanPeriod']);
  $interest_rate = $conn->real_escape_string($_POST['interestRate']);
  $release_date = $conn->real_escape_string($_POST['loanReleaseDate']);

  $total_fee = $processing_fee + $application_fee;

  $num_weeks = $loan_period * 4;
  $interest = ($interest_rate  / 100) * $amount_approved;
  $total_amount = $amount_approved + $interest;
  $weekly_recovery = $total_amount / $num_weeks;

  $dueDate = date('Y-m-d', strtotime($release_date . " + $loan_period months"));

  if($dueDate > $current_date){
    $status = "Inactive";

  }
  else{
    $status = "Active";
  }


$sql =  "INSERT INTO loans(loan_type, borrower, distributed_by, amount_requested,
 amount_approved, processing_fee,application_fee, total_fee, loan_period, interest_rate,
 release_date, weekly_recovery, due_date, loan_status)
 VALUES ('$loan_type','$borrower','$distributed_by','$amount_requested','$amount_approved',
'$processing_fee','$application_fee','$total_fee','$loan_period','$interest_rate',
'$release_date', '$weekly_recovery', '$dueDate', '$status')";

if ($conn->query($sql) === TRUE) {
  session_start();
  $_SESSION['message'] = "Loan added successfully";
echo "<script>window.location.href = 'view-loans.php';</script>";

exit;
} else {
  echo "Error: Failed to add it to the database.......";
}

}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

?>