<?php
// Start session and include database connection
session_start();
include 'connection/db_connection.php';

// Get the site class name and department
$class_name = isset($_GET['class']) ? $_GET['class'] : '';
$department_name = isset($_GET['department']) ? $_GET['department'] : '';
$site_status = isset($_GET['site_status']) ? $_GET['site_status'] : '';
$format = isset($_GET['format']) ? $_GET['format'] : 'csv';

// Modify the query to filter by class or department
// $sql = "SELECT * FROM sites WHERE 1=1";
// if ($class_name) {
//     $sql .= " AND class='$class_name'";
// }
// if ($department_name) {
//     $sql .= " AND department='$department_name'";
// }

// if ($site_status) {
//     $sql .= " AND site_status='$site_status'";
// }

$sql = "SELECT
    s.site_id AS site_id,
    s.primary_id AS primary_id,
    s.site_name AS site_name,
    s.site_auto_status AS site_auto_status,
    s.site_status AS site_status,
    s.department AS department,
    s.tx_site_type AS tx_site_type,
    s.class AS class,
    COUNT(sd.dependent_site_id) AS dependent_sites_count
FROM 
    sites s
LEFT JOIN 
    dependencies sd ON s.primary_id = sd.site_id
WHERE 1=1";
if ($class_name) {
    $sql .= " AND class='$class_name' GROUP BY s.primary_id, s.site_name, s.latitude";
} else if ($department_name) {
    $sql .= " AND department='$department_name' GROUP BY s.primary_id, s.site_name, s.latitude";
} else if ($site_status) {
    $sql .= " AND site_status='$site_status' GROUP BY s.primary_id, s.site_name, s.latitude";
} else {
    $sql .= " GROUP BY s.primary_id, s.site_name, s.latitude";
}

$result = $conn->query($sql);

// Check if data exists
if ($result->num_rows > 0) {
    if ($format == 'csv') {
        // Set headers for CSV download
        // $filename = $class_name ? "Class_$class_name" : "Department_$department_name";
        if ($class_name) {
            $filename = "Class_$class_name";

            header('Content-Type: text/csv');
            header("Content-Disposition: attachment; filename=\"{$filename}_sites.csv\"");
            // Output CSV data
            $output = fopen("php://output", "w");
            fputcsv($output, ['Site ID', 'Site Name', 'Dependents', 'Generators', 'Automation', 'Site Status', 'Department', 'Priorities']);

            while ($row = $result->fetch_assoc()) {
                fputcsv($output, [
                    $row['primary_id'],
                    $row['site_name'],
                    $row['dependent_sites_count'],
                    '2',
                    $row['site_auto_status'],
                    $row['site_status'],
                    $row['department'],
                    $row['tx_site_type']
                ]);
            }
            fclose($output);
        } else if ($department_name) {
            $filename = "Department_$department_name";

            header('Content-Type: text/csv');
            header("Content-Disposition: attachment; filename=\"{$filename}_sites.csv\"");

            $output = fopen("php://output", "w");
            fputcsv($output, ['Site ID', 'Site Name', 'Dependents', 'Generators', 'Automation', 'Site Status', 'Class', 'Priorities']);

            while ($row = $result->fetch_assoc()) {
                fputcsv($output, [
                    $row['primary_id'],
                    $row['site_name'],
                    $row['dependent_sites_count'],
                    '2',
                    $row['site_auto_status'],
                    $row['site_status'],
                    $row['clas'],
                    $row['tx_site_type']
                ]);
            }
            fclose($output);
        } else if ($site_status) {
            $filename = "$site_status";

            header('Content-Type: text/csv');
            header("Content-Disposition: attachment; filename=\"{$filename}_sites.csv\"");

            $output = fopen("php://output", "w");
            fputcsv($output, ['Site ID', 'Site Name', 'Dependents', 'Generators', 'Automation', 'Class', 'Department', 'Priorities']);

            while ($row = $result->fetch_assoc()) {
                fputcsv($output, [
                    $row['primary_id'],
                    $row['site_name'],
                    $row['dependent_sites_count'],
                    '2',
                    $row['site_auto_status'],
                    $row['class'],
                    $row['department'],
                    $row['tx_site_type']
                ]);
            }
            fclose($output);
        } else {
            $filename = "All";

            header('Content-Type: text/csv');
            header("Content-Disposition: attachment; filename=\"{$filename}_sites.csv\"");

            $output = fopen("php://output", "w");
            fputcsv($output, ['Site ID', 'Site Name', 'Dependents', 'Generators', 'Automation Status', 'Site Status', 'Class', 'Department', 'Priorities']);

            while ($row = $result->fetch_assoc()) {
                fputcsv($output, [
                    $row['primary_id'],
                    $row['site_name'],
                    $row['dependent_sites_count'],
                    '2',
                    $row['site_auto_status'],
                    $row['site_status'],
                    $row['class'],
                    $row['department'],
                    $row['tx_site_type']
                ]);
            }
            fclose($output);
        }
    } elseif ($format == 'excel') {
        // Set headers for Excel download
        if ($class_name) {
            $filename = "Class_$class_name";

            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=\"{$filename}_sites.xls\"");

            // Output Excel data
            echo "<table border='1'>";
            echo "<tr><th>Site ID</th><th>Site Name</th><th>Dependents</th><th>Generators</th><th>Automation Status</th><th>Site Status</th><th>Department</th><th>Priorities</th></tr>";

            while ($row = $result->fetch_assoc()) {
                echo "<tr><td>{$row['primary_id']}</td><td>{$row['site_name']}</td><td>{$row['dependent_sites_count']}</td><td>2</td><td>{$row['site_auto_status']}</td><td>{$row['site_status']}</td><td>{$row['department']}</td><td>{$row['tx_site_type']}</td></tr>";
            }
            echo "</table>";
        } else if ($department_name) {
            $filename = "Department_$department_name";

            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=\"{$filename}_sites.xls\"");

            // Output Excel data
            echo "<table border='1'>";
            echo "<tr><th>Site ID</th><th>Site Name</th><th>Dependents</th><th>Generators</th><th>Automation Status</th><th>Site Status</th><th>Class</th><th>Priorities</th></tr>";

            while ($row = $result->fetch_assoc()) {
                echo "<tr><td>{$row['primary_id']}</td><td>{$row['site_name']}</td><td>{$row['dependent_sites_count']}</td><td>2</td><td>{$row['site_auto_status']}</td><td>{$row['site_status']}</td><td>{$row['class']}</td><td>{$row['tx_site_type']}</td></tr>";
            }
            echo "</table>";
        } else if ($site_status) {
            $filename = "$site_status";

            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=\"{$filename}_sites.xls\"");

            // Output Excel data
            echo "<table border='1'>";
            echo "<tr><th>Site ID</th><th>Site Name</th><th>Dependents</th><th>Generators</th><th>Automation Status</th><th>Class</th><th>Department</th><th>Priorities</th></tr>";

            while ($row = $result->fetch_assoc()) {
                echo "<tr><td>{$row['primary_id']}</td><td>{$row['site_name']}</td><td>{$row['dependent_sites_count']}</td><td>2</td><td>{$row['site_auto_status']}</td><td>{$row['class']}</td><td>{$row['department']}</td><td>{$row['tx_site_type']}</td></tr>";
            }
            echo "</table>";
        } else {
            $filename = "All";

            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=\"{$filename}_sites.xls\"");

            // Output Excel data
            echo "<table border='1'>";
            echo "<tr><th>Site ID</th><th>Site Name</th><th>Dependents</th><th>Generators</th><th>Automation Status</th><th>Site Status</th><th>Class</th><th>Department</th><th>Priorities</th></tr>";

            while ($row = $result->fetch_assoc()) {
                echo "<tr><td>{$row['primary_id']}</td><td>{$row['site_name']}</td><td>{$row['dependent_sites_count']}</td><td>2</td><td>{$row['site_auto_status']}</td><td>{$row['site_status']}</td><td>{$row['class']}</td><td>{$row['department']}</td><td>{$row['tx_site_type']}</td></tr>";
            }
            echo "</table>";
        }
    }
} else {
    echo "No data found for this class or department.";
}

$conn->close();
