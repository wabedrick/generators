<?php
include 'connection/db_connection.php';
session_start();
if (!isset($_SESSION["username"])) {
  header('location: ../index.php');
}
// total number of sites
$sql1 = "SELECT COUNT(*) AS number_of_sites FROM sites";
$result1 = mysqli_query($conn, $sql1);
$sites = mysqli_fetch_assoc($result1);

// total number of generators
$sql2 = "SELECT COUNT(*) AS number_of_generators FROM generators";
$result2 = mysqli_query($conn, $sql2);
$generators = mysqli_fetch_assoc($result2);

// total number of Active generators
$sql3 = "SELECT COUNT(*) AS number_of_active_generators FROM generators WHERE state='active'";
$result3 = mysqli_query($conn, $sql3);
$active_generators = mysqli_fetch_assoc($result3);

//  Inactive Generators
$sql4 = "SELECT COUNT(*) AS number_of_inactive_generators FROM generators WHERE state='inactive'";
$result4 = mysqli_query($conn, $sql4);
$inactive_generators = mysqli_fetch_assoc($result4);
$inactive_generator = $inactive_generators['number_of_inactive_generators'];


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
      <h4 class="text-muted">Today</h4>
    </div>
  </div>

  <section class="section dashboard">
    <div class="row">

      <!-- Left side columns -->
      <div class="col-lg-12">
        <div class="row">

          <!-- Sites Card -->
          <div class="col-xxl-4 col-md-4">
            <div class="card info-card sales-card">

              <div class="card-body">
                <h5 class="card-title">Total Sites</h5>

                <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-people"></i>
                  </div>
                  <div class="ps-3">
                    <h6><?php echo $sites['number_of_sites']; ?></h6>
                  </div>
                </div>
              </div>

            </div>
          </div><!-- End Sales Card -->

          <!-- Total Generators Card -->
          <div class="col-xxl-4 col-md-4">
            <div class="card info-card revenue-card">

              <div class="card-body">
                <h5 class="card-title">Total Generators</h5>

                <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <!-- <span class="fs-4">UGX</span> -->
                    <i class="bi bi-cash"></i>
                  </div>
                  <div class="ps-3">
                    <h6><?php echo $generators['number_of_generators']; ?></h6>

                  </div>
                </div>
              </div>

            </div>
          </div><!-- End Total Generators Card -->

          <!-- Active Generators Card -->
          <div class="col-xxl-4 col-md-4">
            <div class="card info-card revenue-card">

              <div class="card-body">
                <h5 class="card-title">Active Generators</h5>

                <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center text-warning">
                    <span class="fs-4"></span>
                  </div>
                  <div class="ps-3">
                    <h6><?php echo $active_generators['number_of_active_generators'] ?></h6>
                  </div>
                </div>
              </div>

            </div>
          </div><!-- End Active Generators Card -->

          <!-- Inactive Generators Card -->
          <div class="col-xxl-4 col-md-4">
            <div class="card info-card sales-card">

              <div class="card-body">
                <h5 class="card-title">Inactive Generators</h5>
                <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-people"></i>
                  </div>
                  <div class="ps-3">
                    <h6><?php echo $inactive_generator; ?></h6>
                  </div>
                </div>
              </div>

            </div>
          </div><!-- End Inactive Generators Card -->

          <!-- Revenue Card -->
          <div class="col-xxl-4 col-md-4">
            <div class="card info-card revenue-card">

              <div class="card-body">
                <h5 class="card-title">Total Run Hours</h5>

                <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <span class="fs-4"></span>
                  </div>
                  <div class="ps-3">
                    <h6>250</h6>
                  </div>
                </div>
              </div>

            </div>
          </div><!-- End Revenue Card -->

          <!-- Revenue Card -->
          <div class="col-xxl-4 col-md-4">
            <div class="card info-card revenue-card">

              <div class="card-body">
                <h5 class="card-title">Total Amount to Run</h5>

                <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center text-warning">
                    <span class="fs-4">USD</span>
                  </div>
                  <div class="ps-3">
                    <h6>8,000,000</h6>
                  </div>
                </div>
              </div>

            </div>
          </div><!-- End Revenue Card -->
        </div>
      </div><!-- End Left side columns -->

      <!-- Chart -->
    </div>
    <div class="row">
      <div class="col-lg-6">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Doughnut Chart</h5>

            <!-- Doughnut Chart -->
            <canvas id="doughnutChart" style="max-height: 400px;"></canvas>
            <script>
              document.addEventListener("DOMContentLoaded", () => {
                new Chart(document.querySelector('#doughnutChart'), {
                  type: 'doughnut',
                  data: {
                    labels: [
                      'A',
                      'B',
                      'C',
                      'D',
                      'E',
                      'F',
                      'G'
                    ],
                    datasets: [{
                      label: 'My First Dataset',
                      data: [300, 50, 100, 150, 200, 98, 130],
                      backgroundColor: [
                        'rgb(255, 99, 132)',
                        'rgb(54, 162, 235)',
                        'rgb(255, 205, 86)',
                        'rgb(255, 180, 80)',
                        'rgb(200, 201, 76)',
                        'rgb(145, 170, 109)',
                        'rgb(200, 100, 10)'
                      ],
                      hoverOffset: 4
                    }]
                  }
                });
              });
            </script>
            <!-- End Doughnut CHart -->

          </div>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Column Chart</h5>

            <!-- Column Chart -->
            <div id="columnChart"></div>

            <script>
              document.addEventListener("DOMContentLoaded", () => {
                new ApexCharts(document.querySelector("#columnChart"), {
                  series: [{
                    name: 'Net Profit',
                    data: [44, 55, 57, 56, 61, 58, 63, 60, 66]
                  }, {
                    name: 'Revenue',
                    data: [76, 85, 101, 98, 87, 105, 91, 114, 94]
                  }, {
                    name: 'Free Cash Flow',
                    data: [35, 41, 36, 26, 45, 48, 52, 53, 41]
                  }],
                  chart: {
                    type: 'bar',
                    height: 350
                  },
                  plotOptions: {
                    bar: {
                      horizontal: false,
                      columnWidth: '55%',
                      endingShape: 'rounded'
                    },
                  },
                  dataLabels: {
                    enabled: false
                  },
                  stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                  },
                  xaxis: {
                    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                  },
                  yaxis: {
                    title: {
                      text: '$ (thousands)'
                    }
                  },
                  fill: {
                    opacity: 1
                  },
                  tooltip: {
                    y: {
                      formatter: function(val) {
                        return "$ " + val + " thousands"
                      }
                    }
                  }
                }).render();
              });
            </script>
            <!-- End Column Chart -->

          </div>
        </div>
      </div>

    </div>

  </section>

</main><!-- End #main -->

<!-- ======= Footer ======= -->
<?php include('footer.php'); ?>