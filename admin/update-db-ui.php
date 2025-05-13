<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Updater</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .status-message {
            margin-top: 20px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="text-center">Database Updater</h1>
        <form id="update-form">
            <div class="form-group">
                <label for="table-select">Select Table:</label>
                <select id="table-select" class="form-control" required>
                    <option value="">-- Select a table --</option>
                    {% for table in tables %}
                    <option value="{{ table }}">{{ table }}</option>
                    {% endfor %}
                </select>
            </div>
            <div class="form-group">
                <label for="file-upload">Upload File:</label>
                <input type="file" id="file-upload" class="form-control" accept=".csv, .xlsx" required>
            </div>
            <div class="form-group">
                <label>Select Columns to Update:</label>
                <div id="column-list" class="form-control" style="height: 150px; overflow-y: auto;"></div>
            </div>
            <button type="submit" class="btn btn-primary w-100">Update Database</button>
        </form>
        <div id="status-message" class="status-message text-center"></div>
    </div>

    <script>
        document.getElementById('table-select').addEventListener('change', function() {
            const tableName = this.value;
            if (tableName) {
                fetch('/get_columns', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            table_name: tableName
                        }),
                    })
                    .then(response => response.json())
                    .then(columns => {
                        const columnList = document.getElementById('column-list');
                        columnList.innerHTML = columns.map(column => `
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="columns[]" value="${column}" id="${column}">
              <label class="form-check-label" for="${column}">${column}</label>
            </div>
          `).join('');
                    });
            }
        });

        document.getElementById('update-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData();
            formData.append('table_name', document.getElementById('table-select').value);
            formData.append('file', document.getElementById('file-upload').files[0]);
            const columns = Array.from(document.querySelectorAll('input[name="columns[]"]:checked')).map(input => input.value);
            columns.forEach(column => formData.append('columns[]', column));

            fetch('/update', {
                    method: 'POST',
                    body: formData,
                })
                .then(response => response.text())
                .then(result => {
                    document.getElementById('status-message').textContent = result;
                })
                .catch(error => {
                    document.getElementById('status-message').textContent = 'An error occurred.';
                });
        });
    </script>
</body>

</html>