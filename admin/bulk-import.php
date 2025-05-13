<?php
include './connection/db_connection.php';
session_start();
if (!isset($_SESSION["username"])) {
    header('location: ../index.php');
}

include './header_aside.php';

// Include necessary libraries
require_once '../vendor/autoload.php';

// Define the page title
$pageTitle = "Bulk Data Import";
?>

<!--main content-->
<main id="main" class="main">

    <?php if (isset($_SESSION['message'])): ?>
        <div class="container" id="message-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-<?php echo $_SESSION['message_type']; ?> d-flex justify-content-between align-items-center">
                        <?php echo $_SESSION['message']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        </div>
    <?php
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
    endif; ?>

    <section class="section">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Bulk Data Import</h5>

                <form method="POST" action="bulk-import-process.php" enctype="multipart/form-data" class="row g-3 needs-validation" novalidate id="import_form">
                    <div class="col-md-6">
                        <label for="target_table" class="form-label">Select Table to Update *</label>
                        <select class="form-select" id="target_table" name="target_table" required>
                            <option value="" selected disabled>Choose table...</option>
                            <option value="sites">Sites</option>
                            <option value="generators">Generators</option>
                        </select>
                        <div class="invalid-feedback">
                            Please select a table to update.
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="import_file" class="form-label">Select File (CSV or Excel) *</label>
                        <input type="file" class="form-control" id="import_file" name="import_file" accept=".csv,.xlsx,.xls" required>
                        <div class="invalid-feedback">
                            Please select a valid CSV or Excel file.
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="update_method" class="form-label">Update Method *</label>
                        <select class="form-select" id="update_method" name="update_method" required>
                            <option value="add_new" selected>Add new records only</option>
                            <option value="update_existing">Update existing records</option>
                            <option value="upsert">Add new or update existing</option>
                        </select>
                        <div class="invalid-feedback">
                            Please select an update method.
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="key_field" class="form-label">Unique Key Field *</label>
                        <select class="form-select" id="key_field" name="key_field" required>
                            <option value="" selected disabled>Select key field...</option>
                            <!-- Options will be populated by JavaScript -->
                        </select>
                        <div class="invalid-feedback">
                            Please select the unique key field.
                        </div>
                    </div>

                    <div class="col-12 mt-3" id="column_selection_container" style="display: none;">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">Select Columns to Update</h6>
                                <p>Choose which columns you want to include in the import operation:</p>
                                <div class="mb-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="select_all_columns">Select All</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="deselect_all_columns">Deselect All</button>
                                </div>
                                <div id="available_columns" class="row"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div id="field_mapping_container" style="display: none;">
                            <h6>Field Mapping</h6>
                            <p>Match the columns in your file to database fields. Unselected fields will be skipped.</p>
                            <div id="file_columns" class="mb-3"></div>
                            <div id="field_mapping"></div>
                        </div>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary" id="submit_btn" disabled>Process Import</button>
                        <button type="reset" class="btn btn-secondary">Reset</button>
                    </div>
                </form>

                <!-- Preview Section -->
                <div class="mt-4" id="preview_section" style="display: none;">
                    <h5>Import Preview</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="preview_table">
                            <thead id="preview_headers"></thead>
                            <tbody id="preview_data"></tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </section>

</main>

<?php include './footer.php'; ?>

<!-- JavaScript for dynamic field mapping -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const targetTable = document.getElementById('target_table');
        const keyField = document.getElementById('key_field');
        const importFile = document.getElementById('import_file');
        const fieldMappingContainer = document.getElementById('field_mapping_container');
        const previewSection = document.getElementById('preview_section');
        const submitBtn = document.getElementById('submit_btn');

        // Define available fields for each table
        const tableFields = {
            'sites': [
                'primary_id', 'secondary_id', 'rf_id', 'site_name',
                'tx_site_type', 'department', 'city_region', 'latitude',
                'longitude', 'class', 'type_of_site', 'installation_date',
                'site_auto_status', 'site_status', 'active_site_status',
                'downtime_date', 'downtime_duration', 'created_at',
                'problem_summary', 'comment'
            ],
            'generators': [
                'site_id', 'hybrid_rbs_battery_type', 'No_of_hybrid_rbs_batteries',
                'battery_capacity', 'power_type', 'status', 'installation_date',
                'percent_op_time', 'brand', 'capacity', 'engine', 'maintenance_scope',
                'number_of_ac', 'gen_mode', 'actual_run_hours'
            ]
        };

        // Define key fields for each table
        const keyFields = {
            'sites': ['primary_id', 'rf_id', 'secondary_id'],
            'generators': ['site_id']
        };

        const columnSelectionContainer = document.getElementById('column_selection_container');
        const selectAllColumnsBtn = document.getElementById('select_all_columns');
        const deselectAllColumnsBtn = document.getElementById('deselect_all_columns');

        // Update key field options and display available columns when table changes
        targetTable.addEventListener('change', function() {
            const table = this.value;
            keyField.innerHTML = '<option value="" selected disabled>Select key field...</option>';

            // Reset file input and hide preview sections
            document.getElementById('import_file').value = '';
            fieldMappingContainer.style.display = 'none';
            previewSection.style.display = 'none';
            columnSelectionContainer.style.display = 'none';
            submitBtn.disabled = true;

            if (table && keyFields[table]) {
                // Update key field options
                keyFields[table].forEach(field => {
                    const option = document.createElement('option');
                    option.value = field;
                    option.textContent = field;
                    keyField.appendChild(option);
                });

                // Display available columns for selection
                const availableColumnsDiv = document.getElementById('available_columns');
                availableColumnsDiv.innerHTML = '';

                if (tableFields[table]) {
                    tableFields[table].forEach(field => {
                        const colDiv = document.createElement('div');
                        colDiv.className = 'col-md-4 mb-2';

                        const checkboxWrapper = document.createElement('div');
                        checkboxWrapper.className = 'form-check';

                        const checkbox = document.createElement('input');
                        checkbox.type = 'checkbox';
                        checkbox.className = 'form-check-input column-checkbox';
                        checkbox.id = 'col_' + field;
                        checkbox.name = 'selected_columns[]';
                        checkbox.value = field;
                        checkbox.checked = true; // Default to checked

                        const label = document.createElement('label');
                        label.className = 'form-check-label';
                        label.htmlFor = 'col_' + field;
                        label.textContent = field;

                        checkboxWrapper.appendChild(checkbox);
                        checkboxWrapper.appendChild(label);
                        colDiv.appendChild(checkboxWrapper);
                        availableColumnsDiv.appendChild(colDiv);
                    });

                    columnSelectionContainer.style.display = 'block';
                }
            }
        });

        // Handle select all columns button
        selectAllColumnsBtn.addEventListener('click', function() {
            document.querySelectorAll('.column-checkbox').forEach(checkbox => {
                checkbox.checked = true;
            });
        });

        // Handle deselect all columns button
        deselectAllColumnsBtn.addEventListener('click', function() {
            document.querySelectorAll('.column-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });
        });

        // Handle file selection for preview
        importFile.addEventListener('change', function() {
            if (this.files.length > 0 && targetTable.value) {
                const file = this.files[0];
                const formData = new FormData();
                formData.append('action', 'preview');
                formData.append('target_table', targetTable.value);
                formData.append('import_file', file);

                // Get selected columns
                const selectedColumns = Array.from(document.querySelectorAll('.column-checkbox:checked')).map(cb => cb.value);
                formData.append('selected_columns', JSON.stringify(selectedColumns));

                // Display loading indicator
                fieldMappingContainer.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2">Processing file, please wait...</p></div>';
                fieldMappingContainer.style.display = 'block';
                previewSection.style.display = 'none';

                fetch('bulk-import-process.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Reset field mapping container
                        fieldMappingContainer.innerHTML = '<h6>Field Mapping</h6><p>Match the columns in your file to database fields. Unselected fields will be skipped.</p><div id="file_columns" class="mb-3"></div><div id="field_mapping"></div>';

                        if (data.success) {
                            // Show file columns for mapping
                            const fileColumnsDiv = document.getElementById('file_columns');
                            fileColumnsDiv.innerHTML = '<strong>File Columns:</strong> ' + data.columns.join(', ');

                            // Generate field mapping options
                            const fieldMappingDiv = document.getElementById('field_mapping');
                            fieldMappingDiv.innerHTML = '';

                            data.columns.forEach(column => {
                                const div = document.createElement('div');
                                div.className = 'mb-3 row align-items-center';

                                const label = document.createElement('label');
                                label.className = 'col-sm-4 col-form-label';
                                label.textContent = column;

                                const select = document.createElement('select');
                                select.className = 'form-select';
                                select.name = 'field_map[' + column + ']';

                                // Add empty option
                                const emptyOption = document.createElement('option');
                                emptyOption.value = '';
                                emptyOption.textContent = '-- Skip this column --';
                                select.appendChild(emptyOption);

                                // Add database fields
                                tableFields[targetTable.value].forEach(dbField => {
                                    const option = document.createElement('option');
                                    option.value = dbField;
                                    option.textContent = dbField;

                                    // Try to auto-match fields with similar names
                                    if (column.toLowerCase().replace(/[^a-z0-9]/g, '') ===
                                        dbField.toLowerCase().replace(/[^a-z0-9]/g, '')) {
                                        option.selected = true;
                                    }
                                    select.appendChild(option);
                                });

                                const colDiv = document.createElement('div');
                                colDiv.className = 'col-sm-8';
                                colDiv.appendChild(select);

                                div.appendChild(label);
                                div.appendChild(colDiv);
                                fieldMappingDiv.appendChild(div);
                            });

                            // Show preview
                            const previewHeaders = document.getElementById('preview_headers');
                            previewHeaders.innerHTML = '';
                            const headerRow = document.createElement('tr');
                            data.columns.forEach(column => {
                                const th = document.createElement('th');
                                th.textContent = column;
                                headerRow.appendChild(th);
                            });
                            previewHeaders.appendChild(headerRow);

                            const previewData = document.getElementById('preview_data');
                            previewData.innerHTML = '';

                            // Show up to 5 rows for preview
                            const previewRows = data.preview.slice(0, 5);
                            previewRows.forEach(row => {
                                const tr = document.createElement('tr');
                                data.columns.forEach(column => {
                                    const td = document.createElement('td');
                                    td.textContent = row[column] !== undefined ? row[column] : '';
                                    tr.appendChild(td);
                                });
                                previewData.appendChild(tr);
                            });

                            fieldMappingContainer.style.display = 'block';
                            previewSection.style.display = 'block';
                            submitBtn.disabled = false;
                        } else {
                            fieldMappingContainer.innerHTML = '<div class="alert alert-danger">' + data.message + '</div>';
                            previewSection.style.display = 'none';
                            submitBtn.disabled = true;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        fieldMappingContainer.innerHTML = '<div class="alert alert-danger">An error occurred while processing the file. Please check the file format and try again.</div>';
                        previewSection.style.display = 'none';
                        submitBtn.disabled = true;
                    });
            } else {
                fieldMappingContainer.style.display = 'none';
                previewSection.style.display = 'none';
                submitBtn.disabled = true;
            }
        });

        // Form validation and submission
        const form = document.getElementById('import_form');
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                form.classList.add('was-validated');
                return;
            }

            // Add selected columns to the form
            const selectedColumns = Array.from(document.querySelectorAll('.column-checkbox:checked')).map(cb => cb.value);
            if (selectedColumns.length === 0) {
                event.preventDefault();
                alert('Please select at least one column to import.');
                return;
            }

            // Add selected columns to form
            selectedColumns.forEach(column => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'selected_columns[]';
                input.value = column;
                form.appendChild(input);
            });

            form.classList.add('was-validated');
        });
    });
</script>