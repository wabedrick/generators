<?php
include '../connection/db_connection.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION["username"])) {
    header('location: ../../index.php');
    exit;
}

// Include PHPExcel or PhpSpreadsheet library (depending on what you're using)
require_once '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// Set content type to JSON for AJAX responses
if (isset($_POST['action']) && $_POST['action'] == 'preview') {
    header('Content-Type: application/json');
}

// Define available fields for each table
$tableFields = [
    'sites' => [
        'primary_id',
        'secondary_id',
        'rf_id',
        'site_name',
        'tx_site_type',
        'department',
        'city_region',
        'latitude',
        'longitude',
        'class',
        'type_of_site',
        'installation_date',
        'site_auto_status',
        'site_status',
        'active_site_status',
        'downtime_date',
        'downtime_duration',
        'created_at',
        'problem_summary',
        'comment'
    ],
    'generators' => [
        'site_id',
        'hybrid_rbs_battery_type',
        'No_of_hybrid_rbs_batteries',
        'battery_capacity',
        'power_type',
        'status',
        'installation_date',
        'percent_op_time',
        'brand',
        'capacity',
        'engine',
        'maintenance_scope',
        'number_of_ac',
        'gen_mode',
        'actual_run_hours'
    ]
];

/**
 * Parse the uploaded file (CSV or Excel)
 * @param string $filePath Path to the uploaded file
 * @return array Array of data with headers and rows
 */
function parseFile($filePath)
{
    $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

    if ($fileExtension == 'csv') {
        return parseCSV($filePath);
    } else if (in_array($fileExtension, ['xlsx', 'xls'])) {
        return parseExcel($filePath);
    } else {
        throw new Exception("Unsupported file format. Please upload a CSV or Excel file.");
    }
}

/**
 * Parse CSV file
 * @param string $filePath Path to the CSV file
 * @return array Array of data with headers and rows
 */
function parseCSV($filePath)
{
    $handle = fopen($filePath, "r");
    if (!$handle) {
        throw new Exception("Failed to open the CSV file.");
    }

    // Read the header row
    $headers = fgetcsv($handle);
    if (!$headers) {
        fclose($handle);
        throw new Exception("CSV file appears to be empty or has invalid format.");
    }

    // Clean header names
    $headers = array_map('trim', $headers);

    // Read all data rows
    $data = [];
    while (($row = fgetcsv($handle)) !== FALSE) {
        if (count($row) < count($headers)) {
            // Pad the row with empty values if needed
            $row = array_pad($row, count($headers), "");
        } else if (count($row) > count($headers)) {
            // Truncate the row if it has more columns than headers
            $row = array_slice($row, 0, count($headers));
        }

        // Create associative array with header keys
        $rowData = [];
        foreach ($headers as $index => $header) {
            $rowData[$header] = isset($row[$index]) ? trim($row[$index]) : "";
        }
        $data[] = $rowData;
    }

    fclose($handle);
    return ['headers' => $headers, 'data' => $data];
}

/**
 * Parse Excel file
 * @param string $filePath Path to the Excel file
 * @return array Array of data with headers and rows
 */
function parseExcel($filePath)
{
    try {
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow();
        $highestColumn = $worksheet->getHighestColumn();
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

        // Read header row
        $headers = [];
        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $cellCoordinate = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . '1';
            $headerValue = trim($worksheet->getCell($cellCoordinate)->getValue());
            if (!empty($headerValue)) {
                $headers[] = $headerValue;
            }
        }

        if (empty($headers)) {
            throw new Exception("Excel file appears to be empty or has invalid format.");
        }

        // Read data rows
        $data = [];
        for ($row = 2; $row <= $highestRow; $row++) {
            $rowData = [];
            $allEmpty = true;

            foreach ($headers as $index => $header) {
                $cellCoordinate = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1) . $row;
                $cellValue = $worksheet->getCell($cellCoordinate)->getValue();
                $cellValue = $cellValue !== null ? trim((string)$cellValue) : "";
                $rowData[$header] = $cellValue;

                if (!empty($cellValue)) {
                    $allEmpty = false;
                }
            }

            // Skip completely empty rows
            if (!$allEmpty) {
                $data[] = $rowData;
            }
        }

        return ['headers' => $headers, 'data' => $data];
    } catch (Exception $e) {
        throw new Exception("Error processing Excel file: " . $e->getMessage());
    }
}

// Handle preview action
if (isset($_POST['action']) && $_POST['action'] == 'preview') {
    try {
        // Validate table name
        $targetTable = isset($_POST['target_table']) ? $_POST['target_table'] : '';
        if (!$targetTable || !isset($tableFields[$targetTable])) {
            echo json_encode(['success' => false, 'message' => 'Invalid table selection.']);
            exit;
        }

        // Check if file was uploaded
        if (!isset($_FILES['import_file']) || $_FILES['import_file']['error'] != UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'File upload failed. Please try again.']);
            exit;
        }

        // Parse the uploaded file
        $fileData = parseFile($_FILES['import_file']['tmp_name']);

        // Return preview data as JSON
        echo json_encode([
            'success' => true,
            'columns' => $fileData['headers'],
            'preview' => array_slice($fileData['data'], 0, 5) // Just return first 5 rows for preview
        ]);
        exit;
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }
}

// Process the actual import
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['action'])) {
    try {
        // Validate required parameters
        $targetTable = isset($_POST['target_table']) ? $_POST['target_table'] : '';
        $updateMethod = isset($_POST['update_method']) ? $_POST['update_method'] : '';
        $keyField = isset($_POST['key_field']) ? $_POST['key_field'] : '';

        if (!$targetTable || !isset($tableFields[$targetTable])) {
            throw new Exception('Invalid table selection.');
        }

        if (!in_array($updateMethod, ['add_new', 'update_existing', 'upsert'])) {
            throw new Exception('Invalid update method.');
        }

        if (!$keyField || !in_array($keyField, $tableFields[$targetTable])) {
            throw new Exception('Invalid key field.');
        }

        // Check if field mapping was provided
        if (!isset($_POST['field_map']) || !is_array($_POST['field_map'])) {
            throw new Exception('Field mapping is required.');
        }

        // Check if file was uploaded
        if (!isset($_FILES['import_file']) || $_FILES['import_file']['error'] != UPLOAD_ERR_OK) {
            throw new Exception('File upload failed. Please try again.');
        }

        // Parse the uploaded file
        $fileData = parseFile($_FILES['import_file']['tmp_name']);
        $data = $fileData['data'];

        if (empty($data)) {
            throw new Exception('No data found in the uploaded file.');
        }

        // Prepare field mapping
        $fieldMap = [];
        foreach ($_POST['field_map'] as $fileColumn => $dbField) {
            if (!empty($dbField) && in_array($dbField, $tableFields[$targetTable])) {
                $fieldMap[$fileColumn] = $dbField;
            }
        }

        if (empty($fieldMap)) {
            throw new Exception('No valid field mappings were provided.');
        }

        // Check if key field is included in the mapping
        $keyFieldMapped = false;
        foreach ($fieldMap as $fileColumn => $dbField) {
            if ($dbField == $keyField) {
                $keyFieldMapped = true;
                break;
            }
        }

        if (!$keyFieldMapped && $updateMethod != 'add_new') {
            throw new Exception("The key field '$keyField' must be mapped for the selected update method.");
        }

        // Process the data based on the update method
        $recordsAdded = 0;
        $recordsUpdated = 0;
        $recordsSkipped = 0;
        $errors = [];

        foreach ($data as $index => $row) {
            try {
                // Prepare data for database operation
                $values = [];
                foreach ($fieldMap as $fileColumn => $dbField) {
                    if (isset($row[$fileColumn])) {
                        $values[$dbField] = $row[$fileColumn];
                    }
                }

                if (empty($values)) {
                    $recordsSkipped++;
                    continue;
                }

                // Check if key field value exists for update/upsert methods
                if ($updateMethod != 'add_new') {
                    if (!isset($values[$keyField]) || empty($values[$keyField])) {
                        $recordsSkipped++;
                        continue;
                    }

                    // Check if record exists
                    $stmt = $conn->prepare("SELECT COUNT(*) FROM $targetTable WHERE $keyField = ?");
                    $stmt->bind_param('s', $values[$keyField]);
                    $stmt->execute();
                    $stmt->bind_result($count);
                    $stmt->fetch();
                    $stmt->close();

                    $recordExists = ($count > 0);

                    if ($updateMethod == 'update_existing' && !$recordExists) {
                        $recordsSkipped++;
                        continue;
                    }

                    if ($updateMethod == 'upsert') {
                        if ($recordExists) {
                            // Update existing record
                            $updateFields = [];
                            $updateTypes = '';
                            $updateValues = [];

                            foreach ($values as $field => $value) {
                                if ($field != $keyField) {
                                    $updateFields[] = "$field = ?";
                                    $updateTypes .= 's'; // Assume string for simplicity
                                    $updateValues[] = $value;
                                }
                            }

                            // Add key field at the end for WHERE clause
                            $updateTypes .= 's';
                            $updateValues[] = $values[$keyField];

                            if (!empty($updateFields)) {
                                $sql = "UPDATE $targetTable SET " . implode(', ', $updateFields) . " WHERE $keyField = ?";
                                $stmt = $conn->prepare($sql);

                                // Dynamically bind parameters
                                $bindParams = array($updateTypes);
                                foreach ($updateValues as $key => $value) {
                                    $bindParams[] = &$updateValues[$key];
                                }
                                call_user_func_array(array($stmt, 'bind_param'), $bindParams);

                                $stmt->execute();
                                $stmt->close();
                                $recordsUpdated++;
                            } else {
                                $recordsSkipped++;
                            }
                        } else {
                            // Insert new record
                            $insertFields = array_keys($values);
                            $placeholders = array_fill(0, count($insertFields), '?');
                            $insertTypes = str_repeat('s', count($insertFields)); // Assume string for simplicity
                            $insertValues = array_values($values);

                            $sql = "INSERT INTO $targetTable (" . implode(', ', $insertFields) . ") VALUES (" . implode(', ', $placeholders) . ")";
                            $stmt = $conn->prepare($sql);

                            // Dynamically bind parameters
                            $bindParams = array($insertTypes);
                            foreach ($insertValues as $key => $value) {
                                $bindParams[] = &$insertValues[$key];
                            }
                            call_user_func_array(array($stmt, 'bind_param'), $bindParams);

                            $stmt->execute();
                            $stmt->close();
                            $recordsAdded++;
                        }
                    } else if ($updateMethod == 'update_existing' && $recordExists) {
                        // Only update existing record
                        $updateFields = [];
                        $updateTypes = '';
                        $updateValues = [];

                        foreach ($values as $field => $value) {
                            if ($field != $keyField) {
                                $updateFields[] = "$field = ?";
                                $updateTypes .= 's'; // Assume string for simplicity
                                $updateValues[] = $value;
                            }
                        }

                        // Add key field at the end for WHERE clause
                        $updateTypes .= 's';
                        $updateValues[] = $values[$keyField];

                        if (!empty($updateFields)) {
                            $sql = "UPDATE $targetTable SET " . implode(', ', $updateFields) . " WHERE $keyField = ?";
                            $stmt = $conn->prepare($sql);

                            // Dynamically bind parameters
                            $bindParams = array($updateTypes);
                            foreach ($updateValues as $key => $value) {
                                $bindParams[] = &$updateValues[$key];
                            }
                            call_user_func_array(array($stmt, 'bind_param'), $bindParams);

                            $stmt->execute();
                            $stmt->close();
                            $recordsUpdated++;
                        } else {
                            $recordsSkipped++;
                        }
                    }
                } else {
                    // Add new record only
                    $insertFields = array_keys($values);
                    $placeholders = array_fill(0, count($insertFields), '?');
                    $insertTypes = str_repeat('s', count($insertFields)); // Assume string for simplicity
                    $insertValues = array_values($values);

                    $sql = "INSERT INTO $targetTable (" . implode(', ', $insertFields) . ") VALUES (" . implode(', ', $placeholders) . ")";
                    $stmt = $conn->prepare($sql);

                    // Dynamically bind parameters
                    $bindParams = array($insertTypes);
                    foreach ($insertValues as $key => $value) {
                        $bindParams[] = &$insertValues[$key];
                    }
                    call_user_func_array(array($stmt, 'bind_param'), $bindParams);

                    $stmt->execute();
                    $stmt->close();
                    $recordsAdded++;
                }
            } catch (Exception $e) {
                $errors[] = "Row " . ($index + 1) . ": " . $e->getMessage();
            }
        }

        // Set success message
        $message = "Import completed: $recordsAdded records added, $recordsUpdated records updated, $recordsSkipped records skipped.";
        if (!empty($errors)) {
            $message .= " There were " . count($errors) . " errors.";
            // Optionally log errors for admin review
            error_log("Bulk import errors: " . implode("; ", $errors));
        }

        $_SESSION['message'] = $message;
        $_SESSION['message_type'] = 'success';
    } catch (Exception $e) {
        $_SESSION['message'] = "Import failed: " . $e->getMessage();
        $_SESSION['message_type'] = 'danger';
    }

    // Redirect back to import page
    header('Location: bulk-import.php');
    exit;
}
