<?php
include 'connection/db_connection.php';
session_start();
if (!isset($_SESSION["username"])) {
  header('location: ../index.php');
}

// Get the current date
$currentDate = date('Y-m-d');

//get the value of the period

if (!isset($_GET['view'])) {
  $period = 'Today';
} else
  $period =  $_GET['view'] == 'month' ? 'Month' : ($_GET['view'] == 'week' ? 'Week' : ($_GET['view'] == 'overall' ? 'Overall' : 'Today'));


if ($period == 'Today') {
  //Total number of loans taken today
  $sql = "SELECT COUNT(*) AS loan_count FROM loans WHERE release_date = '$currentDate'";
  $result = mysqli_query($conn, $sql);
  $row = mysqli_fetch_assoc($result);
  $totalLoanCount = $row['loan_count'];

  //Total amount of loan taken today, how much money is given out today
  $sql1 = "SELECT SUM(amount_approved) AS total_loans_today FROM loans WHERE release_date = '$currentDate'";
  $result1 = mysqli_query($conn, $sql1);
  $row1 = mysqli_fetch_assoc($result1);
  $totalLoanAmount = $row1['total_loans_today'];

  //Total amount of processing fee recieved today
  $sql2 = "SELECT SUM(processing_fee) AS total_process_today FROM loans WHERE release_date = '$currentDate'";
  $result2 = mysqli_query($conn, $sql2);
  $row2 = mysqli_fetch_assoc($result2);
  $totalProcessingFees = $row2['total_process_today'];

  //Total amount of application fee received today
  $sql3 = "SELECT SUM(application_fee) AS total_apply_today FROM loans WHERE release_date = '$currentDate'";
  $result3 = mysqli_query($conn, $sql3);
  $row3 = mysqli_fetch_assoc($result3);
  $totalApplicationFees = $row3['total_apply_today'];

  //amount of repayment made today
  $sql4 = "SELECT COUNT(*) AS total_repayments_today FROM repayments WHERE collection_date = '$currentDate'";
  $result4 = mysqli_query($conn, $sql4);
  $row4 = mysqli_fetch_assoc($result4);
  $totalRepaymentCount = $row4['total_repayments_today'];

  // Total repayment made today
  $sql5 = "SELECT SUM(amount_collected) AS total_recoveries_today FROM repayments WHERE collection_date = '$currentDate'";
  $result5 = mysqli_query($conn, $sql5);
  $row5 = mysqli_fetch_assoc($result5);
  $totalRepayment = $row5['total_recoveries_today'];
  if ($totalRepayment == null) {
    $totalRepayment = 0;
  }

  //Total amount of processing and application fees received today
  $totalFeesToday = $totalProcessingFees + $totalApplicationFees;
} elseif ($period == 'Week') {
  $sql = "SELECT COUNT(*) AS loan_count FROM loans WHERE release_date >= DATE(NOW()) - INTERVAL 7 DAY";
  $result = mysqli_query($conn, $sql);
  $row = mysqli_fetch_assoc($result);
  $totalLoanCount = $row['loan_count'];

  //Total amount of loan taken this week, how much money is given out this week
  $sql1 = "SELECT SUM(amount_approved) AS total_loans_week FROM loans WHERE release_date >= DATE(NOW()) - INTERVAL 7 DAY";
  $result1 = mysqli_query($conn, $sql1);
  $row1 = mysqli_fetch_assoc($result1);
  $totalLoanAmount = $row1['total_loans_week'];

  //Total amount of processing fee received this week
  $sql2 = "SELECT SUM(processing_fee) AS total_process_week FROM loans WHERE release_date >= DATE(NOW()) - INTERVAL 7 DAY";
  $result2 = mysqli_query($conn, $sql2);
  $row2 = mysqli_fetch_assoc($result2);
  $totalProcessingFees = $row2['total_process_week'];

  //Total amount of application fee received this week
  $sql3 = "SELECT SUM(application_fee) AS total_apply_week FROM loans WHERE release_date >= DATE(NOW()) - INTERVAL 7 DAY";
  $result3 = mysqli_query($conn, $sql3);
  $row3 = mysqli_fetch_assoc($result3);
  $totalApplicationFees = $row3['total_apply_week'];

  // Total amount of repayment made this week
  $sql4 = "SELECT COUNT(*) AS total_repayments_week FROM repayments WHERE collection_date >= DATE(NOW()) - INTERVAL 7 DAY";
  $result4 = mysqli_query($conn, $sql4);
  $row4 = mysqli_fetch_assoc($result4);
  $totalRepaymentCount = $row4['total_repayments_week'];

  // Total repayment made this week
  $sql5 = "SELECT SUM(amount_collected) AS total_recoveries_week FROM repayments WHERE collection_date >= DATE(NOW()) - INTERVAL 7 DAY";
  $result5 = mysqli_query($conn, $sql5);
  $row5 = mysqli_fetch_assoc($result5);
  $totalRepayment = $row5['total_recoveries_week'];


  //Total amount of processing and application fees received this week
  $totalFeesWeek = $totalProcessingFees + $totalApplicationFees;
} elseif ($period == 'Month') {
  $sql = "SELECT COUNT(*) AS loan_count FROM loans WHERE release_date >= DATE(NOW()) - INTERVAL 30 DAY";
  $result = mysqli_query($conn, $sql);
  $row = mysqli_fetch_assoc($result);
  $totalLoanCount = $row['loan_count'];

  //Total amount of loan taken this month, how much money is given out this month
  $sql1 = "SELECT SUM(amount_approved) AS total_loans_month FROM loans WHERE release_date >= DATE(NOW()) - INTERVAL 30 DAY";
  $result1 = mysqli_query($conn, $sql1);
  $row1 = mysqli_fetch_assoc($result1);
  $totalLoanAmount = $row1['total_loans_month'];

  //Total amount of processing fee recieved this month
  $sql2 = "SELECT SUM(processing_fee) AS total_process_month FROM loans WHERE release_date >= DATE(NOW()) - INTERVAL 30 DAY";
  $result2 = mysqli_query($conn, $sql2);
  $row2 = mysqli_fetch_assoc($result2);
  $totalProcessingFees = $row2['total_process_month'];

  //Total amount of application fee received this month
  $sql3 = "SELECT SUM(application_fee) AS total_apply_month FROM loans WHERE release_date >= DATE(NOW()) - INTERVAL 30 DAY";
  $result3 = mysqli_query($conn, $sql3);
  $row3 = mysqli_fetch_assoc($result3);
  $totalApplicationFees = $row3['total_apply_month'];

  // Total amount of repayment made this month
  $sql4 = "SELECT COUNT(*) AS total_repayments_month FROM repayments WHERE collection_date >= DATE(NOW()) - INTERVAL 30 DAY";
  $result4 = mysqli_query($conn, $sql4);
  $row4 = mysqli_fetch_assoc($result4);
  $totalRepaymentCount = $row4['total_repayments_month'];

  // Total repayment made this month
  $sql5 = "SELECT SUM(amount_collected) AS total_recoveries_month FROM repayments WHERE collection_date >= DATE(NOW()) - INTERVAL 30 DAY";
  $result5 = mysqli_query($conn, $sql5);
  $row5 = mysqli_fetch_assoc($result5);
  $totalRepayment = $row5['total_recoveries_month'];


  //Total amount of processing and application fees received this month
  $totalFeesMonth = $totalProcessingFees + $totalApplicationFees;
} elseif ($period == 'Overall') {
  $sql = "SELECT COUNT(*) AS loan_count FROM loans";
  $result = mysqli_query($conn, $sql);
  $row = mysqli_fetch_assoc($result);
  $totalLoanCount = $row['loan_count'];

  $sql1 = "SELECT SUM(amount_approved) AS total_loans FROM loans";
  $result1 = mysqli_query($conn, $sql1);
  $row1 = mysqli_fetch_assoc($result1);
  $totalLoanAmount = $row1['total_loans'];

  //Total amount of processing fee recieved overall
  $sql2 = "SELECT SUM(processing_fee) AS total_process FROM loans";
  $result2 = mysqli_query($conn, $sql2);
  $row2 = mysqli_fetch_assoc($result2);
  $totalProcessingFees = $row2['total_process'];

  //Total amount of application fee received overall
  $sql3 = "SELECT SUM(application_fee) AS total_apply FROM loans";
  $result3 = mysqli_query($conn, $sql3);
  $row3 = mysqli_fetch_assoc($result3);
  $totalApplicationFees = $row3['total_apply'];

  // Total amount of repayment made overall
  $sql4 = "SELECT COUNT(*) AS total_repayments FROM repayments";
  $result4 = mysqli_query($conn, $sql4);
  $row4 = mysqli_fetch_assoc($result4);
  $totalRepaymentCount = $row4['total_repayments'];

  // Total repayment made overall
  $sql5 = "SELECT SUM(amount_collected) AS total_recoveries FROM repayments";
  $result5 = mysqli_query($conn, $sql5);
  $row5 = mysqli_fetch_assoc($result5);
  $totalRepayment = $row5['total_recoveries'];


  //Total amount of processing and application fees received overall
  $totalFees = $totalProcessingFees + $totalApplicationFees;
}

//get borrower groups
$sql = "SELECT COUNT(*) AS total_groups FROM borrower_groups";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$totalGroups = $row['total_groups'];



//GET THE REPAYMENTS FROM SUNDAY TO SATURDAY
$sql = "SELECT DATE(DATE_SUB(NOW(), INTERVAL WEEKDAY(NOW()) DAY)) - INTERVAL 1 DAY AS STARTING_DATE, DATE(DATE_SUB(NOW(), INTERVAL WEEKDAY(NOW()) DAY)) + INTERVAL 5 DAY AS ENDING_DATE";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$startingDate = $row['STARTING_DATE'];
$endingDate = $row['ENDING_DATE'];


$sql = "SELECT SUM(amount_collected) FROM repayments WHERE collection_date BETWEEN '$startingDate' AND '$endingDate'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$weeklyRepayment = $row['SUM(amount_collected)'];


// Expected total repayment for the week
$sql  = "SELECT SUM(weekly_recovery) FROM loans WHERE loan_status = 'Active'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$weeklyRepaymentExpected = $row['SUM(weekly_recovery)'];



//total actual repayments made this month
$starting_month_date = date('Y-m-01');
$ending_month_date = date('Y-m-t');

$actualMonthlyRepayment = ($conn->query("SELECT SUM(amount_collected) AS total_recoveries_month FROM repayments WHERE collection_date BETWEEN '$starting_month_date' AND '$ending_month_date'")->fetch_assoc()['total_recoveries_month'] ?? 0);

// expected total repayment for the month
$monthlyRepaymentExpected = 0;
$sql = "SELECT * FROM loans WHERE loan_status = 'Active'";
$result = $conn->query($sql);
while ($loan = $result->fetch_assoc()) {

  $due_date = $loan['due_date'];
  $weekly_recovery = intval($loan['weekly_recovery']);

  $monthstart = strtotime($starting_month_date);
  $monthend = strtotime($ending_month_date);
  $due_date_time = strtotime($due_date);

  if ($due_date_time >= $monthend) {
    $saturdayCount_not_ending = 0;
    while ($monthstart <= $monthend) {
      if (date('N', $monthstart) == 6) { // 6 represents Saturday as per ISO-8601 standard
        $saturdayCount_not_ending++;
      }
      $monthstart = strtotime('+1 day', $monthstart);
    }
    $monthlyRepaymentExpected += ($weekly_recovery * $saturdayCount_not_ending);
  } else {
    $saturdayCount_ending = 0;
    if (date('N', $due_date_time) == 6) {
      while ($monthstart <= $due_date_time) {
        if (date('N', $monthstart) == 6) { // 6 represents Saturday as per ISO-8601 standard
          $saturdayCount_ending++;
        }
        $monthstart = strtotime('+1 day', $monthstart);
      }
      $monthlyRepaymentExpected += ($weekly_recovery * $saturdayCount_ending);
    } else {
      $saturdayCount_ending = 0;
      while ($monthstart <= $due_date_time) {
        if (date('N', $monthstart) == 6) { // 6 represents Saturday as per ISO-8601 standard
          $saturdayCount_ending++;
        }
        $monthstart = strtotime('+1 day', $monthstart);
      }
      $saturdayCount_ending = $saturdayCount_ending + 1;
      $monthlyRepaymentExpected += ($weekly_recovery * $saturdayCount_ending);
    }
  }
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>NABRE Limited</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">

  <style>

    .container {
      display: flex;
      flex-wrap: wrap;

    }

    .box{
      flex: 1 1 300px;
      margin: 10px;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
      box-shadow: 0 0 5px #ccc;
    }


  </style>

</head>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
      <a href="index.html" class="logo d-flex align-items-center">
        <img src="assets/img/logo.png" alt="">
        <span class="d-none d-lg-block">NiceAdmin</span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->

    <div class="search-bar">
      <form class="search-form d-flex align-items-center" method="POST" action="#">
        <input type="text" name="query" placeholder="Search" title="Enter search keyword">
        <button type="submit" title="Search"><i class="bi bi-search"></i></button>
      </form>
    </div><!-- End Search Bar -->

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">

        <li class="nav-item d-block d-lg-none">
          <a class="nav-link nav-icon search-bar-toggle " href="#">
            <i class="bi bi-search"></i>
          </a>
        </li><!-- End Search Icon-->

        <li class="nav-item dropdown pe-3">

          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <img src="assets/img/profile-img.jpg" alt="Profile" class="rounded-circle">
            <span class="d-none d-md-block dropdown-toggle ps-2">K. Anderson</span>
          </a><!-- End Profile Iamge Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6>Kevin Anderson</h6>
              <span>Web Designer</span>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="users-profile.html">
                <i class="bi bi-person"></i>
                <span>My Profile</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="users-profile.html">
                <i class="bi bi-gear"></i>
                <span>Account Settings</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="pages-faq.html">
                <i class="bi bi-question-circle"></i>
                <span>Need Help?</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="index.php">
                <i class="bi bi-box-arrow-right"></i>
                <span>Sign Out</span>
              </a>
            </li>

          </ul><!-- End Profile Dropdown Items -->
        </li><!-- End Profile Nav -->

      </ul>
    </nav><!-- End Icons Navigation -->

  </header><!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

      <li class="nav-item">
        <a class="nav-link " href="dashboard.php">
          <i class="bi bi-house-fill"></i>
          <span>Dashboard</span>
        </a>
      </li><!-- End Dashboard Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-person-fill"></i><span>Borrowers</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="components-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">

         <li>
            <a href="view-borrowers.php">
              <i class="bi bi-circle"></i><span>View Borrowers</span>
            </a>
          </li>
          <li>
            <a href="add-borrowers.php">
              <i class="bi bi-circle"></i><span>Add Borrowers</span>
            </a>
          </li>
          <li>
            <a href="view-borrower-groups.php">
              <i class="bi bi-circle"></i><span>View Borrower Groups</span>
            </a>
          </li>
          <li>
            <a href="add-borrower-group.php">
              <i class="bi bi-circle"></i><span>Add Borrower Groups</span>
            </a>
          </li>
          <li>
            <a href="#">
              <i class="bi bi-circle"></i><span>Send Emails to all Borrowers</span>
            </a>
          </li>

          <li>
            <a href="#">
              <i class="bi bi-circle"></i><span>Send SMS to all Borrowers</span>
            </a>
        
        </ul>
      </li><!-- End Borrowers Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#forms-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-journal-text"></i><span>Loans</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="forms-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="view-loans.php">
              <i class="bi bi-circle"></i><span>View All Loans</span>
            </a>
          </li>
          <li>
            <a href="add-Loan.php">
              <i class="bi bi-circle"></i><span>Add Loan</span>
            </a>
          </li>
          <li>
          <a href="due-loans.php">
              <i class="bi bi-circle"></i><span>Due Loan</span>
            </a>
          </li>
        </ul>
      </li><!-- End Loans Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#pannel-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-currency-dollar"></i><span>Recoveries</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="pannel-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="view-repayments.php">
              <i class="bi bi-circle"></i><span>View Recoveries</span>
            </a>
          </li>

          <li>
            <a href="add-repayments.php">
              <i class="bi bi-circle"></i><span>Add Recoveries</span>
            </a>
          </li>
          <li>

            <a href="approve-payment.php">
              <i class="bi bi-circle"></i><span>Approve Recoveries</span>
            </a>
          </li>
        </ul>
      </li><!-- End Payments Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#tables-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-bank"></i><span>Savings</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="tables-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="add-savings.php">
              <i class="bi bi-circle"></i><span>Add Savings</span>
            </a>
          </li>
          <li>
            <a href="view-savings.php">
              <i class="bi bi-circle"></i><span>View Savings</span>
            </a>
          </li>
        </ul>
      </li><!-- End Savings Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#panel-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-list-ul"></i><span>Collateral</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="panel-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="register-collateral.php">
              <i class="bi bi-circle"></i><span>Register Collateral</span>
            </a>
          </li>
          <li>
            <a href="view-collaterals.php">
              <i class="bi bi-circle"></i><span>View Collaterals</span>
            </a>
          </li>
        </ul>
      </li><!-- End Colllateral Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#expense-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-reply-fill"></i><span>Expenses</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="expense-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="view-expenses.php">
              <i class="bi bi-circle"></i><span>View Expenses</span>
            </a>
          </li>
          <li>
            <a href="add-expenses.php">
              <i class="bi bi-circle"></i><span>Add Expenses</span>
            </a>
          </li>
        </ul>
      </li><!-- End Expenses Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#charts-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-bar-chart"></i><span>Reports</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="charts-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="view-reports.php">
              <i class="bi bi-circle"></i><span>View Reports</span>
            </a>
          </li>
          <li>
            <a href="generate-reports.php">
              <i class="bi bi-circle"></i><span>Generate Reports</span>
            </a>
          </li>
        </ul>
      </li><!-- End Reports Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#monthly-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-justify"></i><span>Monthly Administration</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="monthly-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="add-monthly-administration.php">
              <i class="bi bi-circle"></i><span>Add Monthly Administration</span>
            </a>
          </li>
          <li>
            <a href="view-monthly-administration.php">
              <i class="bi bi-circle"></i><span>View Monthly Administration</span>
            </a>
          </li>
        </ul>
      </li><!-- End Administrative Cost Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="pages-register.php">
          <i class="bi bi-card-list"></i>
          <span>Register</span>
        </a>
      </li><!-- End Register Page Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="index.php">
          <i class="bi bi-box-arrow-in-right"></i>
          <span>Login</span>
        </a>
      </li><!-- End Login Page Nav -->

    </ul>

  </aside><!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Dashboard</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="row">

        <!-- Left side columns -->
        <div class="col-lg-8">
          <div class="row">
            <div class="container">
            <!-- Sales Card -->
            <div class="col-xxl-4 col-md-6 box">
              <div class="card info-card sales-card">

                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                      <h6>Filter</h6>
                    </li>

                    <li><a class="dropdown-item" href="#amountToday">Today</a></li>
                    <li><a class="dropdown-item" href="#amountOverall">This Month</a></li>
                    <li><a class="dropdown-item" href="#">This Year</a></li>
                  </ul>
                </div>

                <div class="card-body">
                  <h5 class="card-title" id="amountToday">Amount of Loans <span>| Today</span></h5>

                  <div class="d-flex align-items-center">
                     <!--<div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-cart"></i>-->
                    </div>
                    <div class="ps-3">
                      <p style="color: black; font-weight: bold;">Number of Borrowers: <?php echo $loanCount; ?></p>
                      <p style="color: black; font-weight: bold;">Total Loan amount: <?php echo $totalLoansToday; ?></p>
                      <p style="color: black; font-weight: bold;">Total Processing: <?php echo $totalProcessingToday; ?></p>
                      <p style="color: black; font-weight: bold;">Total Application: <?php echo $totalApplicationToday; ?></p>
                      <p style="color: black; font-weight: bold;">Total Fees: <?php echo $totalFeesToday; ?></p>
                      <!--<span class="text-success small pt-1 fw-bold">12%</span> <span class="text-muted small pt-2 ps-1">increase</span>-->

                    </div>
                  </div>
                </div>

              </div>
            </div><!-- End Sales Card -->


            <!-- Sales Card -->
            <div class="col-xxl-4 col-md-6 box">
              <div class="card info-card sales-card">

                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                      <h6>Filter</h6>
                    </li>

                    <li><a class="dropdown-item" href="#">Today</a></li>
                    <li><a class="dropdown-item" href="#">This Month</a></li>
                    <li><a class="dropdown-item" href="#">This Year</a></li>
                  </ul>
                </div>

                <div class="card-body">
                  <h5 class="card-title" id="amountOverall">Amount of Loans <span>| Overall</span></h5>

                  <div class="d-flex align-items-center">
                     <!--<div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-cart"></i>-->
                    </div>
                    <div class="ps-3">
                      <p style="color: black; font-weight: bold;">Number of Borrowers: <?php echo $TotalLoanCount; ?></p>
                      <p style="color: black; font-weight: bold;">Total Loan amount: <?php echo $totalLoans; ?></p>
                      <p style="color: black; font-weight: bold;">Total Processing: <?php echo $totalProcessing; ?></p>
                      <p style="color: black; font-weight: bold;">Total Application: <?php echo $totalApplication; ?></p>
                      <p style="color: black; font-weight: bold;">Total Fees: <?php echo $totalFees; ?></p>
                      <!--<span class="text-success small pt-1 fw-bold">12%</span> <span class="text-muted small pt-2 ps-1">increase</span>-->

                    </div>
                  </div>
                </div>

              </div>
            </div><!-- End Sales Card -->
            </div>

      

                <!-- <div class="card-body">
                  <h5 class="card-title">Reports <span>/Today</span></h5>

                  Line Chart
                  <div id="reportsChart"></div>

                  <script>
                    document.addEventListener("DOMContentLoaded", () => {
                      new ApexCharts(document.querySelector("#reportsChart"), {
                        series: [{
                          name: 'Sales',
                          data: [31, 40, 28, 51, 42, 82, 56],
                        }, {
                          name: 'Revenue',
                          data: [11, 32, 45, 32, 34, 52, 41]
                        }, {
                          name: 'Customers',
                          data: [15, 11, 32, 18, 9, 24, 11]
                        }],
                        chart: {
                          height: 350,
                          type: 'area',
                          toolbar: {
                            show: false
                          },
                        },
                        markers: {
                          size: 4
                        },
                        colors: ['#4154f1', '#2eca6a', '#ff771d'],
                        fill: {
                          type: "gradient",
                          gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.3,
                            opacityTo: 0.4,
                            stops: [0, 90, 100]
                          }
                        },
                        dataLabels: {
                          enabled: false
                        },
                        stroke: {
                          curve: 'smooth',
                          width: 2
                        },
                        xaxis: {
                          type: 'datetime',
                          categories: ["2018-09-19T00:00:00.000Z", "2018-09-19T01:30:00.000Z", "2018-09-19T02:30:00.000Z", "2018-09-19T03:30:00.000Z", "2018-09-19T04:30:00.000Z", "2018-09-19T05:30:00.000Z", "2018-09-19T06:30:00.000Z"]
                        },
                        tooltip: {
                          x: {
                            format: 'dd/MM/yy HH:mm'
                          },
                        }
                      }).render();
                    });
                  </script>
                   End Line Chart

                </div>

              </div>
            </div> End Reports -->

            <!-- Recent Sales -->
            <div class="col-12">
              <div class="card recent-sales overflow-auto">

                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                      <h6>Filter</h6>
                    </li>

                    <li><a class="dropdown-item" href="#">Today</a></li>
                    <li><a class="dropdown-item" href="#">This Month</a></li>
                    <li><a class="dropdown-item" href="#">This Year</a></li>
                  </ul>
                </div>

                
            
          </div>
        </div><!-- End Left side columns -->

        <!-- Right side columns -->
        <div class="col-lg-4">

              </div>

            </div>
          </div><!-- End Recent Activity -->

            </div>
          </div> End News & Updates

        </div> End Right side columns

      </div>
    </section>

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <div class="copyright">
      &copy; Copyright <strong><span>NiceAdmin</span></strong>. All Rights Reserved
    </div>
    <div class="credits">
      Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
    </div>
  </footer><!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/chart.js/chart.umd.js"></script>
  <script src="assets/vendor/echarts/echarts.min.js"></script>
  <script src="assets/vendor/quill/quill.min.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

</body>

</html>