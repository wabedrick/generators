<?php

include 'connection/db_connection.php';
session_start();
if (!isset($_SESSION["username"])) {
  header('location: ../index.php');
}

include 'header_aside.php';


// total number of sites
$sql1 = "SELECT COUNT(*) AS number_of_sites FROM sites";
$result1 = mysqli_query($conn, $sql1);
$sites = mysqli_fetch_assoc($result1);
$total_sites = $sites['number_of_sites'];

// total number of Active sites
$active_sql = "SELECT COUNT(*) AS number_of_act_sites FROM sites WHERE site_status='active'";
$active_result = mysqli_query($conn, $active_sql);
$act_sites = mysqli_fetch_assoc($active_result);
$total_act_sites = $act_sites['number_of_act_sites'];

// total number of Active sites with status Up
$active_up_sql = "SELECT COUNT(*) AS number_of_act_up_sites FROM sites WHERE site_status='active' 
AND active_site_status = 'Up'";
$active_up_result = mysqli_query($conn, $active_up_sql);
$act_up_sites = mysqli_fetch_assoc($active_up_result);
$total_act_up_sites = $act_up_sites['number_of_act_up_sites'];

// Total number of active sites with status Down
$total_active_down_sites = $total_act_sites - $total_act_up_sites;

// total number of Inactive sites
$inactive_sql = "SELECT COUNT(*) AS number_of_inact_sites FROM sites WHERE site_status='inactive'";
$inactive_result = mysqli_query($conn, $inactive_sql);
$inact_sites = mysqli_fetch_assoc($inactive_result);
$total_inact_sites = $inact_sites['number_of_inact_sites'];

// NUMBER OF SILES BY THEIR CLASSES
$chart_sql = "SELECT class, COUNT(*) AS number_of_sites_count FROM sites GROUP BY class";
$result = $conn->query($chart_sql);

$labels = [];
$data = [];
$totalCount = 0;

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $totalCount += $row['number_of_sites_count'];
    $data[] = $row;
  }
}

$chartData = [];
foreach ($data as $item) {
  $class = $item['class'];
  $count = $item['number_of_sites_count'];

  // Label with class name and value only
  $chartData[] = [
    'value' => $count,
    'name' => "$class: $count"
  ];
}

$chartDataJson = json_encode($chartData);

// DATA FOR THE SITES UNDER EACH REGION
// Fetch and count the occurrences of each site and it's department from the database
$region_sql = "SELECT department, COUNT(*) AS site_count FROM sites GROUP BY department";
$result = $conn->query($region_sql);

// Prepare data for JavaScript
$departments = [];
$site_counts = [];

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $departments[] = $row['department'];
    $site_counts[] = $row['site_count'];
  }
}

// THE PHP CODE FOR THE STACKED BAR GRAPH CONTENT
// SQL query to get counts for each class and department combination
$chart_sql_one = "SELECT department, class, COUNT(*) AS count 
              FROM sites 
              GROUP BY department, class";
$result_one = $conn->query($chart_sql_one);

$departments = [];
$classes = [];
$data_one = [];

// Process the result
while ($row = $result_one->fetch_assoc()) {
  $department = $row['department'];
  $class = $row['class'];
  $count = $row['count'];

  if (!in_array($department, $departments)) {
    $departments[] = $department;
  }

  if (!in_array($class, $classes)) {
    $classes[] = $class;
  }

  $data_one[$class][$department] = $count;
}

// Prepare data for JavaScript
$chartDataOne = [];
foreach ($classes as $class) {
  $classData = [];
  foreach ($departments as $department) {
    $classData[] = $data_one[$class][$department] ?? 0;
  }
  $chartDataOne[] = [
    'name' => $class,
    'type' => 'bar',
    'stack' => 'total',
    'data' => $classData
  ];
}

$departmentsJsonOne = json_encode($departments);
$chartDataJsonOne = json_encode($chartDataOne);


// THE PHP CODE FOR THE STACKED BAR GRAPH CONTENT FOR PRIORITY AND REGION OR DEPARTMENT
// SQL query to get counts for each class and department combination
$chart_sql_two = "SELECT department, tx_site_type, COUNT(*) AS priority_count 
              FROM sites 
              GROUP BY department, tx_site_type";
$result_two = $conn->query($chart_sql_two);

$departments_two = [];
$priorities = [];
$data_two = [];

// Process the result
while ($row = $result_two->fetch_assoc()) {
  $department_two = $row['department'];
  $priority = $row['tx_site_type'];
  $priority_count = $row['priority_count'];

  if (!in_array($department_two, $departments_two)) {
    $departments_two[] = $department_two;
  }

  if (!in_array($priority, $priorities)) {
    $priorities[] = $priority;
  }

  $data_two[$priority][$department_two] = $priority_count;
}

// Prepare data for JavaScript
$chartDataTwo = [];
foreach ($priorities as $priority) {
  $priorityData = [];
  foreach ($departments_two as $department_two) {
    $priorityData[] = $data_two[$priority][$department_two] ?? 0;
  }
  $chartDataTwo[] = [
    'name' => $priority,
    'type' => 'bar',
    'stack' => 'total',
    'data' => $priorityData
  ];
}

$departmentsJsonTwo = json_encode($departments_two);
$chartDataJsonTwo = json_encode($chartDataTwo);

// Query to get active and inactive counts for each of the last 4 weeks
$chart_sql = "
  SELECT 
    WEEK(created_at) AS week_num,
    COUNT(CASE WHEN site_status = 'active' THEN 1 END) AS active_count,
    COUNT(CASE WHEN site_status = 'inactive' THEN 1 END) AS inactive_count
  FROM sites
  WHERE created_at <= DATE_SUB(NOW(), INTERVAL 4 WEEK)
  GROUP BY week_num
  ORDER BY week_num ASC;
";

$result = $conn->query($chart_sql);

// Prepare data for the chart
$weeks = [];
$activeData = [];
$inactiveData = [];

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $weeks[] = 'Week ' . $row['week_num'];
    $activeData[] = (int)$row['active_count'];
    $inactiveData[] = (int)$row['inactive_count'];
  }
}

// Encode data as JSON for use in JavaScript
$weeksJson = json_encode($weeks);
$activeDataJson = json_encode($activeData);
$inactiveDataJson = json_encode($inactiveData);


// CODE TO FETCH DATA FOR A STACKED BAR CHART FOR THE UP AND DOWN SITE STATUS FOR THE DIFFERENT TIME DIFFERENCES
// Function to fetch site status based on the selected time frame
// Function to fetch active site status
function fetchActiveSiteStatus($timeFrame)
{
  global $conn;

  // Determine interval based on selected time frame
  switch ($timeFrame) {
    case 'last_12_hours':
      $interval = "INTERVAL 12 HOUR";
      break;
    case 'last_24_hours':
      $interval = "INTERVAL 24 HOUR";
      break;
    case 'last_7_days':
      $interval = "INTERVAL 7 DAY";
      break;
    case 'last_4_weeks':
      $interval = "INTERVAL 28 DAY";
      break;
    default:
      $interval = "INTERVAL 24 HOUR";
  }

  // Query to get active site status
  $active_site_status_sql = "
      SELECT 
          DATE_FORMAT(created_at, '%Y-%m-%d %H:00') AS time_period,
          SUM(CASE WHEN active_site_status = 'Up' THEN 1 ELSE 0 END) AS up_count,
          SUM(CASE WHEN active_site_status = 'Down' THEN 1 ELSE 0 END) AS down_count
      FROM sites
      WHERE site_status = 'Active' AND created_at >= NOW() - $interval
      GROUP BY time_period ORDER BY time_period";

  $active_site_status_result = $conn->query($active_site_status_sql);
  $data = [];

  while ($row = $active_site_status_result->fetch_assoc()) {
    $data[] = $row;
  }

  return json_encode($data);
}

$selectedTimeFrame = $_GET['time_frame'] ?? 'last_12_hours';
// header('Content-Type: application/json');
$chartData = fetchActiveSiteStatus($selectedTimeFrame);
// echo $chartData;
// exit;

?>

<style>
  .hover-info {
    /* display: none;
    position: absolute; */
    /* bottom: 100%; */
    /* Position above the card */
    /* left: 90%; */
    transform: translateX(-50%);
    background: rgba(255, 255, 200, 0.8);
    color: black;
    padding: 8px;
    border-radius: 5px;
    white-space: nowrap;
    font-size: 14px;
    text-align: center;
    z-index: 10;
  }

  .active-card:hover .hover-info,
  .hover-info:hover {
    display: flex;
  }

  .down-link {
    color: red;
  }

  .down-link:hover {
    text-decoration: underline;
    color: #ff4d4d;
    font-weight: bold;
  }

  /* Add some custom styles to make the chart container look better */
  .chart-container {
    margin: 0 auto;
    padding: 20px;
    /* background-color: #f4f4f4;  */
    border-radius: 10px;
    /* box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); */
  }

  canvas {
    max-height: 500px;
  }
</style>

<!-- Main Content -->
<main id="main" class="main">
  <!-- align pagetitle on the left and button on the right -->
  <div class="d-flex justify-content-between">
    <div class="pagetitle float-left">
      <h1>Site Dashboard</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
          <li class="breadcrumb-item active">Sites Dashboard</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    <!-- middle div for indicating the period being viewed -->
    <div class="period">
      <h4 class="text-muted">Today</h4>
    </div>

    <!-- Download Charts Button -->
    <button id="downloadChartsBtn" class="btn btn-primary" style="height: 3rem;">Download Charts</button>
  </div>

  <section class="section dashboard">
    <div class="row">
      <!-- Total Number of Sites Card -->
      <div class="col-xxl-3 col-md-3">
        <div class="card info-card revenue-card">

          <div class="card-body">
            <h5 class="card-title fs-6 text-center">Sites Summary</h5>

            <div class="">
              <a href="view-all-sites.php" class="card-link">
                <div class="card align-items-center p-2">
                  <p><span class="fs-6">Total</span>
                  <p><?php echo $total_sites; ?></p>
                  </p>
              </a>
            </div>

            <div class="card align-items-center p-2 position-relative active-card">
              <a href="view-sites-on-status.php?status=active" class="card-link">
                <p><span class="fs-6">Active</span> (<?php echo $total_act_sites; ?>)</p>
                <p>
                  <div class="" style="display: flex;">
                    <p style="margin-right: 12px; color: black;">Up: <?php echo htmlspecialchars($total_act_up_sites); ?>
                      <span>
                        <p>
                          <a href="view-sites-on-status.php?status=active&filter=Down" class="down-link">
                            Down:
                            <?php echo htmlspecialchars($total_active_down_sites); ?>
                          </a>
                        </p>
                      </span>
                    </p>

                  </div>
                </p>
              </a>

              <!-- Floating container for Up and Down values -->

            </div>

            <a href="view-sites-on-status.php?status=inactive" class="card-link">
              <div class="card align-items-center p-2">
                <p><span class="fs-6 text-danger">Inactive</span>
                <p class="text-danger"><?php echo $total_inact_sites; ?></p>
                </p>
              </div>
            </a>

          </div>
        </div>

      </div>
    </div><!-- End Total Number of Sites Card -->

    <div class="col-md-9">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title fs-4 text-center">Active Sites Status</h5>

          <div class="chart-container" style="margin-top: -2rem;">
            <div class="time-select">
              <label for="timeFrame">Select Time Frame:</label>
              <select id="timeFrame" onchange="updateChart()">
                <option value="last_12_hours" selected>Last 12 Hours</option>
                <option value="last_24_hours">Last 24 Hours</option>
                <option value="last_7_days">Last 7 Days</option>
                <option value="last_4_weeks">Last 4 Weeks</option>
              </select>
            </div>
            <div id="activeSitesChart" style="height: 380px;"></div>
          </div>

          <!-- <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script> -->
          <script>
            let chart;

            function fetchChartData(timeFrame) {
              fetch(`dashboard.php?time_frame=${timeFrame}`)
                .then(response => response.json())
                .then(data => {
                  console.log(data); // Check the structure of the data
                  const categories = data.map(item => item.time_period);
                  const upCounts = data.map(item => parseInt(item.up_count));
                  const downCounts = data.map(item => parseInt(item.down_count));

                  const options = {
                    chart: {
                      type: 'bar',
                      height: 500,
                      stacked: true,
                      toolbar: {
                        show: true
                      }
                    },
                    plotOptions: {
                      bar: {
                        horizontal: false,
                        borderRadius: 5
                      }
                    },
                    series: [{
                        name: 'Up Sites',
                        data: upCounts
                      },
                      {
                        name: 'Down Sites',
                        data: downCounts
                      }
                    ],
                    xaxis: {
                      categories: categories,
                      title: {
                        text: 'Time Period'
                      }
                    },
                    yaxis: {
                      title: {
                        text: 'Number of Sites'
                      }
                    },
                    colors: ['#28a745', '#dc3545'],
                    tooltip: {
                      y: {
                        formatter: val => `${val} site(s)`
                      }
                    },
                    legend: {
                      position: 'top'
                    }
                  };

                  if (chart) {
                    chart.updateOptions(options);
                  } else {
                    chart = new ApexCharts(document.querySelector("#activeSitesChart"), options);
                    chart.render();
                  }
                })
                .catch(error => console.error('Error fetching data:', error));
            }

            function updateChart() {
              const timeFrame = document.getElementById('timeFrame').value;
              fetchChartData(timeFrame);
            }

            // Initial chart load
            fetchChartData('last_12_hours');
          </script>

        </div>
      </div>
    </div>


    <div class="col-md">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title fs-4 text-center">Site Classification</h5>

          <!-- Donut Chart -->
          <div id="donutChart" style="min-height: 442px;" class="echart"></div>

          <script>
            document.addEventListener("DOMContentLoaded", () => {
              // Use PHP-generated JSON data
              const chartData = <?php echo $chartDataJson; ?>;

              // Initialize the donut chart
              const chart = echarts.init(document.querySelector("#donutChart"));
              chart.setOption({
                color: ['#FF0000', '#000000', '#A9A9A9', '#87CEEB', '#FFA500', '#008000'],
                tooltip: {
                  trigger: 'item',
                  formatter: '{d}%' // Display the percentage on hover
                },
                legend: {
                  top: '5%',
                  left: 'center'
                },
                series: [{
                  name: 'Site Count',
                  type: 'pie',
                  radius: ['40%', '70%'],
                  avoidLabelOverlap: false,
                  label: {
                    show: true,
                    position: 'inside',
                    formatter: '{b}', // Display class and value inside each section
                    color: '#fff', // Adjust color for readability
                    fontSize: 14
                  },
                  emphasis: {
                    label: {
                      show: true,
                      fontSize: '18',
                      fontWeight: 'bold'
                    }
                  },
                  labelLine: {
                    show: false
                  },
                  data: chartData // Use dynamic data with values only
                }]
              });

              // Add click event listener for navigation
              chart.on('click', function(params) {
                // Redirect to a URL based on the clicked section's class
                const selectedClass = params.data.name.split(':')[0].trim();
                window.location.href = `class-details.php?class=${encodeURIComponent(selectedClass)}`;
              });
            });
          </script>
          <!-- End Donut Chart -->

        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title fs-6 text-center">Departments and Classes</h5>

          <!-- Stacked Bar Chart -->
          <div id="stackedBarChart" style="min-height: 440px;" class="echart"></div>

          <script>
            document.addEventListener("DOMContentLoaded", () => {
              // Use PHP-generated JSON data
              const departments = <?php echo $departmentsJsonOne; ?>;
              const chartData = <?php echo $chartDataJsonOne; ?>;

              // Initialize the stacked bar chart
              const chart = echarts.init(document.querySelector("#stackedBarChart"));
              chart.setOption({
                color: ['#FF0000', '#000000', '#A9A9A9', '#87CEEB', '#FFA500', '#008000'],
                tooltip: {
                  trigger: 'axis',
                  axisPointer: {
                    type: 'shadow'
                  }
                },
                legend: {
                  top: '5%',
                  left: 'center'
                },
                xAxis: {
                  type: 'category',
                  data: departments, // Set department names on x-axis
                  axisLabel: {
                    rotate: 30
                  }
                },
                yAxis: {
                  type: 'value',
                  name: 'Count'
                },
                series: chartData // Use dynamic data for each class
              });

              // Add click event listener for navigation
              // Add click event listener for navigation
              chart.on('click', function(params) {
                // Check if a bar is clicked
                if (params.componentType === 'series') {
                  const selectedDepartment = params.name; // Get the name of the clicked bar (which corresponds to the department)

                  // Redirect to a specific page for that department
                  window.location.href = `department-details.php?department=${encodeURIComponent(selectedDepartment)}`;
                }
              });

            });
          </script>
          <!-- End Stacked Bar Chart -->

        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title fs-6 text-center">Departments and Priorities</h5>

          <!-- Stacked Bar Chart -->
          <div id="stackedBarChartOne" style="min-height: 440px;" class="echart"></div>

          <script>
            document.addEventListener("DOMContentLoaded", () => {
              // Use PHP-generated JSON data
              const departments = <?php echo $departmentsJsonTwo; ?>;
              const chartData = <?php echo $chartDataJsonTwo; ?>;

              // Initialize the stacked bar chart
              const chart = echarts.init(document.querySelector("#stackedBarChartOne"));
              chart.setOption({
                color: ['#FF0000', '#000000', '#A9A9A9', '#87CEEB', '#FFA500', '#008000'], // Custom colors for each series
                tooltip: {
                  trigger: 'axis',
                  axisPointer: {
                    type: 'shadow'
                  }
                },
                legend: {
                  top: '1%',
                  left: 'center',
                  // Add space between legend and chart
                },
                grid: {
                  top: '20%', // Adjust grid top to prevent overlapping with legend
                  left: '3%',
                  right: '4%',
                  bottom: '5%',
                  containLabel: true
                },
                xAxis: {
                  type: 'category',
                  data: departments, // Set department names on x-axis
                  axisLabel: {
                    rotate: 30
                  }
                },
                yAxis: {
                  type: 'value',
                  name: 'Count'
                },
                series: chartData // Use dynamic data for each class
              });

              // Add click event listener for navigation
              chart.on('click', function(params) {
                // Check if a bar is clicked
                if (params.componentType === 'series') {
                  const selectedDepartment = params.name; // Get the name of the clicked bar (which corresponds to the department)

                  // Redirect to a specific page for that department
                  window.location.href = `department-details.php?department=${encodeURIComponent(selectedDepartment)}`;
                }
              });
            });
          </script>
          <!-- End Stacked Bar Chart -->

        </div>
      </div>
    </div>


    </div>

    </div>

  </section>

</main><!-- End #main -->

<script>
  // Download Charts Function
  function downloadChart(chartInstance, fileName) {
    const url = chartInstance.getDataURL({
      type: 'png',
      pixelRatio: 2,
      backgroundColor: '#fff'
    });

    // Create a temporary download link
    const link = document.createElement('a');
    link.href = url;
    link.download = fileName;
    link.click();
  }

  // Initialize Charts and Add Download Functionality
  document.addEventListener("DOMContentLoaded", () => {
    // Initialize Donut Chart
    const donutChart = echarts.init(document.querySelector("#donutChart"));
    donutChart.setOption({
      // Your existing donut chart options
    });

    // Initialize Stacked Bar Charts
    const stackedBarChart = echarts.init(document.querySelector("#stackedBarChart"));
    stackedBarChart.setOption({
      // Your existing stacked bar chart options
    });

    const stackedBarChartOne = echarts.init(document.querySelector("#stackedBarChartOne"));
    stackedBarChartOne.setOption({
      // Your existing stacked bar chart options
    });

    // Download charts on button click
    document.getElementById("downloadChartsBtn").addEventListener("click", () => {
      downloadChart(donutChart, 'site classification.png');
      downloadChart(stackedBarChart, 'departments and classes.png');
      downloadChart(stackedBarChartOne, 'department and priorities.png');
    });
  });
</script>


<!-- ======= Footer ======= -->
<?php include('footer.php'); ?>