<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

?>

<style>
    .hover-info {
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

    /* Chart container styles */
    .chart-container {
        margin: 0 auto;
        padding: 20px;
        border-radius: 10px;
        cursor: pointer;
        transition: transform 0.2s;
    }

    .chart-container:hover {
        transform: scale(1.02);
    }

    canvas {
        max-height: 500px;
    }

    /* Modal styles */
    .chart-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }

    .chart-modal-content {
        background-color: #fff;
        width: 90%;
        height: 85%;
        border-radius: 10px;
        padding: 20px;
        position: relative;
    }

    .chart-modal-close {
        position: absolute;
        top: 10px;
        right: 20px;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
        color: #333;
    }

    .chart-modal-title {
        font-size: 24px;
        margin-bottom: 20px;
        text-align: center;
    }

    .chart-modal-canvas-container {
        width: 100%;
        height: calc(100% - 60px);
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
                    <li class="breadcrumb-item"><a href="grafana-dashboard.php">Home</a></li>
                    <li class="breadcrumb-item active">Sites Dashboard</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->
        <!-- middle div for indicating the period being viewed -->
        <!-- <div class="period">
            <h4 class="text-muted">Today</h4>
        </div> -->

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
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title fs-6 text-center">Active Sites Status</h5>

                            <!-- Add time period selector -->
                            <div class="mb-3">
                                <select id="timeIntervalSelect" class="form-select form-select-sm">
                                    <option value="24 HOUR" selected>Last 24 Hours</option>
                                    <option value="7 DAY">Last 7 Days</option>
                                    <option value="30 DAY">Last 30 Days</option>
                                    <option value="3 MONTH">Last 3 Months</option>
                                    <option value="6 MONTH">Last 6 Months</option>
                                    <option value="1 YEAR">Last 1 Year</option>
                                    <option value="custom">Custom Range</option>
                                </select>

                                <!-- Custom date range selector (hidden by default) -->
                                <div id="customRangeContainer" class="mt-2" style="display: none;">
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <label for="startDate" class="form-label form-label-sm">Start Date/Time</label>
                                            <input type="datetime-local" id="startDate" class="form-control form-control-sm">
                                        </div>
                                        <div class="col-6">
                                            <label for="endDate" class="form-label form-label-sm">End Date/Time</label>
                                            <input type="datetime-local" id="endDate" class="form-control form-control-sm">
                                        </div>
                                        <div class="col-12 mt-2">
                                            <button id="applyCustomRange" class="btn btn-sm btn-primary">Apply</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="chart-container" data-chart-id="statusChart" data-chart-title="Active Sites Status">
                                <div id="chartStatus"></div>
                                <!-- Set fixed height and width for consistent sizing -->
                                <div style="height: 290px; position: relative;">
                                    <canvas id="statusChart" style="cursor: pointer;"></canvas>
                                </div>
                                <div class="expand-hint" style="text-align: center; font-size: 12px; color: #777; margin-top: 5px;">Click chart background to expand</div>
                            </div>

                            <!-- Fullscreen modal for expanded view -->
                            <div class="modal fade" id="fullscreenChartModal" tabindex="-1" aria-labelledby="fullscreenChartModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" style="width: 80%; max-width: 800px;">
                                    <div class="modal-content shadow-lg rounded-3">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title" id="fullscreenChartModalLabel">📊 Active Sites Status</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>

                                        <div class="modal-body bg-light d-flex flex-column">
                                            <!-- Time interval controls -->
                                            <div class="mb-3">
                                                <label for="modalInterval" class="form-label fw-semibold">⏱ Select Time Interval:</label>
                                                <select id="modalInterval" class="form-select w-auto">
                                                    <option value="24 HOUR" selected>Last 24 Hours</option>
                                                    <option value="7 DAY">Last 7 Days</option>
                                                    <option value="30 DAY">Last 30 Days</option>
                                                    <option value="3 MONTH">Last 3 Months</option>
                                                    <option value="6 MONTH">Last 6 Months</option>
                                                    <option value="1 YEAR">Last 1 YEAR</option>
                                                    <option value="custom">Custom Range</option>
                                                </select>

                                                <!-- Custom date range selector for modal (hidden by default) -->
                                                <div id="modalCustomRangeContainer" class="mt-2" style="display: none;">
                                                    <div class="row g-2">
                                                        <div class="col-6">
                                                            <label for="modalStartDate" class="form-label">Start Date/Time</label>
                                                            <input type="datetime-local" id="modalStartDate" class="form-control">
                                                        </div>
                                                        <div class="col-6">
                                                            <label for="modalEndDate" class="form-label">End Date/Time</label>
                                                            <input type="datetime-local" id="modalEndDate" class="form-control">
                                                        </div>
                                                        <div class="col-12 mt-2">
                                                            <button id="modalApplyCustomRange" class="btn btn-primary">Apply</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Chart container with fixed height -->
                                            <div id="fullscreenChartContainer" class="bg-white rounded shadow-sm p-2">
                                                <div style="height: 400px; position: relative;">
                                                    <canvas id="fullscreenStatusChart"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Include jQuery and Chart.js libraries -->
                            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                            <!-- Include the Date adapter for Chart.js -->
                            <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
                            <!-- Include the ChartDataLabels plugin -->
                            <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
                            <!-- Include Bootstrap JS for modal functionality -->
                            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

                            <script>
                                // Ensure scripts are loaded in the correct order
                                document.addEventListener('DOMContentLoaded', function() {
                                    // Function to load scripts in sequence
                                    function loadScriptsSequentially(scripts, callback) {
                                        if (scripts.length === 0) {
                                            if (callback) callback();
                                            return;
                                        }

                                        const script = document.createElement('script');
                                        script.src = scripts[0];
                                        script.onload = function() {
                                            console.log(`Loaded: ${scripts[0]}`);
                                            loadScriptsSequentially(scripts.slice(1), callback);
                                        };
                                        script.onerror = function() {
                                            console.error(`Failed to load: ${scripts[0]}`);
                                            loadScriptsSequentially(scripts.slice(1), callback); // Continue anyway
                                        };
                                        document.head.appendChild(script);
                                    }

                                    // Scripts to load in sequence
                                    const scripts = [
                                        "https://code.jquery.com/jquery-3.6.0.min.js",
                                        "https://cdn.jsdelivr.net/npm/chart.js",
                                        "https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js",
                                        "https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0",
                                        "https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
                                    ];

                                    // Load scripts then initialize chart
                                    loadScriptsSequentially(scripts, initializeChart);

                                    // Initialize chart after scripts are loaded
                                    function initializeChart() {
                                        console.log("All scripts loaded, initializing chart...");

                                        // Check if required libraries are loaded
                                        if (typeof $ === 'undefined') {
                                            console.error("jQuery not loaded!");
                                            return;
                                        }

                                        if (typeof Chart === 'undefined') {
                                            console.error("Chart.js not loaded!");
                                            return;
                                        }

                                        // Wait for ChartDataLabels to load
                                        if (typeof ChartDataLabels === 'undefined') {
                                            console.error("ChartDataLabels plugin not loaded!");
                                            return;
                                        }

                                        let chart = null;
                                        let fullscreenChart = null;
                                        let currentInterval = '24 HOUR'; // Track the current interval
                                        let chartData = null; // Store the latest data
                                        let customStartDate = null;
                                        let customEndDate = null;

                                        // Handle regular interval selector changes
                                        $('#timeIntervalSelect').change(function() {
                                            const selectedValue = $(this).val();

                                            if (selectedValue === 'custom') {
                                                $('#customRangeContainer').show();
                                                // Set default dates (24 hours)
                                                const now = new Date();
                                                const yesterday = new Date(now.getTime() - 24 * 60 * 60 * 1000);

                                                // Format for datetime-local input
                                                $('#startDate').val(formatDateForInput(yesterday));
                                                $('#endDate').val(formatDateForInput(now));
                                            } else {
                                                $('#customRangeContainer').hide();
                                                currentInterval = selectedValue;
                                                updateChart(currentInterval);
                                            }
                                        });

                                        // Handle custom range application
                                        $('#applyCustomRange').click(function() {
                                            const startDate = $('#startDate').val();
                                            const endDate = $('#endDate').val();

                                            if (!startDate || !endDate) {
                                                alert('Please specify both start and end dates.');
                                                return;
                                            }

                                            customStartDate = new Date(startDate);
                                            customEndDate = new Date(endDate);

                                            // Validate dates
                                            if (customEndDate <= customStartDate) {
                                                alert('End date must be after start date.');
                                                return;
                                            }

                                            updateChartCustomRange(customStartDate, customEndDate);
                                        });

                                        // Handle modal interval selector changes
                                        $('#modalInterval').change(function() {
                                            const selectedValue = $(this).val();

                                            if (selectedValue === 'custom') {
                                                $('#modalCustomRangeContainer').show();
                                                // Set default dates (24 hours)
                                                const now = new Date();
                                                const yesterday = new Date(now.getTime() - 24 * 60 * 60 * 1000);

                                                $('#modalStartDate').val(formatDateForInput(yesterday));
                                                $('#modalEndDate').val(formatDateForInput(now));
                                            } else {
                                                $('#modalCustomRangeContainer').hide();
                                                currentInterval = selectedValue;
                                                updateFullscreenChart(currentInterval);
                                            }
                                        });

                                        // Handle modal custom range application
                                        $('#modalApplyCustomRange').click(function() {
                                            const startDate = $('#modalStartDate').val();
                                            const endDate = $('#modalEndDate').val();

                                            if (!startDate || !endDate) {
                                                alert('Please specify both start and end dates.');
                                                return;
                                            }

                                            customStartDate = new Date(startDate);
                                            customEndDate = new Date(endDate);

                                            // Validate dates
                                            if (customEndDate <= customStartDate) {
                                                alert('End date must be after start date.');
                                                return;
                                            }

                                            updateFullscreenChartCustomRange(customStartDate, customEndDate);
                                        });

                                        // Helper function to format date for input
                                        function formatDateForInput(date) {
                                            return date.toISOString().slice(0, 16);
                                        }

                                        // Function to fetch data and update the main chart with predefined interval
                                        function updateChart(interval) {
                                            $('#chartStatus').text('Loading...').show();
                                            currentInterval = interval; // Store current interval for click handler

                                            // Create mock data for testing
                                            const mockData = createMockData(interval);
                                            processChartData(mockData);

                                            // Uncomment this when your PHP script is ready:
                                            /*
                                            $.ajax({
                                                url: 'fetch_active_site_data.php',
                                                method: 'POST',
                                                data: {
                                                    interval: interval,
                                                    type: 'time_series'
                                                },
                                                dataType: 'json',
                                                success: function(data) {
                                                    processChartData(data);
                                                },
                                                error: function(xhr, status, error) {
                                                    $('#chartStatus').text('AJAX Error: ' + error).show();
                                                    console.error("AJAX Error:", xhr.responseText);
                                                    
                                                    // Try to parse and display the response for debugging
                                                    try {
                                                        const response = JSON.parse(xhr.responseText);
                                                        console.log("Parsed response:", response);
                                                    } catch (e) {
                                                        console.error("Couldn't parse response:", e);
                                                    }
                                                }
                                            });
                                            */
                                        }

                                        // Create mock data for testing
                                        function createMockData(interval) {
                                            const now = new Date();
                                            const timePoints = [];
                                            let numPoints = 24;
                                            let timeStep = 60 * 60 * 1000; // 1 hour in milliseconds

                                            // Adjust number of points and time step based on interval
                                            if (interval.includes('DAY')) {
                                                const days = parseInt(interval);
                                                if (days >= 7) {
                                                    numPoints = days;
                                                    timeStep = 24 * 60 * 60 * 1000; // 1 day
                                                }
                                            }

                                            // Generate time points
                                            for (let i = numPoints - 1; i >= 0; i--) {
                                                const timestamp = new Date(now.getTime() - (i * timeStep));
                                                const upCount = Math.floor(Math.random() * 30) + 40; // 40-70 up sites
                                                const downCount = Math.floor(Math.random() * 10) + 1; // 1-10 down sites

                                                timePoints.push({
                                                    timestamp: timestamp.toISOString(),
                                                    up_count: upCount,
                                                    down_count: downCount
                                                });
                                            }

                                            return {
                                                interval: interval,
                                                time_series: timePoints
                                            };
                                        }

                                        // Function to fetch data and update the main chart with custom date range
                                        function updateChartCustomRange(startDate, endDate) {
                                            $('#chartStatus').text('Loading...').show();

                                            // Use mock data for testing
                                            const mockData = createMockDataForRange(startDate, endDate);
                                            processChartData(mockData);

                                            // Uncomment this when your PHP script is ready:

                                            $.ajax({
                                                url: 'fetch_active_site_data.php',
                                                method: 'POST',
                                                data: {
                                                    type: 'custom_range',
                                                    start_date: startDate.toISOString(),
                                                    end_date: endDate.toISOString()
                                                },
                                                dataType: 'json',
                                                success: function(data) {
                                                    processChartData(data);
                                                },
                                                error: function(xhr, status, error) {
                                                    $('#chartStatus').text('AJAX Error: ' + error).show();
                                                    console.error("AJAX Error:", xhr.responseText);
                                                }
                                            });

                                        }

                                        // Create mock data for custom date range
                                        function createMockDataForRange(startDate, endDate) {
                                            const diffMs = endDate - startDate;
                                            const diffDays = diffMs / (1000 * 60 * 60 * 24);

                                            let timeStep, numPoints;

                                            if (diffDays <= 2) {
                                                // For 1-2 days, do hourly points
                                                timeStep = 60 * 60 * 1000; // 1 hour
                                                numPoints = Math.ceil(diffMs / timeStep);
                                            } else if (diffDays <= 30) {
                                                // For up to 30 days, do daily points
                                                timeStep = 24 * 60 * 60 * 1000; // 1 day
                                                numPoints = Math.ceil(diffMs / timeStep);
                                            } else {
                                                // For longer periods, do weekly points
                                                timeStep = 7 * 24 * 60 * 60 * 1000; // 1 week
                                                numPoints = Math.ceil(diffMs / timeStep);
                                            }

                                            // Limit to a reasonable number of points
                                            numPoints = Math.min(numPoints, 50);
                                            timeStep = diffMs / numPoints;

                                            const timePoints = [];
                                            for (let i = 0; i < numPoints; i++) {
                                                const timestamp = new Date(startDate.getTime() + (i * timeStep));
                                                timePoints.push({
                                                    timestamp: timestamp.toISOString(),
                                                    up_count: Math.floor(Math.random() * 30) + 40, // 40-70 up sites
                                                    down_count: Math.floor(Math.random() * 10) + 1 // 1-10 down sites
                                                });
                                            }

                                            return {
                                                time_series: timePoints
                                            };
                                        }

                                        // Process and display chart data
                                        function processChartData(data) {
                                            try {
                                                // Check if there's an error in the response
                                                if (data.error) {
                                                    $('#chartStatus').text('Error: ' + data.error).show();
                                                    return;
                                                }

                                                // Convert single data point to time series if needed
                                                if (!data.time_series && data.up_count !== undefined && data.down_count !== undefined) {
                                                    // Create a single data point time series
                                                    data = {
                                                        time_series: [{
                                                            timestamp: new Date().toISOString(),
                                                            up_count: data.up_count,
                                                            down_count: data.down_count
                                                        }],
                                                        interval: currentInterval
                                                    };
                                                }

                                                // Check if data has the expected properties
                                                if (!data.time_series || !Array.isArray(data.time_series)) {
                                                    $('#chartStatus').text('Error: Invalid data format received').show();
                                                    console.error("Invalid data format:", data);
                                                    return;
                                                }

                                                // Store the data for later use
                                                chartData = data;

                                                // Hide status message
                                                $('#chartStatus').hide();

                                                // Regular chart update
                                                if (chart) {
                                                    chart.destroy();
                                                }

                                                chart = createTimeSeriesChart('statusChart', data, false);
                                            } catch (e) {
                                                $('#chartStatus').text('Error processing data: ' + e.message).show();
                                                console.error("Error processing data:", e, data);
                                            }
                                        }

                                        // Create time series chart
                                        function createTimeSeriesChart(elementId, data, isFullscreen) {
                                            console.log("Creating chart with data:", data);

                                            const ctx = document.getElementById(elementId).getContext('2d');

                                            // Extract time series data
                                            const timeLabels = data.time_series.map(item => new Date(item.timestamp));
                                            const upCounts = data.time_series.map(item => item.up_count);
                                            const downCounts = data.time_series.map(item => item.down_count);

                                            // Check timeLabels are valid dates
                                            if (timeLabels.some(date => isNaN(date.getTime()))) {
                                                console.error("Invalid date found in timeLabels:", timeLabels);
                                                $('#chartStatus').text('Error: Invalid date format in data').show();
                                                return null;
                                            }

                                            // Register the chart.js plugins
                                            Chart.register(ChartDataLabels);

                                            // Determine time unit based on data range
                                            const timeUnit = determineTimeUnit(data.interval || getTimeSpan(customStartDate, customEndDate));
                                            console.log("Using time unit:", timeUnit);

                                            const chartInstance = new Chart(ctx, {
                                                type: 'bar',
                                                data: {
                                                    labels: timeLabels,
                                                    datasets: [{
                                                            label: 'Up Sites',
                                                            data: upCounts,
                                                            backgroundColor: 'green',
                                                            borderColor: 'green',
                                                            borderWidth: 1,
                                                            order: 2
                                                        },
                                                        {
                                                            label: 'Down Sites',
                                                            data: downCounts,
                                                            backgroundColor: 'rgba(255, 0, 0, 1)',
                                                            borderColor: 'rgba(255, 0, 0, 1)',
                                                            borderWidth: 1,
                                                            order: 1
                                                        }
                                                    ]
                                                },
                                                options: {
                                                    responsive: true,
                                                    maintainAspectRatio: false,
                                                    layout: {
                                                        padding: {
                                                            //     top: 10,
                                                            //     right: 10,
                                                            //     bottom: 10,
                                                            //     left: 10
                                                        }
                                                    },
                                                    scales: {
                                                        x: {
                                                            type: 'time',
                                                            time: {
                                                                unit: timeUnit,
                                                                displayFormats: {
                                                                    hour: 'HH:mm',
                                                                    day: 'MMM d',
                                                                    week: 'MMM d',
                                                                    month: 'MMM yyyy'
                                                                }
                                                            },
                                                            stacked: true,
                                                            title: {
                                                                display: isFullscreen,
                                                                text: 'Time'
                                                            }
                                                        },
                                                        y: {
                                                            stacked: false,
                                                            beginAtZero: true,
                                                            title: {
                                                                display: isFullscreen,
                                                                text: 'Number of Sites'
                                                            },
                                                            ticks: {
                                                                precision: 0 // Only show whole numbers
                                                            }
                                                        }
                                                    },
                                                    plugins: {
                                                        legend: {
                                                            display: true,
                                                            position: isFullscreen ? 'top' : 'bottom',
                                                            labels: {
                                                                boxWidth: isFullscreen ? 20 : 10,
                                                                font: {
                                                                    size: isFullscreen ? 12 : 9
                                                                }
                                                            }
                                                        },
                                                        tooltip: {
                                                            callbacks: {
                                                                title: function(tooltipItems) {
                                                                    const date = new Date(tooltipItems[0].parsed.x);
                                                                    if (timeUnit === 'hour') {
                                                                        return date.toLocaleString();
                                                                    } else if (timeUnit === 'day') {
                                                                        return date.toLocaleDateString();
                                                                    } else if (timeUnit === 'week') {
                                                                        const endDate = new Date(date);
                                                                        endDate.setDate(endDate.getDate() + 6);
                                                                        return `Week of ${date.toLocaleDateString()} - ${endDate.toLocaleDateString()}`;
                                                                    } else {
                                                                        return date.toLocaleDateString(undefined, {
                                                                            year: 'numeric',
                                                                            month: 'long'
                                                                        });
                                                                    }
                                                                }
                                                            }
                                                        },
                                                        datalabels: {
                                                            display: isFullscreen,
                                                            anchor: 'end',
                                                            align: 'top',
                                                            formatter: function(value) {
                                                                return value > 0 ? value : '';
                                                            },
                                                            color: function(context) {
                                                                return context.dataset.borderColor;
                                                            },
                                                            font: {
                                                                weight: 'bold',
                                                                size: isFullscreen ? 12 : 9
                                                            }
                                                        }
                                                    },
                                                    onClick: isFullscreen ? null : handleChartClick
                                                }
                                            });

                                            console.log("Chart created successfully");
                                            return chartInstance;
                                        }

                                        // Determine time unit based on interval
                                        function determineTimeUnit(interval) {
                                            if (!interval) return 'hour';

                                            if (typeof interval === 'string') {
                                                if (interval.includes('HOUR') || interval.includes('hour')) {
                                                    return 'hour';
                                                } else if (interval.includes('DAY') && parseInt(interval) < 7) {
                                                    return 'hour';
                                                } else if (interval.includes('DAY') && parseInt(interval) <= 30) {
                                                    return 'day';
                                                } else if (interval.includes('MONTH') && parseInt(interval) <= 3) {
                                                    return 'week';
                                                } else {
                                                    return 'month';
                                                }
                                            } else {
                                                // For millisecond differences
                                                const days = interval / (1000 * 60 * 60 * 24);
                                                if (days <= 2) {
                                                    return 'hour';
                                                } else if (days <= 30) {
                                                    return 'day';
                                                } else if (days <= 90) {
                                                    return 'week';
                                                } else {
                                                    return 'month';
                                                }
                                            }
                                        }

                                        // Calculate time span between two dates in milliseconds
                                        function getTimeSpan(startDate, endDate) {
                                            if (!startDate || !endDate) return 0;
                                            return endDate.getTime() - startDate.getTime();
                                        }

                                        // Function to update the fullscreen chart with predefined interval
                                        function updateFullscreenChart(interval) {
                                            // First destroy any existing fullscreen chart
                                            if (fullscreenChart) {
                                                fullscreenChart.destroy();
                                                fullscreenChart = null;
                                            }

                                            // If we already have data for this interval, just recreate the chart
                                            if (interval === currentInterval && chartData) {
                                                fullscreenChart = createTimeSeriesChart('fullscreenStatusChart', chartData, true);
                                                return;
                                            }

                                            // Otherwise create mock data for testing
                                            const mockData = createMockData(interval);
                                            fullscreenChart = createTimeSeriesChart('fullscreenStatusChart', mockData, true);

                                            // Uncomment when PHP script is ready:

                                            $.ajax({
                                                url: 'fetch_active_site_data.php',
                                                method: 'POST',
                                                data: {
                                                    interval: interval,
                                                    type: 'time_series'
                                                },
                                                dataType: 'json',
                                                success: function(data) {
                                                    try {
                                                        if (data.error || !data.time_series) {
                                                            console.error("Error or invalid data:", data);
                                                            return;
                                                        }

                                                        // Store the new data and interval
                                                        chartData = data;
                                                        currentInterval = interval;

                                                        // Create the fullscreen chart
                                                        fullscreenChart = createTimeSeriesChart('fullscreenStatusChart', data, true);
                                                    } catch (e) {
                                                        console.error("Error processing fullscreen chart data:", e);
                                                    }
                                                },
                                                error: function(xhr, status, error) {
                                                    console.error("AJAX Error for fullscreen chart:", xhr.responseText);
                                                }
                                            });

                                        }

                                        // Function to update the fullscreen chart with custom date range
                                        function updateFullscreenChartCustomRange(startDate, endDate) {
                                            // First destroy any existing fullscreen chart
                                            if (fullscreenChart) {
                                                fullscreenChart.destroy();
                                                fullscreenChart = null;
                                            }

                                            // Create mock data for testing
                                            const mockData = createMockDataForRange(startDate, endDate);
                                            fullscreenChart = createTimeSeriesChart('fullscreenStatusChart', mockData, true);

                                            // Uncomment when PHP script is ready:

                                            $.ajax({
                                                url: 'fetch_active_site_data.php',
                                                method: 'POST',
                                                data: {
                                                    type: 'custom_range',
                                                    start_date: startDate.toISOString(),
                                                    end_date: endDate.toISOString()
                                                },
                                                dataType: 'json',
                                                success: function(data) {
                                                    try {
                                                        if (data.error || !data.time_series) {
                                                            console.error("Error or invalid data:", data);
                                                            return;
                                                        }

                                                        // Store the new data
                                                        chartData = data;

                                                        // Create the fullscreen chart
                                                        fullscreenChart = createTimeSeriesChart('fullscreenStatusChart', data, true);
                                                    } catch (e) {
                                                        console.error("Error processing fullscreen chart data:", e);
                                                    }
                                                },
                                                error: function(xhr, status, error) {
                                                    console.error("AJAX Error for fullscreen chart:", xhr.responseText);
                                                }
                                            });

                                        }

                                        // Handle chart click event - opens fullscreen modal
                                        function handleChartClick(event, elements) {
                                            // Simply open the modal when clicking anywhere on the chart
                                            openFullscreenModal();
                                        }

                                        // Function to open fullscreen modal
                                        function openFullscreenModal() {
                                            // Set the modal interval selector to match current interval
                                            $('#modalInterval').val($('#timeIntervalSelect').val());

                                            // If custom range is selected, copy the date values
                                            if ($('#timeIntervalSelect').val() === 'custom') {
                                                $('#modalCustomRangeContainer').show();
                                                $('#modalStartDate').val($('#startDate').val());
                                                $('#modalEndDate').val($('#endDate').val());
                                            } else {
                                                $('#modalCustomRangeContainer').hide();
                                            }

                                            // Show the modal
                                            $('#fullscreenChartModal').modal('show');
                                        }

                                        // Initialize fullscreen modal events
                                        $('#fullscreenChartModal').on('shown.bs.modal', function() {
                                            // Create the fullscreen chart when modal is fully shown
                                            if ($('#modalInterval').val() === 'custom' && customStartDate && customEndDate) {
                                                updateFullscreenChartCustomRange(customStartDate, customEndDate);
                                            } else {
                                                updateFullscreenChart($('#modalInterval').val());
                                            }

                                            // Force consistent sizing of the modal
                                            $(this).find('.modal-dialog').css({
                                                'display': 'flex',
                                                'align-items': 'center',
                                                'justify-content': 'center'
                                            });
                                        });

                                        // Clean up fullscreen chart when modal is hidden
                                        $('#fullscreenChartModal').on('hidden.bs.modal', function() {
                                            if (fullscreenChart) {
                                                fullscreenChart.destroy();
                                                fullscreenChart = null;
                                            }
                                        });

                                        // Initialize with default interval
                                        updateChart('24 HOUR');
                                    }
                                });
                            </script>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title fs-6 text-center">Site Classification</h5>

                            <!-- Donut Chart -->
                            <div id="donutChart" style="height: 150px; width: 100%; padding: 0; margin: 0;" class="echart chart-container" data-chart-id="donutChart" data-chart-title="Site Classification"></div>

                            <script>
                                document.addEventListener("DOMContentLoaded", () => {
                                    // Use PHP-generated JSON data
                                    const chartData = <?php echo $chartDataJson; ?>;

                                    // Track if chart is in expanded view
                                    let isExpanded = false;
                                    let isFullscreen = false; // Track if chart is in fullscreen mode

                                    if (isExpanded == true) {
                                        isFullscreen = true;
                                    }

                                    // Initialize the donut chart
                                    const chart = echarts.init(document.querySelector("#donutChart"));

                                    // Function to update chart options based on view mode
                                    function updateChartOptions() {
                                        chart.setOption({
                                            color: ['#FF0000', '#000000', '#A9A9A9', '#87CEEB', '#FFA500', '#008000'],
                                            tooltip: {
                                                trigger: 'item',
                                                formatter: '{d}%' // Display the percentage on hover
                                            },
                                            legend: {
                                                top: '5%',
                                                left: 'center',
                                                show: isExpanded // Only show legend when expanded
                                            },
                                            series: [{
                                                name: 'Site Count',
                                                type: 'pie',
                                                radius: '100%',
                                                avoidLabelOverlap: false,
                                                size: 500,
                                                label: {
                                                    // position: 'inside',
                                                    // formatter: '{b}', // Display class and value inside each section
                                                    // color: '#fff', // Adjust color for readability
                                                    // fontSize: 6

                                                    position: 'inside',
                                                    formatter: isFullscreen ? '{b}: {d}%' : '{b}', // Show class name and percentage
                                                    fontSize: 10,
                                                    color: '#fff'
                                                },
                                                emphasis: {
                                                    label: {
                                                        show: false,
                                                        fontSize: '12',
                                                        fontWeight: 'bold'
                                                    }
                                                },
                                                labelLine: {
                                                    show: true
                                                },
                                                data: chartData // Use dynamic data with values only
                                            }]
                                        });
                                    }

                                    // Initial render in compact mode
                                    updateChartOptions();

                                    // Add click handler for chart expansion
                                    document.querySelector("#donutChart").addEventListener("click", function(e) {
                                        // Don't trigger navigation when clicking the chart container
                                        if (e.target === this) {
                                            expandChart();
                                            e.stopPropagation();
                                        }
                                    });

                                    // Function to handle chart expansion
                                    function expandChart() {
                                        // Get the chart container
                                        const chartContainer = document.querySelector("#donutChart");

                                        // Toggle expanded state
                                        isExpanded = !isExpanded;

                                        if (isExpanded) {
                                            // Save original position and size
                                            chartContainer.dataset.originalStyle = chartContainer.getAttribute('style') || '';

                                            // Create overlay for full-screen effect
                                            const overlay = document.createElement('div');
                                            overlay.id = 'chartOverlay';
                                            overlay.style.position = 'fixed';
                                            overlay.style.top = '0';
                                            overlay.style.left = '0';
                                            overlay.style.width = '100%';
                                            overlay.style.height = '100%';
                                            overlay.style.backgroundColor = 'rgba(0, 0, 0, 0.7)';
                                            overlay.style.zIndex = '1000';
                                            overlay.style.display = 'flex';
                                            overlay.style.justifyContent = 'center';
                                            overlay.style.alignItems = 'center';

                                            // Create close button
                                            const closeBtn = document.createElement('button');
                                            closeBtn.innerText = '×';
                                            closeBtn.style.position = 'absolute';
                                            closeBtn.style.top = '20px';
                                            closeBtn.style.right = '20px';
                                            closeBtn.style.fontSize = '24px';
                                            closeBtn.style.background = 'none';
                                            closeBtn.style.border = 'none';
                                            closeBtn.style.color = 'white';
                                            closeBtn.style.cursor = 'pointer';
                                            closeBtn.style.zIndex = '1002';

                                            closeBtn.onclick = function() {
                                                collapseChart();
                                            };

                                            // Create expanded chart container
                                            const expandedContainer = document.createElement('div');
                                            expandedContainer.id = 'expandedChart';
                                            expandedContainer.style.width = '80%';
                                            expandedContainer.style.height = '80%';
                                            expandedContainer.style.backgroundColor = 'white';
                                            expandedContainer.style.padding = '20px';
                                            expandedContainer.style.borderRadius = '5px';
                                            expandedContainer.style.zIndex = '1001';

                                            // Move chart to expanded container
                                            overlay.appendChild(expandedContainer);
                                            overlay.appendChild(closeBtn);
                                            document.body.appendChild(overlay);

                                            // Initialize new chart in expanded view
                                            const expandedChart = echarts.init(expandedContainer);
                                            expandedChart.setOption(chart.getOption());
                                            expandedChart.resize();

                                            // Show the legend in expanded view and enhance labels
                                            expandedChart.setOption({
                                                legend: {
                                                    show: true,
                                                    textStyle: {
                                                        fontSize: 14
                                                    }
                                                },
                                                series: [{
                                                    label: {
                                                        fontSize: 14,
                                                        formatter: '{b}: {d}%' // Show more detailed labels in expanded view
                                                    },
                                                    emphasis: {
                                                        label: {
                                                            show: true,
                                                            fontSize: 16,
                                                            fontWeight: 'bold'
                                                        }
                                                    }
                                                }]
                                            });

                                            // Add click event listener for navigation on expanded chart
                                            expandedChart.on('click', function(params) {
                                                if (params.componentType === 'series') {
                                                    const selectedClass = params.data.name.split(':')[0].trim();
                                                    window.location.href = `class-details.php?class=${encodeURIComponent(selectedClass)}`;
                                                }
                                            });

                                            // Close expanded view when clicking outside the chart
                                            overlay.addEventListener('click', function(e) {
                                                if (e.target === overlay) {
                                                    collapseChart();
                                                }
                                            });
                                        }
                                    }

                                    // Function to collapse the expanded chart
                                    function collapseChart() {
                                        const overlay = document.getElementById('chartOverlay');
                                        if (overlay) {
                                            document.body.removeChild(overlay);
                                        }

                                        isExpanded = false;
                                        updateChartOptions();
                                        chart.resize();
                                    }

                                    // Add click event listener for navigation (only on compact view)
                                    chart.on('click', function(params) {
                                        // Check if modal is open
                                        const isModalOpen = false; // Replace with your actual modal state check

                                        if (!isExpanded && !isModalOpen && params.componentType === 'series') {
                                            const selectedClass = params.data.name.split(':')[0].trim();
                                            window.location.href = `class-details.php?class=${encodeURIComponent(selectedClass)}`;
                                        }
                                    });

                                    // Handle window resize
                                    window.addEventListener('resize', function() {
                                        chart.resize();

                                        // Resize expanded chart if it exists
                                        const expandedContainer = document.getElementById('expandedChart');
                                        if (expandedContainer) {
                                            const expandedChart = echarts.getInstanceByDom(expandedContainer);
                                            if (expandedChart) {
                                                expandedChart.resize();
                                            }
                                        }
                                    });
                                });
                            </script>
                            <!-- End Donut Chart -->

                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title fs-6 text-center">Departments and Classes</h5>

                            <!-- Stacked Bar Chart -->
                            <div id="stackedBarChart" style="height: 130px; width: 100%; padding: 0; margin: 0;" class="echart chart-container" data-chart-id="stackedBarChart" data-chart-title="Departments and Classes"></div>

                            <script>
                                document.addEventListener("DOMContentLoaded", () => {
                                    // Use PHP-generated JSON data
                                    const departments = <?php echo $departmentsJsonOne; ?>;
                                    const chartData = <?php echo $chartDataJsonOne; ?>;

                                    // Track if chart is in expanded view
                                    let isExpanded = false;

                                    // Initialize the stacked bar chart
                                    const chart = echarts.init(document.querySelector("#stackedBarChart"));

                                    // Function to update chart options based on view mode
                                    function updateChartOptions() {
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
                                                left: 'center',
                                                show: isExpanded // Only show legend when expanded
                                            },
                                            grid: {
                                                top: isExpanded ? '20%' : '10%', // More space for legend when expanded
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
                                                name: 'Count',
                                                min: 0,
                                                interval: 200,
                                                max: 800,
                                                axisLabel: {
                                                    formatter: '{value}'
                                                }
                                            },
                                            series: chartData // Use dynamic data for each class
                                        });
                                    }

                                    // Initial render in compact mode
                                    updateChartOptions();

                                    // Add click handler for chart expansion
                                    document.querySelector("#stackedBarChart").addEventListener("click", function(e) {
                                        // Don't trigger navigation when clicking the chart container
                                        if (e.target === this) {
                                            expandChart();
                                            e.stopPropagation();
                                        }
                                    });

                                    // Function to handle chart expansion
                                    function expandChart() {
                                        // Get the chart container
                                        const chartContainer = document.querySelector("#stackedBarChart");

                                        // Toggle expanded state
                                        isExpanded = !isExpanded;

                                        if (isExpanded) {
                                            // Save original position and size
                                            chartContainer.dataset.originalStyle = chartContainer.getAttribute('style') || '';

                                            // Create overlay for full-screen effect
                                            const overlay = document.createElement('div');
                                            overlay.id = 'chartOverlay';
                                            overlay.style.position = 'fixed';
                                            overlay.style.top = '0';
                                            overlay.style.left = '0';
                                            overlay.style.width = '100%';
                                            overlay.style.height = '100%';
                                            overlay.style.backgroundColor = 'rgba(0, 0, 0, 0.7)';
                                            overlay.style.zIndex = '1000';
                                            overlay.style.display = 'flex';
                                            overlay.style.justifyContent = 'center';
                                            overlay.style.alignItems = 'center';

                                            // Create close button
                                            const closeBtn = document.createElement('button');
                                            closeBtn.innerText = '×';
                                            closeBtn.style.position = 'absolute';
                                            closeBtn.style.top = '20px';
                                            closeBtn.style.right = '20px';
                                            closeBtn.style.fontSize = '24px';
                                            closeBtn.style.background = 'none';
                                            closeBtn.style.border = 'none';
                                            closeBtn.style.color = 'white';
                                            closeBtn.style.cursor = 'pointer';
                                            closeBtn.style.zIndex = '1002';

                                            closeBtn.onclick = function() {
                                                collapseChart();
                                            };

                                            // Create expanded chart container
                                            const expandedContainer = document.createElement('div');
                                            expandedContainer.id = 'expandedChart';
                                            expandedContainer.style.width = '80%';
                                            expandedContainer.style.height = '80%';
                                            expandedContainer.style.backgroundColor = 'white';
                                            expandedContainer.style.padding = '20px';
                                            expandedContainer.style.borderRadius = '5px';
                                            expandedContainer.style.zIndex = '1001';

                                            // Move chart to expanded container
                                            overlay.appendChild(expandedContainer);
                                            overlay.appendChild(closeBtn);
                                            document.body.appendChild(overlay);

                                            // Initialize new chart in expanded view
                                            const expandedChart = echarts.init(expandedContainer);
                                            expandedChart.setOption(chart.getOption());
                                            expandedChart.resize();

                                            // Show the legend in expanded view
                                            expandedChart.setOption({
                                                legend: {
                                                    show: true
                                                },
                                                grid: {
                                                    top: '1%'
                                                }
                                            });

                                            // Add click event listener for navigation on expanded chart
                                            expandedChart.on('click', function(params) {
                                                if (params.componentType === 'series') {
                                                    const selectedDepartment = params.name;
                                                    window.location.href = `department-details.php?department=${encodeURIComponent(selectedDepartment)}`;
                                                }
                                            });

                                            // Close expanded view when clicking outside the chart
                                            overlay.addEventListener('click', function(e) {
                                                if (e.target === overlay) {
                                                    collapseChart();
                                                }
                                            });
                                        }
                                    }

                                    // Function to collapse the expanded chart
                                    function collapseChart() {
                                        const overlay = document.getElementById('chartOverlay');
                                        if (overlay) {
                                            document.body.removeChild(overlay);
                                        }

                                        isExpanded = false;
                                        updateChartOptions();
                                        chart.resize();
                                    }

                                    // Add click event listener for navigation (only on compact view)
                                    chart.on('click', function(params) {
                                        // Check if modal is open
                                        const isModalOpen = false; // Replace with your actual modal state check

                                        if (!isExpanded && !isModalOpen && params.componentType === 'series') {
                                            const selectedDepartment = params.name;
                                            window.location.href = `department-details.php?department=${encodeURIComponent(selectedDepartment)}`;
                                        }
                                    });

                                    // Handle window resize
                                    window.addEventListener('resize', function() {
                                        chart.resize();

                                        // Resize expanded chart if it exists
                                        const expandedContainer = document.getElementById('expandedChart');
                                        if (expandedContainer) {
                                            const expandedChart = echarts.getInstanceByDom(expandedContainer);
                                            if (expandedChart) {
                                                expandedChart.resize();
                                            }
                                        }
                                    });
                                });
                            </script>
                            <!-- End Stacked Bar Chart -->

                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                </div>
                <div class="col-md-4" style="margin-top: -17rem;">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title fs-6 text-center">Departments and Priorities</h5>

                            <!-- Stacked Bar Chart -->
                            <div id="stackedBarChartOne" style="height: 140px; width: 100%; padding: 0; margin: 0;" class="echart chart-container" data-chart-id="stackedBarChartOne" data-chart-title="Departments and Priorities"></div>

                            <script>
                                document.addEventListener("DOMContentLoaded", () => {
                                    // Use PHP-generated JSON data
                                    const departments = <?php echo $departmentsJsonTwo; ?>;
                                    const chartData = <?php echo $chartDataJsonTwo; ?>;

                                    // Track if chart is in expanded view
                                    let isExpanded = false;

                                    // Initialize the stacked bar chart
                                    const chart = echarts.init(document.querySelector("#stackedBarChartOne"));

                                    // Function to update chart options based on view mode
                                    function updateChartOptions() {
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
                                                show: isExpanded, // Only show legend when expanded
                                            },
                                            grid: {
                                                top: isExpanded ? '20%' : '10%', // More space for legend when expanded
                                                left: '3%',
                                                right: '4%',
                                                bottom: '5%',
                                                containLabel: true
                                            },
                                            xAxis: {
                                                type: 'category',
                                                data: departments,
                                                axisLabel: {
                                                    rotate: 30
                                                }
                                            },
                                            yAxis: {
                                                type: 'value',
                                                name: 'Count',
                                                min: 0,
                                                interval: 200,
                                                max: 800,
                                                axisLabel: {
                                                    formatter: '{value}'
                                                }
                                            },
                                            series: chartData
                                        });
                                    }

                                    // Initial render in compact mode
                                    updateChartOptions();

                                    // Add click handler for chart expansion
                                    document.querySelector("#stackedBarChartOne").addEventListener("click", function(e) {
                                        // Don't trigger navigation when clicking the chart container
                                        if (e.target === this) {
                                            expandChart();
                                            e.stopPropagation();
                                        }
                                    });

                                    // Function to handle chart expansion
                                    function expandChart() {
                                        // Get the chart container
                                        const chartContainer = document.querySelector("#stackedBarChartOne");

                                        // Toggle expanded state
                                        isExpanded = !isExpanded;

                                        if (isExpanded) {
                                            // Save original position and size
                                            chartContainer.dataset.originalStyle = chartContainer.getAttribute('style') || '';

                                            // Create overlay for full-screen effect
                                            const overlay = document.createElement('div');
                                            overlay.id = 'chartOverlay';
                                            overlay.style.position = 'fixed';
                                            overlay.style.top = '0';
                                            overlay.style.left = '0';
                                            overlay.style.width = '100%';
                                            overlay.style.height = '100%';
                                            overlay.style.backgroundColor = 'rgba(0, 0, 0, 0.7)';
                                            overlay.style.zIndex = '1000';
                                            overlay.style.display = 'flex';
                                            overlay.style.justifyContent = 'center';
                                            overlay.style.alignItems = 'center';

                                            // Create close button
                                            const closeBtn = document.createElement('button');
                                            closeBtn.innerText = '×';
                                            closeBtn.style.position = 'absolute';
                                            closeBtn.style.top = '20px';
                                            closeBtn.style.right = '20px';
                                            closeBtn.style.fontSize = '24px';
                                            closeBtn.style.background = 'none';
                                            closeBtn.style.border = 'none';
                                            closeBtn.style.color = 'white';
                                            closeBtn.style.cursor = 'pointer';
                                            closeBtn.style.zIndex = '1002';

                                            closeBtn.onclick = function() {
                                                collapseChart();
                                            };

                                            // Create expanded chart container
                                            const expandedContainer = document.createElement('div');
                                            expandedContainer.id = 'expandedChart';
                                            expandedContainer.style.width = '80%';
                                            expandedContainer.style.height = '80%';
                                            expandedContainer.style.backgroundColor = 'white';
                                            expandedContainer.style.padding = '20px';
                                            expandedContainer.style.borderRadius = '5px';
                                            expandedContainer.style.zIndex = '1001';

                                            // Move chart to expanded container
                                            overlay.appendChild(expandedContainer);
                                            overlay.appendChild(closeBtn);
                                            document.body.appendChild(overlay);

                                            // Initialize new chart in expanded view
                                            const expandedChart = echarts.init(expandedContainer);
                                            expandedChart.setOption(chart.getOption());
                                            expandedChart.resize();

                                            // Show the legend in expanded view
                                            expandedChart.setOption({
                                                legend: {
                                                    show: true
                                                },
                                                grid: {
                                                    top: '20%'
                                                }
                                            });

                                            // Add click event listener for navigation on expanded chart
                                            expandedChart.on('click', function(params) {
                                                if (params.componentType === 'series') {
                                                    const selectedDepartment = params.name;
                                                    window.location.href = `department-details.php?department=${encodeURIComponent(selectedDepartment)}`;
                                                }
                                            });

                                            // Close expanded view when clicking outside the chart
                                            overlay.addEventListener('click', function(e) {
                                                if (e.target === overlay) {
                                                    collapseChart();
                                                }
                                            });
                                        }
                                    }

                                    // Function to collapse the expanded chart
                                    function collapseChart() {
                                        const overlay = document.getElementById('chartOverlay');
                                        if (overlay) {
                                            document.body.removeChild(overlay);
                                        }

                                        isExpanded = false;
                                        updateChartOptions();
                                        chart.resize();
                                    }

                                    // Add click event listener for navigation (only on compact view)
                                    chart.on('click', function(params) {
                                        if (!isExpanded && params.componentType === 'series') {
                                            const selectedDepartment = params.name;
                                            window.location.href = `department-details.php?department=${encodeURIComponent(selectedDepartment)}`;
                                        }
                                    });

                                    // Handle window resize
                                    window.addEventListener('resize', function() {
                                        chart.resize();

                                        // Resize expanded chart if it exists
                                        const expandedContainer = document.getElementById('expandedChart');
                                        if (expandedContainer) {
                                            const expandedChart = echarts.getInstanceByDom(expandedContainer);
                                            if (expandedChart) {
                                                expandedChart.resize();
                                            }
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

        </div>
        </div>
    </section>
</main><!-- End #main -->

<!-- Chart Modal -->
<div id="chartModal" class="chart-modal">
    <div class="chart-modal-content">
        <span class="chart-modal-close">&times;</span>
        <h2 class="chart-modal-title" id="modalChartTitle">Chart Title</h2>
        <div class="chart-modal-canvas-container">
            <canvas id="modalChart"></canvas>
        </div>
    </div>
</div>

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

    // Global modal state
    let isModalOpen = false;
    let activeModalChart = null;
    let originalChart = null;
    let originalChartId = null;

    // Initialize Charts and Add Download Functionality
    document.addEventListener("DOMContentLoaded", () => {
        // Chart containers
        const chartContainers = document.querySelectorAll('.chart-container');

        // Modal elements
        const modal = document.getElementById('chartModal');
        const modalClose = document.querySelector('.chart-modal-close');
        const modalTitle = document.getElementById('modalChartTitle');

        // Initialize the charts
        const statusChart = echarts.init(document.querySelector("#statusChart"));
        const donutChart = echarts.init(document.querySelector("#donutChart"));
        const stackedBarChart = echarts.init(document.querySelector("#stackedBarChart"));
        const stackedBarChartOne = echarts.init(document.querySelector("#stackedBarChartOne"));

        // Handle chart container clicks
        chartContainers.forEach(container => {
            container.addEventListener('click', function(e) {
                // Ignore clicks on select elements or their children
                if (e.target.tagName === 'SELECT' || e.target.closest('select')) {
                    return;
                }

                const chartId = this.getAttribute('data-chart-id');
                const chartTitle = this.getAttribute('data-chart-title');

                // Set modal title
                modalTitle.textContent = chartTitle;
                originalChartId = chartId;

                // Store reference to original chart
                if (chartId === 'statusChart') {
                    originalChart = chart; // Using the existing chart variable for statusChart
                } else if (chartId === 'donutChart') {
                    originalChart = donutChart;
                } else if (chartId === 'stackedBarChart') {
                    originalChart = stackedBarChart;
                } else if (chartId === 'stackedBarChartOne') {
                    originalChart = stackedBarChartOne;
                }

                // Display modal
                modal.style.display = 'flex';
                isModalOpen = true;

                // Create a new chart in the modal
                createModalChart(chartId);

                // Prevent event propagation
                e.stopPropagation();
            });
        });

        // Create a chart in the modal
        function createModalChart(chartId) {
            const modalCanvasContainer = document.querySelector('.chart-modal-canvas-container');

            // Clear any existing chart
            modalCanvasContainer.innerHTML = '';

            if (chartId === 'statusChart') {
                // For Chart.js (statusChart)
                const canvas = document.createElement('canvas');
                canvas.id = 'modalStatusChart';
                modalCanvasContainer.appendChild(canvas);

                // Get data from original chart
                const data = chart.data;
                const options = chart.options;

                // Create new chart
                const ctx = canvas.getContext('2d');
                activeModalChart = new Chart(ctx, {
                    type: 'bar',
                    data: data,
                    options: {
                        ...options,
                        maintainAspectRatio: false,
                        responsive: true,
                        plugins: {
                            ...options.plugins,
                            // Override click handler to do nothing in modal
                            onClick: null
                        }
                    },
                    plugins: [ChartDataLabels]
                });
            } else {
                // For ECharts (donutChart, stackedBarChart, stackedBarChartOne)
                const div = document.createElement('div');
                div.id = 'modal' + chartId;
                div.style.width = '100%';
                div.style.height = '100%';
                modalCanvasContainer.appendChild(div);

                // Initialize ECharts instance
                activeModalChart = echarts.init(div);

                // Get options from original chart
                const options = originalChart.getOption();

                // Set options for modal chart
                activeModalChart.setOption(options);

                // Resize to fit container
                activeModalChart.resize();
            }
        }

        // Close modal when clicking the close button
        modalClose.addEventListener('click', function() {
            closeModal();
        });

        // Close modal when clicking outside the content
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal();
            }
        });

        // Close modal function
        function closeModal() {
            modal.style.display = 'none';
            isModalOpen = false;

            // Destroy the modal chart to clean up
            if (activeModalChart) {
                if (originalChartId === 'statusChart') {
                    activeModalChart.destroy();
                } else {
                    activeModalChart.dispose();
                }
                activeModalChart = null;
            }
        }

        // Handle window resize
        window.addEventListener('resize', function() {
            if (isModalOpen && activeModalChart) {
                if (originalChartId !== 'statusChart') {
                    activeModalChart.resize();
                }
            }

            // Resize original charts
            statusChart.resize();
            donutChart.resize();
            stackedBarChart.resize();
            stackedBarChartOne.resize();
        });

        // Download charts on button click
        document.getElementById("downloadChartsBtn").addEventListener("click", () => {
            downloadChart(statusChart, 'Up and Down sites.png');
            downloadChart(donutChart, 'site classification.png');
            downloadChart(stackedBarChart, 'departments and classes.png');
            downloadChart(stackedBarChartOne, 'department and priorities.png');
        });
    });
</script>

<!-- ======= Footer ======= -->
<?php include('footer.php'); ?>