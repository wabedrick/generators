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
<?php include('header_aside.php'); ?>
<!-- Main Content -->
  <main id="main" class="main">

    <!-- align pagetitle on the left and button on the right -->
    <div class="d-flex justify-content-between">
      <div class="pagetitle float-left">
        <h1>Dashboard</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
            <li class="breadcrumb-item active">Dashboard</li>
          </ol>
        </nav>
      </div><!-- End Page Title -->
      <!-- middle div for indicating the period being viewed -->
      <div class="period">
        <h4 class="text-muted"><?php echo $period; ?></h4>
      </div>

      <div class="btn-group btn-group-sm mb-3 float-right" role="group" aria-label="" style="height:35px">
        <a class="btn btn-primary" role="button" href="dashboard.php?view=today">Today</a>
        <a class="btn btn-warning" p-role="button" href="dashboard.php?view=week">Week</a>
        <a class="btn btn-success" role="button" href="dashboard.php?view=month">Month</a>
        <a class="btn btn-primary" role="button" href="dashboard.php?view=overall">Overall</a>
      </div>
    </div>

    <section class="section dashboard">
      <div class="row">

        <!-- Left side columns -->
        <div class="col-lg-12">
          <div class="row">

            <!-- Sales Card -->
            <div class="col-xxl-4 col-md-4">
              <div class="card info-card sales-card">

                <div class="card-body">
                  <h5 class="card-title">Recoveries</h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-people"></i>
                    </div>
                    <div class="ps-3">
                      <h6><?php echo $totalRepaymentCount ?></h6>
                    </div>
                  </div>
                </div>

              </div>
            </div><!-- End Sales Card -->

            <!-- Revenue Card -->
            <div class="col-xxl-4 col-md-4">
              <div class="card info-card revenue-card">

                <div class="card-body">
                  <h5 class="card-title">Loans Disbursed</h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <!-- <span class="fs-4">UGX</span> -->
                      <i class="bi bi-cash"></i>
                    </div>
                    <div class="ps-3">
                      <h6><?php echo $totalLoanCount ?></h6>

                    </div>
                  </div>
                </div>

              </div>
            </div><!-- End Revenue Card -->

            <!-- Revenue Card -->
            <div class="col-xxl-4 col-md-4">
              <div class="card info-card revenue-card">

                <div class="card-body">
                  <h5 class="card-title">Total Loan processing fees</h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center text-warning">
                      <span class="fs-4">UGX</span>
                    </div>
                    <div class="ps-3">
                      <h6><?php echo number_format($totalProcessingFees) ?></h6>
                    </div>
                  </div>
                </div>

              </div>
            </div><!-- End Revenue Card -->

            <!-- Sales Card -->
            <div class="col-xxl-4 col-md-4">
              <div class="card info-card sales-card">

                <div class="card-body">
                  <h5 class="card-title">Total Recovery Amount</h5>
                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-people"></i>
                    </div>
                    <div class="ps-3">
                      <h6><?php echo number_format($totalRepayment); ?></h6>
                    </div>
                  </div>
                </div>

              </div>
            </div><!-- End Sales Card -->

            <!-- Revenue Card -->
            <div class="col-xxl-4 col-md-4">
              <div class="card info-card revenue-card">

                <div class="card-body">
                  <h5 class="card-title">Total Amount disbursed</h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <span class="fs-4">UGX</span>
                    </div>
                    <div class="ps-3">
                      <h6><?php echo number_format($totalLoanAmount) ?></h6>
                    </div>
                  </div>
                </div>

              </div>
            </div><!-- End Revenue Card -->

            <!-- Revenue Card -->
            <div class="col-xxl-4 col-md-4">
              <div class="card info-card revenue-card">

                <div class="card-body">
                  <h5 class="card-title">Total Loan Application Fees</h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center text-warning">
                      <span class="fs-4">UGX</span>
                    </div>
                    <div class="ps-3">
                      <h6><?php echo number_format($totalApplicationFees) ?></h6>
                    </div>
                  </div>
                </div>

              </div>
            </div><!-- End Revenue Card -->

            <!-- Borrower Groups -->
            <div class="col-xxl-4 col-xl-12 mt-3">

              <div class="card info-card customers-card">

                <!-- <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                      <h6>Filter</h6>
                    </li>

                    <li><a class="dropdown-item" href="#">Today</a></li>
                    <li><a class="dropdown-item" href="#">This Month</a></li>
                    <li><a class="dropdown-item" href="#">This Year</a></li>
                  </ul>
                </div> -->

                <div class="card-body">
                  <h5 class="card-title">Borrower Groups</h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-people"></i>
                    </div>
                    <div class="ps-3">
                      <h6><?php echo $totalGroups ?> Borrower Groups</h6>
                    </div>
                  </div>

                </div>
              </div>

            </div>

            <hr class="mt-4" />

            <!-- Weekly analysis -->
            <h3 class="card-title text-center mt-3 mb-4" style="font-size:xx-large"> This Week <span class="text-danger"><?php echo "(" . $startingDate . " to " . $endingDate . ")"; ?></span></h3>
            <!-- Customers Card -->

            <div class="col-xxl-4 col-xl-12">

              <div class="card info-card customers-card">

                <div class="card-body">
                  <h5 class="card-title">Recoveries <span><?php echo $startingDate . " to " . $endingDate; ?></span></h5>

                  <div class="progress">
                    <?php
                    $percentage = round(($actualMonthlyRepayment / $monthlyRepaymentExpected) * 100, 1);
                    ?>
                    <div class="progress-bar" role="progressbar" style="<?php echo 'width: ' . $percentage . '%';  ?>" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"><?php echo $percentage ?></div>
                  </div>
                  <h6 class="text-center text-small mt-2"><?php
                                                          echo number_format($weeklyRepayment);
                                                          ?> / <?php echo  number_format($weeklyRepaymentExpected); ?></h6>
                </div>
              </div>

            </div><!-- End Customers Card -->

            <!-- Reports -->
            <?Php
            $sunday = $startingDate;
            $monday = date('Y-m-d', strtotime($sunday . ' +1 day'));
            $tuesday = date('Y-m-d', strtotime($sunday . ' +2 day'));
            $wednesday = date('Y-m-d', strtotime($sunday . ' +3 day'));
            $thursday = date('Y-m-d', strtotime($sunday . ' +4 day'));
            $friday = date('Y-m-d', strtotime($sunday . ' +5 day'));
            $saturday = date('Y-m-d', strtotime($sunday . ' +6 day'));

            $sundayRepayment = ($conn->query("SELECT SUM(amount_collected) AS sundayRepayment FROM repayments WHERE collection_date = '$sunday'")->fetch_assoc()['sundayRepayment'] ?? 0);
            $mondayRepayment = ($conn->query("SELECT SUM(amount_collected) AS mondayRepayment FROM repayments WHERE collection_date = '$monday'")->fetch_assoc()['mondayRepayment'] ?? 0);
            $tuesdayRepayment = ($conn->query("SELECT SUM(amount_collected) AS tuesdayRepayment FROM repayments WHERE collection_date = '$tuesday'")->fetch_assoc()['tuesdayRepayment'] ?? 0);
            $wednesdayRepayment = ($conn->query("SELECT SUM(amount_collected) AS wednesdayRepayment FROM repayments WHERE collection_date = '$wednesday'")->fetch_assoc()['wednesdayRepayment'] ?? 0);
            $thursdayRepayment = ($conn->query("SELECT SUM(amount_collected) AS thursdayRepayment FROM repayments WHERE collection_date = '$thursday'")->fetch_assoc()['thursdayRepayment'] ?? 0);
            $fridayRepayment = ($conn->query("SELECT SUM(amount_collected) AS fridayRepayment FROM repayments WHERE collection_date = '$friday'")->fetch_assoc()['fridayRepayment'] ?? 0);
            $saturdayRepayment = ($conn->query("SELECT SUM(amount_collected) AS saturdayRepayment FROM repayments WHERE collection_date = '$saturday'")->fetch_assoc()['saturdayRepayment'] ?? 0);
            ?>

            <div class="col-12">
              <div class="card">

                <div class="card-body">
                  <h5 class="card-title">Reports <span>/This week</span></h5>

                  <!-- Line Chart -->
                  <div id="reportsChart"></div>

                  <script>
                    document.addEventListener("DOMContentLoaded", () => {
                      new ApexCharts(document.querySelector("#reportsChart"), {
                        series: [{
                          name: 'Expected loan recoveries',
                          data: [
                            <?php echo $weeklyRepaymentExpected ?>,
                            <?php echo $weeklyRepaymentExpected ?>,
                            <?php echo $weeklyRepaymentExpected ?>,
                            <?php echo $weeklyRepaymentExpected ?>,
                            <?php echo $weeklyRepaymentExpected ?>,
                            <?php echo $weeklyRepaymentExpected ?>,
                            <?php echo $weeklyRepaymentExpected ?>
                          ],
                        }, {
                          name: 'Total loan recoveries',
                          data: [
                            <?php echo $sundayRepayment ?>,
                            <?php echo $sundayRepayment + $mondayRepayment ?>,
                            <?php echo $sundayRepayment + $mondayRepayment + $tuesdayRepayment ?>,
                            <?php echo $sundayRepayment + $mondayRepayment + $tuesdayRepayment + $wednesdayRepayment ?>,
                            <?php echo $sundayRepayment + $mondayRepayment + $tuesdayRepayment + $wednesdayRepayment + $thursdayRepayment ?>,
                            <?php echo $sundayRepayment + $mondayRepayment + $tuesdayRepayment + $wednesdayRepayment + $thursdayRepayment + $fridayRepayment ?>,
                            <?php echo $sundayRepayment + $mondayRepayment + $tuesdayRepayment + $wednesdayRepayment + $thursdayRepayment + $fridayRepayment + $saturdayRepayment ?>

                          ]
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
                        colors: ['#4154f1', '#ff771d'],
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
                          categories: [
                            <?php echo "'" . 'Sun - ' . $sunday . "'"; ?>,
                            <?php echo "'" . 'Mon - ' . $monday . "'"; ?>,
                            <?php echo "'" . 'Tue - ' . $tuesday . "'"; ?>,
                            <?php echo "'" . 'Wed - ' . $wednesday . "'"; ?>,
                            <?php echo "'" . 'Thur - ' . $thursday . "'"; ?>,
                            <?php echo "'" . 'Fri - ' . $friday . "'"; ?>,
                            <?php echo "'" . 'Sat - ' . $saturday . "'"; ?>

                          ]
                        },
                      }).render();
                    });
                  </script>

                  <!-- End Line Chart -->

                </div>

              </div>
            </div><!-- End Reports -->

            <hr class="mt-4">

            <!-- Weekly analysis -->
            <h3 class="card-title text-center mt-3 mb-4" style="font-size:xx-large"> This Month <span class="text-danger"><?php echo "(" . date('F') . ")"; ?></span></h3>

            <!-- Customers Card -->

            <div class="col-xxl-4 col-xl-12">

              <div class="card info-card customers-card">

                <div class="card-body">
                  <h5 class="card-title">Recoveries <span><?php echo "(" . date('F') . ")"; ?></span></h5>

                  <div class="progress">
                    <?php
                    $percentage = round(($actualMonthlyRepayment / $weeklyRepaymentExpected) * 100, 1);
                    ?>
                    <div class="progress-bar" role="progressbar" style="<?php echo 'width: ' . $percentage . '%';  ?>" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"><?php echo $percentage ?></div>
                  </div>
                  <h6 class="text-center text-small mt-2"><?php
                                                          echo number_format($actualMonthlyRepayment);
                                                          ?> / <?php echo  number_format($monthlyRepaymentExpected); ?></h6>
                </div>
              </div>

            </div><!-- End Customers Card -->

            <?php
            $lastDayOfMonth = date('Y-m-t');

            // generate date array from starting date to ending date
            $dateArray = array();
            $date = date("Y-m-01");
            $i = 0;
            while ($date <= $lastDayOfMonth) {
              $dateArray[$i] = $date;
              $date = date("Y-m-d", strtotime("+1 day", strtotime($date)));
              $i++;
            }
            ?>


            <div class="col-12">
              <div class="card">

                <div class="card-body">
                  <h5 class="card-title">Reports <span>/Month</span></h5>



                  <!-- Line Chart -->
                  <div id="monthlyReportsChart"></div>
                  <script>
                    document.addEventListener("DOMContentLoaded", () => {
                      new ApexCharts(document.querySelector("#monthlyReportsChart"), {
                        series: [{
                            name: 'Actual recovery amount',
                            data: [
                              <?php
                              $totalAmount = 0;
                              foreach ($dateArray as $date) {
                                $amount = ($conn->query("SELECT SUM(amount_collected) AS total FROM repayments WHERE collection_date = '$date'")->fetch_assoc()['total'] ?? 0);
                                $totalAmount += $amount;
                                echo $totalAmount . ",";
                              }
                              ?>
                            ],
                          },
                          {
                            name: 'Expected recovery amount',
                            data: [
                              <?php
                              foreach ($dateArray as $date) {
                                echo $monthlyRepaymentExpected . ",";
                              }
                              ?>
                            ],
                          }

                        ],
                        chart: {
                          height: 350,
                          type: 'area',
                          toolbar: {
                            show: false,
                          },
                        },
                        markers: {
                          size: 4,
                        },
                        colors: ['#4154f1', '#ff771d'],
                        fill: {
                          type: 'gradient',
                          gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.3,
                            opacityTo: 0.4,
                            stops: [0, 90, 100],
                          },
                        },
                        dataLabels: {
                          enabled: false,
                        },
                        stroke: {
                          curve: 'smooth',
                          width: 2,
                        },
                        xaxis: {
                          categories: [
                            <?php
                            foreach ($dateArray as $date) {
                              echo "'" . $date . "',";
                            }

                            ?>
                          ],
                        },
                      }).render();
                    });
                  </script>




                  <!-- End Line Chart -->

                </div>

              </div>
            </div><!-- End Reports -->







          </div>
        </div><!-- End Left side columns -->


      </div>
    </section>

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <?php include('footer.php'); ?>