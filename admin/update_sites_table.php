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
    <h1>Update Sites Table</h1>
    <div class="container">
        <form action="update_sites_table.php" method="post" enctype="multipart/form-data">
            <label for="file">Upload CSV or Excel File:</label>
            <input type="file" name="file" id="file" accept=".csv, .xlsx" required>

            <div class="column-selection">
                <label>Select Columns to Update:</label><br>
                <input type="checkbox" name="columns[]" value="status"> Up Or Down Status<br>
                <input type="checkbox" name="columns[]" value="date"> Date went Down<br>
            </div>

            <input type="submit" value="Update Sites Table">
        </form>

        <?php
        if (isset($_GET['message'])) {
            echo '<div class="message">' . htmlspecialchars($_GET['message']) . '</div>';
        }
        if (isset($_GET['error'])) {
            echo '<div class="error">' . htmlspecialchars($_GET['error']) . '</div>';
        }
        ?>
    </div>
</main>

<?php
include 'footer.php';

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['file']['tmp_name'];
        $selected_columns = $_POST['columns'] ?? []; // Get selected columns

        // Check file extension
        $file_ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
        if ($file_ext === 'csv') {
            // Process CSV file
            $handle = fopen($file, 'r');
            if ($handle !== FALSE) {
                // Skip the header row
                fgetcsv($handle);

                while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                    $id = $data[0]; // Assuming the first column is the primary key

                    // Prepare the SQL query
                    $updates = [];
                    $params = [];
                    $types = '';

                    foreach ($selected_columns as $column) {
                        $index = array_search($column, ['active_site_status', 'site_down_date']); // Map columns to indices
                        if ($index !== FALSE) {
                            $updates[] = "$column = ?";
                            $params[] = $data[$index + 1]; // +1 to skip the primary key
                            $types .= 's'; // Assume all columns are strings
                        }
                    }

                    if (!empty($updates)) {
                        $sql = "UPDATE sites SET " . implode(', ', $updates) . " WHERE primary_id = ?";
                        $params[] = $id;
                        $types .= 'i'; // Primary key is an integer

                        // Debugging: Print the SQL query and parameters
                        echo "SQL Query: $sql<br>";
                        echo "Parameters: " . print_r($params, true) . "<br>";

                        $stmt = $conn->prepare($sql);
                        if ($stmt === false) {
                            die("Prepare failed: " . $conn->error);
                        }

                        $stmt->bind_param($types, ...$params);
                        if (!$stmt->execute()) {
                            die("Execute failed: " . $stmt->error);
                        }
                    }
                }
                fclose($handle);
            }

            // Redirect with success message
            header("Location: update_sites_table.php?message=Database updated successfully!");
            exit();
        } else {
            // Redirect with error message
            header("Location: update_sites_table.php?error=Unsupported file format. Please upload a CSV file.");
            exit();
        }
    } else {
        // Redirect with error message
        header("Location: update_sites_table.php?error=File upload failed. Please try again.");
        exit();
    }
}

// End output buffering and send the output
ob_end_flush();
?>