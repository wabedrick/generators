
<?php
// Turn off output buffering and clear any existing output
if (ob_get_level()) ob_end_clean();

// Set proper JSON header
header('Content-Type: application/json');

// Disable error reporting for output
error_reporting(0);
ini_set('display_errors', 0);

// Include database connection
include 'connection/db_connection.php';

// Check if connection was successful
if (!isset($conn) || $conn === false) {
    echo json_encode([
        "error" => "Database connection failed"
    ]);
    exit;
}

// Check if interval is set and sanitize input
$interval = isset($_POST['interval']) ? trim($_POST['interval']) : '24 HOUR';

// Validate and sanitize interval (to prevent SQL injection)
$allowed_units = ['HOUR', 'DAY', 'MONTH', 'YEAR'];
$interval_parts = explode(' ', $interval);

if (count($interval_parts) !== 2 || !is_numeric($interval_parts[0]) || !in_array(strtoupper($interval_parts[1]), $allowed_units)) {
    echo json_encode([
        "error" => "Invalid interval format",
        "received" => $interval
    ]);
    exit;
}

$sql = "
    SELECT 
        SUM(CASE WHEN active_site_status = 'Down' THEN 1 ELSE 0 END) AS down_count,
        SUM(CASE WHEN active_site_status = 'Up' THEN 1 ELSE 0 END) AS up_count
    FROM sites
    WHERE site_status = 'Active'
    AND (downtime_date >= NOW() - INTERVAL $interval OR downtime_date IS NULL)
";

$result = mysqli_query($conn, $sql);

if (!$result) {
    echo json_encode([
        "error" => "SQL Error: " . mysqli_error($conn)
    ]);
    exit;
}

$data = mysqli_fetch_assoc($result);

// Convert NULL values to 0
$data['down_count'] = $data['down_count'] === NULL ? 0 : (int)$data['down_count'];
$data['up_count'] = $data['up_count'] === NULL ? 0 : (int)$data['up_count'];

// Return clean JSON response with no other output
echo json_encode($data);
exit;

?>
