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

    .chart-container {
        margin: 0 auto;
        padding: 20px;
        border-radius: 10px;
        cursor: pointer;
    }

    canvas {
        max-height: 500px;
    }

    /* Modal Styles */
    .chart-modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.8);
        justify-content: center;
        align-items: center;
    }

    .chart-modal-content {
        background-color: #fff;
        padding: 20px;
        border-radius: 10px;
        width: 90vw;
        height: 90vh;
        position: relative;
    }

    .close-modal {
        position: absolute;
        top: 10px;
        right: 20px;
        font-size: 30px;
        cursor: pointer;
        color: #333;
    }
</style>

<main id="main" class="main">
    <div class="d-flex justify-content-between">
        <div class="pagetitle float-left">
            <h1>Site Dashboard</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item active">Sites Dashboard</li>
                </ol>
            </nav>
        </div>
        <div class="period">
            <h4 class="text-muted">Today</h4>
        </div>
        <button id="downloadChartsBtn" class="btn btn-primary" style="height: 3rem;">Download Charts</button>
    </div>

    <section class="section dashboard">
        <div class="row">
            <div class="col-xxl-4 col-md-4">
                <div class="card info-card revenue-card">
                    <div class="card-body">
                        <h5 class="card-title fs-6 text-center">Sites Summary</h5>
                        <div class="">
                            <a href="view-all-sites.php" class="card-link">
                                <div class="card align-items-center p-2">
                                    <p><span class="fs-6">Total</span>
                                    <p><?php echo $total_sites; ?></p>
                                    </p>
                                </div>
                            </a>
                            <div class="card align-items-center p-2 position-relative active-card">
                                <a href="view-sites-on-status.php?status=active" class="card-link">
                                    <p><span class="fs-6">Active</span> (<?php echo $total_act_sites; ?>)</p>
                                    <p>
                                        <div style="display: flex;">
                                            <p style="margin-right: 12px; color: black;">Up: <?php echo htmlspecialchars($total_act_up_sites); ?>
                                                <span>
                                                    <p><a href="view-sites-on-status.php?status=active&filter=Down" class="down-link">Down: <?php echo htmlspecialchars($total_active_down_sites); ?></a></p>
                                                </span>
                                            </p>
                                        </div>
                                    </p>
                                </a>
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
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title fs-4 text-center">Active Sites Status</h5>
                        <div style="width: 50%; margin: auto; height: 410px;" class="chart-container" id="statusChartContainer">
                            <label for="interval">Select Time Interval:</label>
                            <select id="interval">
                                <option value="1 HOUR">Last 1 Hour</option>
                                <option value="3 HOUR">Last 3 Hours</option>
                                <option value="12 HOUR" selected>Last 12 Hours</option>
                                <option value="1 DAY">Last 1 Day</option>
                                <option value="7 DAY">Last 7 Days</option>
                                <option value="30 DAY">Last 30 Days</option>
                                <option value="90 DAY">Last 90 Days</option>
                            </select>
                            <div id="chartStatus" style="display: none; color: red; margin-top: 10px;"></div>
                            <canvas id="statusChart" style="display: block; height: 360px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title fs-4 text-center">Site Classification</h5>
                        <div id="donutChart" style="min-height: 370px; max-height: 380px" class="echart chart-container"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title fs-6 text-center">Departments and Classes</h5>
                        <div id="stackedBarChart" style="min-height: 380px; max-height: 380px;" class="echart chart-container"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title fs-6 text-center">Departments and Priorities</h5>
                        <div id="stackedBarChartOne" style="min-height: 380px; max-height: 380px;" class="echart chart-container"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal for expanded charts -->
    <div id="chartModal" class="chart-modal">
        <div class="chart-modal-content">
            <span class="close-modal">&times;</span>
            <div id="expandedChart" style="width: 100%; height: 100%;"></div>
        </div>
    </div>
</main>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
<script src="https://cdn.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>

<script>
    let chartInstances = {};
    let currentInterval = '12 HOUR';

    function updateBarChart(interval) {
        $('#chartStatus').text('Loading...').show();
        currentInterval = interval;

        $.ajax({
            url: 'fetch_active_site_data.php',
            method: 'POST',
            data: {
                interval: interval
            },
            dataType: 'json',
            success: function(data) {
                if (data.error || data.down_count === undefined || data.up_count === undefined) {
                    $('#chartStatus').text('Error: ' + (data.error || 'Invalid data format')).show();
                    return;
                }
                $('#chartStatus').hide();

                if (chartInstances.statusChart) chartInstances.statusChart.destroy();

                const ctx = document.getElementById('statusChart').getContext('2d');
                chartInstances.statusChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Down', 'Up'],
                        datasets: [{
                            label: 'Site Status',
                            data: [data.down_count, data.up_count],
                            backgroundColor: ['rgba(255, 99, 132, 0.5)', 'rgba(75, 192, 192, 0.5)'],
                            borderColor: ['rgba(255, 99, 132, 1)', 'rgba(75, 192, 192, 1)'],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            }
                        },
                        plugins: {
                            datalabels: {
                                anchor: 'end',
                                align: 'top',
                                formatter: value => value,
                                color: context => context.dataset.borderColor[context.dataIndex],
                                font: {
                                    weight: 'bold',
                                    size: 16
                                },
                                padding: {
                                    top: 6
                                }
                            }
                        }
                    },
                    plugins: [ChartDataLabels]
                });
            },
            error: function(xhr, status, error) {
                $('#chartStatus').text('AJAX Error: ' + error).show();
            }
        });
    }

    function showExpandedChart(chartId) {
        const modal = document.getElementById('chartModal');
        const expandedChartDiv = document.getElementById('expandedChart');
        modal.style.display = 'flex';

        if (chartId === 'statusChart') {
            const ctx = expandedChartDiv.appendChild(document.createElement('canvas')).getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: chartInstances.statusChart.data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    },
                    plugins: {
                        datalabels: {
                            anchor: 'end',
                            align: 'top',
                            formatter: value => value,
                            color: context => context.dataset.borderColor[context.dataIndex],
                            font: {
                                weight: 'bold',
                                size: 20
                            },
                            padding: {
                                top: 10
                            }
                        },
                        title: {
                            display: true,
                            text: 'Active Sites Status',
                            font: {
                                size: 24
                            }
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });
        } else {
            const expandedChart = echarts.init(expandedChartDiv);
            expandedChart.setOption(chartInstances[chartId].getOption());
            expandedChart.resize({
                width: '100%',
                height: '100%'
            });
        }
    }

    document.addEventListener("DOMContentLoaded", () => {
        // Bar Chart
        updateBarChart($('#interval').val());
        $('#interval').change(() => updateBarChart($(this).val()));

        // Donut Chart
        chartInstances.donutChart = echarts.init(document.querySelector("#donutChart"));
        chartInstances.donutChart.setOption({
            color: ['#FF0000', '#000000', '#A9A9A9', '#87CEEB', '#FFA500', '#008000'],
            tooltip: {
                trigger: 'item',
                formatter: '{d}%'
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
                    formatter: '{b}',
                    color: '#fff',
                    fontSize: 10
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
                data: <?php echo $chartDataJson; ?>
            }]
        });

        // Stacked Bar Charts
        chartInstances.stackedBarChart = echarts.init(document.querySelector("#stackedBarChart"));
        chartInstances.stackedBarChart.setOption({
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
                data: <?php echo $departmentsJsonOne; ?>,
                axisLabel: {
                    rotate: 30
                }
            },
            yAxis: {
                type: 'value',
                name: 'Count'
            },
            series: <?php echo $chartDataJsonOne; ?>
        });

        chartInstances.stackedBarChartOne = echarts.init(document.querySelector("#stackedBarChartOne"));
        chartInstances.stackedBarChartOne.setOption({
            color: ['#FF0000', '#000000', '#A9A9A9', '#87CEEB', '#FFA500', '#008000'],
            tooltip: {
                trigger: 'axis',
                axisPointer: {
                    type: 'shadow'
                }
            },
            legend: {
                top: '1%',
                left: 'center'
            },
            grid: {
                top: '20%',
                left: '3%',
                right: '4%',
                bottom: '5%',
                containLabel: true
            },
            xAxis: {
                type: 'category',
                data: <?php echo $departmentsJsonTwo; ?>,
                axisLabel: {
                    rotate: 30
                }
            },
            yAxis: {
                type: 'value',
                name: 'Count'
            },
            series: <?php echo $chartDataJsonTwo; ?>
        });

        // Chart click handlers
        document.querySelectorAll('.chart-container').forEach(container => container.addEventListener('click', () => {
            const chartId = container.querySelector('canvas, div').id;
            showExpandedChart(chartId);
        }));

        // Modal close handler
        document.querySelector('.close-modal').addEventListener('click', () => {
            const modal = document.getElementById('chartModal');
            modal.style.display = 'none';
            document.getElementById('expandedChart').innerHTML = '';
        });

        // Download functionality
        document.getElementById("downloadChartsBtn").addEventListener("click", () => {
            const downloadChart = (chart, fileName) => {
                const url = chart.getDataURL ? chart.getDataURL({
                        type: 'png',
                        pixelRatio: 2,
                        backgroundColor: '#fff'
                    }) :
                    chart.canvas.toDataURL('image/png');
                const link = document.createElement('a');
                link.href = url;
                link.download = fileName;
                link.click();
            };
            downloadChart(chartInstances.statusChart, 'Up and Down sites.png');
            downloadChart(chartInstances.donutChart, 'site classification.png');
            downloadChart(chartInstances.stackedBarChart, 'departments and classes.png');
            downloadChart(chartInstances.stackedBarChartOne, 'department and priorities.png');
        });
    });
</script>

<!-- ======= Footer ======= -->
<?php include('footer.php'); ?>