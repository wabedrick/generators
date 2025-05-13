<?php
// Start output buffering
ob_start();

include 'connection/db_connection.php';
session_start();
if (!isset($_SESSION["username"])) {
    header('location: ../index.php');
    exit(); // Ensure the script stops after redirecting
}

include 'header_aside.php';
?>

<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }

    .container {
        width: 50%;
        margin: 50px auto;
        background-color: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    h1 {
        text-align: center;
        color: #333;
    }

    form {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    input[type="file"] {
        margin: 20px 0;
    }

    .column-selection {
        margin: 20px 0;
        text-align: left;
        width: 80%;
    }

    input[type="submit"] {
        padding: 10px 20px;
        background-color: #007bff;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    input[type="submit"]:hover {
        background-color: #0056b3;
    }

    .message {
        margin-top: 20px;
        text-align: center;
        color: green;
    }

    .error {
        margin-top: 20px;
        text-align: center;
        color: red;
    }

    .debug-info {
        margin-top: 20px;
        padding: 10px;
        background-color: #f8f9fa;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-family: monospace;
        white-space: pre-wrap;
        font-size: 12px;
    }

    body {
        background-color: #f8f9fa;
        font-family: 'Arial', sans-serif;
    }

    .dashboard-container {
        max-width: 800px;
        margin: 50px auto;
        padding: 20px;
        background: #ffffff;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .dashboard-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .dashboard-header h1 {
        font-size: 2.5rem;
        color: #333;
        font-weight: bold;
    }

    .dashboard-header p {
        font-size: 1.2rem;
        color: #666;
    }

    .run-script-button {
        display: block;
        width: 100%;
        max-width: 300px;
        margin: 0 auto;
        padding: 15px;
        font-size: 1.1rem;
        font-weight: bold;
        background-color: #007bff;
        border: none;
        border-radius: 5px;
        color: #fff;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .run-script-button:hover {
        background-color: #0056b3;
    }

    .run-script-button:active {
        background-color: #004080;
    }

    .status-message {
        text-align: center;
        margin-top: 20px;
        font-size: 1rem;
        color: #333;
    }
</style>

<main id="main" class="main">
    <h1>Update Databse Tables</h1>
    <div class="container">


        <body>
            <div class="dashboard-container">
                <div class="dashboard-header">
                    <p>Press the Button to Run The Script</p>
                </div>
                <button id="run-script-button" class="run-script-button">Run the Script</button>
                <div id="status-message" class="status-message"></div>
            </div>

            <!-- Bootstrap 5 JS and dependencies -->

            <!-- Example HTML button -->
            <!-- <button id="run-script-button">Run Python Script</button> -->

            <!-- Include jQuery or any other library for AJAX (optional) -->
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

            <script>
                // JavaScript to handle button click
                document.getElementById('run-script-button').addEventListener('click', function() {
                    // Send an AJAX request to the server
                    fetch('./run-python-script.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                        })
                        .then(response => response.json())
                        .then(data => {
                            console.log('Script output:', data);
                            alert('Python script executed successfully!');
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Failed to execute Python script.');
                        });
                });
            </script>

    </div>
</main>

<?php
include 'footer.php';

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Enable debugging - store debug information to show after redirect
    $debug_info = "DEBUG INFORMATION:\n";

    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        header("Location: update_sites_table.php?error=File upload failed. Please try again.");
        exit();
    }

    $file = $_FILES['file']['tmp_name'];
    $selected_columns = $_POST['columns'] ?? []; // Get selected columns

    if (empty($selected_columns)) {
        header("Location: update_sites_table.php?error=Please select at least one column to update.");
        exit();
    }

    // Check file extension
    $file_ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));

    if ($file_ext !== 'csv') {
        header("Location: update_sites_table.php?error=Unsupported file format. Please upload a CSV file.");
        exit();
    }

    // Counter for successful updates
    $updated_count = 0;

    // Map form field names to database column names
    $form_to_db_map = [
        'status' => 'active_site_status',
        'date' => 'downtime_date',
        'duration' => 'downtime_duration',
        'summary' => 'problem_summary',
        'comment' => 'comment'
    ];

    // Process CSV file
    $handle = fopen($file, 'r');
    if ($handle !== FALSE) {
        // Get header row to map columns
        $header = fgetcsv($handle);
        $debug_info .= "CSV Header: " . print_r($header, true) . "\n";

        // Explicitly map CSV column names to their positions
        $column_indices = [];
        foreach ($header as $index => $col_name) {
            $column_indices[trim($col_name)] = $index;
        }
        $debug_info .= "Column Indices: " . print_r($column_indices, true) . "\n";

        // Define the CSV column names that correspond to our database fields
        $csv_columns = [
            'status' => 'Status', // Adjust these to match exact CSV column names with correct case
            'date' => 'Downtime Date',
            'duration' => 'Duration',
            'summary' => 'Problem Summary',
            'comment' => 'Comment'
        ];

        $debug_info .= "CSV Columns Map: " . print_r($csv_columns, true) . "\n";
        $debug_info .= "Selected Columns: " . print_r($selected_columns, true) . "\n";

        // Process data rows
        $row_count = 1; // Header was row 1
        while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
            $row_count++;
            if (empty($data[0])) {
                $debug_info .= "Skipping row $row_count - No ID\n";
                continue; // Skip rows with no ID
            }

            $id = $data[0]; // Assuming the first column is the primary key
            $debug_info .= "Processing row $row_count with ID: $id\n";

            // Prepare the SQL query
            $updates = [];
            $params = [];
            $types = '';

            foreach ($selected_columns as $column) {
                // Only process if column is defined in our mapping
                if (isset($form_to_db_map[$column]) && isset($csv_columns[$column])) {
                    $db_column = $form_to_db_map[$column];
                    $csv_column = $csv_columns[$column];

                    // Check if the CSV column exists in the header
                    if (isset($column_indices[$csv_column])) {
                        $index = $column_indices[$csv_column];

                        if (isset($data[$index])) {
                            $value = trim($data[$index]);

                            // Format date if needed
                            if ($column === 'date' && !empty($value)) {
                                // Try different date formats
                                $date_formats = [
                                    'n/j/Y H:i',
                                    'm/d/Y H:i',
                                    'Y-m-d H:i:s',
                                    'Y-m-d H:i',
                                    'd-m-Y H:i',
                                    'j-n-Y H:i'
                                ];

                                $date_converted = false;
                                foreach ($date_formats as $format) {
                                    $date_obj = DateTime::createFromFormat($format, $value);
                                    if ($date_obj) {
                                        $value = $date_obj->format('Y-m-d H:i:s');
                                        $date_converted = true;
                                        break;
                                    }
                                }

                                if (!$date_converted) {
                                    $debug_info .= "Warning: Could not convert date '$value' to proper format on row $row_count\n";
                                }
                            }

                            $updates[] = "$db_column = ?";
                            $params[] = $value;
                            $types .= 's'; // Assume all columns are strings

                            $debug_info .= "  - Adding update for $db_column = '$value'\n";
                        } else {
                            $debug_info .= "  - No data for $csv_column at index $index on row $row_count\n";
                        }
                    } else {
                        $debug_info .= "  - CSV column '$csv_column' not found in header\n";
                    }
                } else {
                    $debug_info .= "  - Mapping not found for column '$column'\n";
                }
            }

            if (!empty($updates)) {
                $sql = "UPDATE sites SET " . implode(', ', $updates) . " WHERE primary_id = ?";
                $params[] = $id;
                $types .= 'i'; // Primary key is an integer

                $debug_info .= "  - SQL: $sql\n";
                $debug_info .= "  - Params: " . print_r($params, true) . "\n";

                $stmt = $conn->prepare($sql);
                if ($stmt === false) {
                    $error_msg = "Database error: " . $conn->error;
                    $debug_info .= "  - Error: $error_msg\n";
                    header("Location: update_sites_table.php?error=" . urlencode($error_msg) . "&debug=1&debug_info=" . urlencode($debug_info));
                    exit();
                }

                $stmt->bind_param($types, ...$params);
                if ($stmt->execute()) {
                    $updated_count++;
                    $debug_info .= "  - Update successful\n";
                } else {
                    $debug_info .= "  - Update failed: " . $stmt->error . "\n";
                }
                $stmt->close();
            } else {
                $debug_info .= "  - No updates to make for this row\n";
            }
        }
        fclose($handle);

        // Redirect with success message
        $message = "Database updated successfully! Updated $updated_count records.";
        header("Location: update_sites_table.php?message=" . urlencode($message) . "&debug=1&debug_info=" . urlencode($debug_info));
        exit();
    } else {
        header("Location: update_sites_table.php?error=Could not open CSV file. Please try again.");
        exit();
    }
}

// End output buffering and send the output
ob_end_flush();
?>