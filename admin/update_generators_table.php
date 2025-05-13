<?php
ob_start();

include "connection/db_connection.php";
session_start();
if (!isset($_SESSION["username"])) {
    header('location: ../index.php');
    exit(); // Ensure the script stops after redirecting
}

include "header_aside.php";

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
</style>

<main id="main" class="main">
    <h1>Update Generators Table</h1>
    <div class="container">
        <form action="update_generators_table.php" method="post" enctype="multipart/form-data">
            <label for="file">Upload CSV or Excel File:</label>
            <input type="file" name="file" id="file" accept=".csv, .xlsx" required>

            <div class="column-selection">
                <label>Select Columns to Update:</label><br>
                <input type="checkbox" name="columns[]" value="actual-rhrs"> Actual Run Hours<br>
                <input type="checkbox" name="columns[]" value="date-updated"> Date Updated<br>
            </div>

            <div style="margin: 15px 0;">
                <label>Debug Mode:</label>
                <input type="checkbox" name="debug_mode" value="1"> Show Debug Information
            </div>

            <input type="submit" value="Update Generators Table">
        </form>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="modal success-modal">
        <div class="modal-content">
            <div class="modal-header">
                <span class="close-btn">&times;</span>
                <h2>Success</h2>
            </div>
            <div class="modal-body">
                <p id="successMessage"></p>
            </div>
            <div class="modal-footer">
                <button class="btn" onclick="closeModal('successModal')">OK</button>
            </div>
        </div>
    </div>

    <!-- Error Modal -->
    <div id="errorModal" class="modal error-modal">
        <div class="modal-content">
            <div class="modal-header">
                <span class="close-btn">&times;</span>
                <h2>Error</h2>
            </div>
            <div class="modal-body">
                <p id="errorMessage"></p>
            </div>
            <div class="modal-footer">
                <button class="btn" onclick="closeModal('errorModal')">OK</button>
            </div>
        </div>
    </div>

    <!-- Debug Information Section -->
    <?php
    // Display debug info from session if available
    if (isset($_SESSION['debug_info']) && !empty($_SESSION['debug_info'])):
    ?>
        <div class="container debug-info">
            <h3>Debug Information</h3>
            <pre><?php echo htmlspecialchars($_SESSION['debug_info']); ?></pre>
        </div>
    <?php
        // Clear the debug info from session after displaying
        unset($_SESSION['debug_info']);
    endif;
    ?>
</main>

<?php
include "footer.php";

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $debug_mode = isset($_POST['debug_mode']) ? true : false;
    $debug_info = "";

    if ($_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['file']['tmp_name'];
        $selected_columns = $_POST['columns'] ?? [];

        // Check if columns are selected
        if (empty($selected_columns)) {
            header("Location: update_generators_table.php?error=Please select at least one column to update.");
            exit();
        }

        // Map CSV column names to database columns and their data types
        // IMPORTANT: These should match exactly with your CSV headers
        $column_mapping = [
            'actual-rhrs' => ['column' => 'latest_actual_run_hrs', 'type' => 'integer', 'csv_header' => 'actual_run_hours'],
            'date-updated' => ['column' => 'rhr_update_date', 'type' => 'datetime', 'csv_header' => 'date_updated'],
        ];

        if ($debug_mode) {
            $debug_info .= "Selected columns: " . implode(', ', $selected_columns) . "\n\n";
            $debug_info .= "Column mapping:\n" . print_r($column_mapping, true) . "\n\n";
        }

        // Check file extension
        $file_ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));

        if ($file_ext === 'csv') {
            // Process CSV file
            $handle = fopen($file, 'r');
            if ($handle !== FALSE) {
                // Read the header row
                $header = fgetcsv($handle);

                if ($debug_mode) {
                    $debug_info .= "CSV Headers: " . implode(', ', $header) . "\n\n";
                }

                // Find the ID column index (assuming it's named 'generator_id' or similar)
                $id_column_index = array_search('generator_id', array_map('strtolower', $header));
                if ($id_column_index === false) {
                    $id_column_index = array_search('id', array_map('strtolower', $header));
                }
                if ($id_column_index === false) {
                    $id_column_index = array_search('site_id', array_map('strtolower', $header));
                }

                if ($id_column_index === false) {
                    if ($debug_mode) {
                        $debug_info .= "Error: Could not find ID column in CSV headers\n";
                    }
                    header("Location: update_generators_table.php?error=Could not identify ID column in CSV. Please ensure your CSV has a column named 'id', 'generator_id', or 'site_id'.");
                    exit();
                }

                // Find the indices for each selected column
                $csv_indices = [];
                foreach ($selected_columns as $column) {
                    if (isset($column_mapping[$column])) {
                        $csv_header = $column_mapping[$column]['csv_header'];
                        $index = array_search($csv_header, array_map('strtolower', $header));

                        if ($index === false) {
                            if ($debug_mode) {
                                $debug_info .= "Warning: Could not find column '$csv_header' in CSV headers\n";
                            }
                        } else {
                            $csv_indices[$column] = $index;
                        }
                    }
                }

                if (empty($csv_indices)) {
                    if ($debug_mode) {
                        $debug_info .= "Error: None of the selected columns could be found in the CSV\n";
                    }
                    header("Location: update_generators_table.php?error=Could not match any selected columns with CSV headers. Please check your file format.");
                    exit();
                }

                if ($debug_mode) {
                    $debug_info .= "CSV Indices for selected columns: " . print_r($csv_indices, true) . "\n";
                    $debug_info .= "ID column index: $id_column_index\n\n";
                }

                $updated_count = 0;
                $error_count = 0;
                $processed_rows = [];
                $row_number = 1; // Start from 1 to account for header row

                while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                    $row_number++;

                    // Skip if ID column is empty
                    if (empty($data[$id_column_index])) {
                        if ($debug_mode) {
                            $debug_info .= "Row $row_number: Skipping row with empty ID\n";
                        }
                        continue;
                    }

                    $id = intval($data[$id_column_index]);
                    if ($id <= 0) {
                        if ($debug_mode) {
                            $debug_info .= "Row $row_number: Skipping row with invalid ID: $id\n";
                        }
                        continue;
                    }

                    if ($debug_mode) {
                        $debug_info .= "Row $row_number: Processing row with ID: $id\n";
                        $debug_info .= "Row data: " . implode(', ', $data) . "\n";
                    }

                    // Prepare the SQL query
                    $updates = [];
                    $params = [];
                    $types = '';
                    $row_debug = ['row_number' => $row_number];

                    foreach ($selected_columns as $column) {
                        if (isset($column_mapping[$column]) && isset($csv_indices[$column])) {
                            $db_column = $column_mapping[$column]['column'];
                            $data_type = $column_mapping[$column]['type'];
                            $csv_index = $csv_indices[$column];

                            $row_debug[$column] = [
                                'db_column' => $db_column,
                                'data_type' => $data_type,
                                'csv_index' => $csv_index
                            ];

                            // Make sure the index exists in the data array
                            if (!isset($data[$csv_index])) {
                                $row_debug[$column]['status'] = "CSV index $csv_index not found in data";
                                continue;
                            }

                            $raw_value = trim($data[$csv_index]);
                            $row_debug[$column]['raw_value'] = $raw_value;

                            if (empty($raw_value) && $raw_value !== '0') {
                                $row_debug[$column]['status'] = "Empty value";
                                continue;
                            }

                            $value = null;

                            // Process value based on data type
                            if ($data_type === 'integer') {
                                // Make sure we have a valid integer
                                if (is_numeric($raw_value)) {
                                    $value = intval($raw_value);
                                    $types .= 'i';
                                    $row_debug[$column]['processed_value'] = $value;
                                    $row_debug[$column]['param_type'] = 'i';
                                } else {
                                    $row_debug[$column]['status'] = "Invalid integer value: '$raw_value'";
                                    continue;
                                }
                            } elseif ($data_type === 'datetime') {
                                // For dates, try multiple formats
                                $formats = ['Y-m-d H:i:s', 'd/m/Y H:i:s', 'm/d/Y H:i:s', 'Y-m-d', 'd/m/Y', 'm/d/Y'];
                                $date = null;

                                foreach ($formats as $format) {
                                    $date = DateTime::createFromFormat($format, $raw_value);
                                    if ($date !== false) {
                                        $row_debug[$column]['matched_format'] = $format;
                                        break;
                                    }
                                }

                                if ($date === false) {
                                    $row_debug[$column]['status'] = "Invalid date format: '$raw_value'";
                                    continue;
                                }

                                // Always append time if not present
                                $value = $date->format('Y-m-d H:i:s');
                                $types .= 's';
                                $row_debug[$column]['processed_value'] = $value;
                                $row_debug[$column]['param_type'] = 's';
                            } else {
                                $value = strval($raw_value);
                                $types .= 's';
                                $row_debug[$column]['processed_value'] = $value;
                                $row_debug[$column]['param_type'] = 's';
                            }

                            // Add to updates and params
                            $updates[] = "$db_column = ?";
                            $params[] = $value;
                            $row_debug[$column]['status'] = "Added to update";
                        }
                    }

                    if (!empty($updates)) {
                        // IMPORTANT: Changed site_id to generator_id based on apparent database design
                        // You should verify this is the correct primary key column name in your database
                        $sql = "UPDATE generators SET " . implode(', ', $updates) . " WHERE generator_id = ?";
                        $params[] = $id;
                        $types .= 'i'; // Primary key is an integer

                        $row_debug['sql'] = $sql;
                        $row_debug['params'] = $params;
                        $row_debug['types'] = $types;

                        $stmt = $conn->prepare($sql);
                        if ($stmt === false) {
                            $row_debug['prepare_error'] = $conn->error;
                            $error_count++;
                        } else {
                            $stmt->bind_param($types, ...$params);
                            $execute_result = $stmt->execute();

                            if ($execute_result) {
                                $affected_rows = $stmt->affected_rows;
                                $row_debug['affected_rows'] = $affected_rows;

                                if ($affected_rows > 0) {
                                    $updated_count++;
                                    $row_debug['status'] = "Update successful";
                                } else {
                                    $row_debug['status'] = "No rows affected (ID may not exist or values unchanged)";
                                }
                            } else {
                                $row_debug['execute_error'] = $stmt->error;
                                $error_count++;
                            }

                            $stmt->close();
                        }
                    } else {
                        $row_debug['status'] = "No columns to update";
                    }

                    $processed_rows["row_$id"] = $row_debug;
                }
                fclose($handle);

                if ($debug_mode) {
                    $debug_info .= "\nProcessed Rows:\n" . print_r($processed_rows, true);
                    $debug_info .= "\nSummary: $updated_count updated, $error_count errors\n";

                    // Store debug info in session
                    $_SESSION['debug_info'] = $debug_info;
                }

                // Create success message
                $message = "Generators Table updated successfully! ($updated_count records updated)";
                if ($error_count > 0) {
                    $message .= " ($error_count errors encountered)";
                }

                // Redirect with only the necessary parameters
                header("Location: update_generators_table.php?message=" . urlencode($message));
                exit();
            } else {
                header("Location: update_generators_table.php?error=Failed to open the file. Please try again.");
                exit();
            }
        } elseif ($file_ext === 'xlsx') {
            // For Excel files, we need to use a library
            // Check if PhpSpreadsheet is available
            if (!class_exists('PhpOffice\PhpSpreadsheet\Spreadsheet')) {
                // You can add PhpSpreadsheet via Composer if needed
                header("Location: update_generators_table.php?error=Excel processing library not available. Please upload a CSV file instead.");
                exit();
            } else {
                // This would be the code to process Excel files if PhpSpreadsheet is available
                header("Location: update_generators_table.php?error=Excel processing is not yet implemented. Please upload a CSV file instead.");
                exit();
            }
        } else {
            header("Location: update_generators_table.php?error=Unsupported file format. Please upload a CSV or Excel file.");
            exit();
        }
    } else {
        // Handle file upload errors
        $error_message = "File upload failed. ";
        switch ($_FILES['file']['error']) {
            case UPLOAD_ERR_INI_SIZE:
                $error_message .= "The uploaded file exceeds the upload_max_filesize directive in php.ini.";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $error_message .= "The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form.";
                break;
            case UPLOAD_ERR_PARTIAL:
                $error_message .= "The uploaded file was only partially uploaded.";
                break;
            case UPLOAD_ERR_NO_FILE:
                $error_message .= "No file was uploaded.";
                break;
            default:
                $error_message .= "Unknown error occurred.";
                break;
        }
        header("Location: update_generators_table.php?error=" . urlencode($error_message));
        exit();
    }
}
