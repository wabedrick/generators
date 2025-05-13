from flask import Flask, render_template, request, jsonify
import pandas as pd
from sqlalchemy import create_engine, MetaData, Table, select, update, inspect
import chardet
import os

app = Flask(__name__)

# MySQL database connection details (update these as per your database)
DATABASE_URI = 'mysql+pymysql://root:''@localhost/power_database'
engine = create_engine(DATABASE_URI)
metadata = MetaData()

def load_table_names():
    """Load table names from the database."""
    inspector = inspect(engine)
    return inspector.get_table_names()

def load_table_columns(table_name):
    """Load column names for a specific table."""
    metadata.reflect(bind=engine, only=[table_name])
    table = Table(table_name, metadata, autoload_with=engine)
    return table.columns.keys()

def detect_encoding(file_path):
    """Detect the encoding of a file."""
    with open(file_path, 'rb') as f:
        raw_data = f.read()
    result = chardet.detect(raw_data)
    return result['encoding']

def update_table_from_file(table_name, file_path, selected_columns):
    """Update the database table using data from the CSV/Excel file."""
    try:
        # Read the file
        if file_path.endswith('.csv'):
            encoding = detect_encoding(file_path)  # Detect encoding
            df = pd.read_csv(file_path, encoding=encoding)
        elif file_path.endswith('.xlsx'):
            df = pd.read_excel(file_path)
        else:
            return "Unsupported file format. Please use CSV or Excel."

        # Replace NaN values with None
        df = df.where(pd.notnull(df), None)

        # Load the table from the database
        with engine.connect() as connection:
            metadata.reflect(bind=engine)  # Reflect the database schema
            table = Table(table_name, metadata, autoload_with=engine)

            # Identify the primary key
            if table.primary_key:
                primary_key = table.primary_key.columns.keys()[0]  # Primary key
            else:
                primary_key = table.columns.keys()[1]  # Second column as fallback

            auto_increment_field = table.columns.keys()[0]  # First column is auto-incremented

            # Map column names to their indices in the result tuple
            column_indices = {column: idx for idx, column in enumerate(table.columns.keys())}

            for _, row in df.iterrows():
                # Check if the row exists in the database
                stmt = select(table).where(table.c[primary_key] == row[primary_key])
                result = connection.execute(stmt).fetchone()

                if result:
                    # Compare each column and update if there are changes
                    update_data = {}
                    for column in selected_columns:
                        if column == auto_increment_field:
                            continue
                        if str(row[column]) != str(result[column_indices[column]]):
                            update_data[column] = row[column]

                    if update_data:
                        stmt = update(table).where(table.c[primary_key] == row[primary_key]).values(**update_data)
                        connection.execute(stmt)

            # Commit the transaction
            connection.commit()

        return "Database updated successfully!"

    except Exception as e:
        return f"An error occurred: {e}"

@app.route('/')
def index():
    """Render the main page."""
    tables = load_table_names()
    return render_template('index.html', tables=tables)

@app.route('/get_columns', methods=['POST'])
def get_columns():
    """Get columns for a selected table."""
    table_name = request.json['table_name']
    columns = load_table_columns(table_name)
    return jsonify(columns)

@app.route('/update', methods=['POST'])
def update_table():
    """Handle the update process."""
    table_name = request.form['table_name']
    file = request.files['file']
    selected_columns = request.form.getlist('columns[]')

    # Save the uploaded file temporarily
    file_path = os.path.join('uploads', file.filename)
    file.save(file_path)

    # Update the database
    result = update_table_from_file(table_name, file_path, selected_columns)

    # Clean up the uploaded file
    os.remove(file_path)

    return result

if __name__ == '__main__':
    # Create the uploads directory if it doesn't exist
    if not os.path.exists('uploads'):
        os.makedirs('uploads')
    app.run(debug=True)