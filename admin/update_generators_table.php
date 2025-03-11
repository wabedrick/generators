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

    /* Modal Popup Styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .modal-content {
        position: relative;
        background-color: #fff;
        margin: 15% auto;
        padding: 20px;
        border-radius: 5px;
        width: 50%;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        animation: modalopen 0.4s;
    }

    @keyframes modalopen {
        from {
            opacity: 0;
            transform: translateY(-60px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .success-modal .modal-content {
        border-top: 5px solid #28a745;
    }

    .error-modal .modal-content {
        border-top: 5px solid #dc3545;
    }

    .close-btn {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }

    .close-btn:hover {
        color: #333;
    }

    .modal-header {
        padding: 10px 0;
        border-bottom: 1px solid #eee;
    }

    .modal-header h2 {
        margin: 0;
        color: #333;
    }

    .modal-body {
        padding: 20px 0;
    }

    .modal-footer {
        padding: 10px 0;
        text-align: right;
        border-top: 1px solid #eee;
    }

    .btn {
        padding: 8px 16px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .btn:hover {
        background-color: #0056b3;
    }

    /* Debug info styling */
    .debug-info {
        margin-top: 20px;
        padding: 15px;
        background-color: #f8f9fa;
        border: 1px solid #ddd;
        border-radius: 5px;
    }

    .debug-info h3 {
        margin-top: 0;
        color: #333;
    }

    .debug-info pre {
        background-color: #f1f1f1;
        padding: 10px;
        border-radius: 5px;
        overflow-x: auto;
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

<script>
    // Function to show modal with message
    function showModal(modalId, message) {
        document.getElementById(modalId).style.display = "block";
        document.getElementById(modalId === 'successModal' ? 'successMessage' : 'errorMessage').textContent = message;
    }

    // Function to close modal
    function closeModal(modalId) {
        document.getElementById(modalId).style.display = "none";
    }

    // Close modal when clicking on X
    document.querySelectorAll('.close-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            this.closest('.modal').style.display = 'none';
        });
    });

    // Close modal when clicking outside the modal content
    window.addEventListener('click', function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
        }
    });

    // Check for URL parameters to show modal
    window.onload = function() {
        const urlParams = new URLSearchParams(window.location.search);
        const successMsg = urlParams.get('message');
        const errorMsg = urlParams.get('error');

        if (successMsg) {
            showModal('successModal', successMsg);
            // Remove parameters from URL without refreshing the page
            window.history.replaceState({}, document.title, window.location.pathname);
        } else if (errorMsg) {
            showModal('errorModal', errorMsg);
            // Remove parameters from URL without refreshing the page
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    };
</script>

<?php
include 'footer.php';

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

        // Map CSV columns to database columns and their data types
        $column_mapping = [
            'actual-rhrs' => ['column' => 'latest_actual_run_hrs', 'type' => 'integer', 'csv_index' => 1],
            'date-updated' => ['column' => 'rhr_update_date', 'type' => 'datetime', 'csv_index' => 2],
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
                // Read and display the header row
                $header = fgetcsv($handle);

                if ($debug_mode) {
                    $debug_info .= "CSV Headers: " . implode(', ', $header) . "\n\n";
                }

                $updated_count = 0;
                $error_count = 0;
                $processed_rows = [];

                while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                    if (empty($data[0])) {
                        if ($debug_mode) {
                            $debug_info .= "Skipping row with empty ID\n";
                        }
                        continue; // Skip empty rows
                    }

                    $id = intval($data[0]); // Assuming the first column is the generator_id
                    if ($id <= 0) {
                        if ($debug_mode) {
                            $debug_info .= "Skipping row with invalid ID: $id\n";
                        }
                        continue; // Skip invalid IDs
                    }

                    if ($debug_mode) {
                        $debug_info .= "Processing row with ID: $id\n";
                        $debug_info .= "Row data: " . implode(', ', $data) . "\n";
                    }

                    // Prepare the SQL query
                    $updates = [];
                    $params = [];
                    $types = '';
                    $row_debug = [];

                    foreach ($selected_columns as $column) {
                        if (isset($column_mapping[$column])) {
                            $db_column = $column_mapping[$column]['column'];
                            $data_type = $column_mapping[$column]['type'];
                            $csv_index = $column_mapping[$column]['csv_index'];

                            $row_debug[$column] = [
                                'db_column' => $db_column,
                                'data_type' => $data_type,
                                'csv_index' => $csv_index
                            ];

                            // Make sure the index exists in the data array
                            if (!isset($data[$csv_index])) {
                                $row_debug[$column]['status'] = "CSV index $csv_index not found in data";
                                continue; // Skip this column but continue with others
                            }

                            $raw_value = trim($data[$csv_index]);
                            $row_debug[$column]['raw_value'] = $raw_value;

                            if (empty($raw_value) && $raw_value !== '0') {
                                $row_debug[$column]['status'] = "Empty value";
                                continue; // Skip empty values
                            }

                            $value = null;

                            // Process value based on data type
                            if ($data_type === 'integer') {
                                $value = intval($raw_value);
                                $types .= 'i';
                                $row_debug[$column]['processed_value'] = $value;
                                $row_debug[$column]['param_type'] = 'i';
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
                                    $row_debug[$column]['status'] = "Invalid date format";
                                    $error_count++;
                                    continue; // Skip this column but continue with others
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
                        $sql = "UPDATE generators SET " . implode(', ', $updates) . " WHERE site_id = ?";
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

                    // Store debug info in session instead of URL
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
            // Check if PHPExcel or PhpSpreadsheet is available
            if (!class_exists('PHPExcel') && !class_exists('PhpOffice\PhpSpreadsheet\Spreadsheet')) {
                header("Location: update_generators_table.php?error=Excel processing library not available. Please upload a CSV file instead.");
                exit();
            }

            // This example assumes PHPExcel/PhpSpreadsheet is not available and redirects with an error
            header("Location: update_generators_table.php?error=Excel processing is not implemented. Please upload a CSV file instead.");
            exit();
        } else {
            // Redirect with error message
            header("Location: update_generators_table.php?error=Unsupported file format. Please upload a CSV or Excel file.");
            exit();
        }
    } else {
        // Redirect with error message for file upload error
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

// End output buffering and send the output
ob_end_flush();
?>