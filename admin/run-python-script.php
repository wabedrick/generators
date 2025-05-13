<?php
// run_script.php

// Path to your Python script
$pythonScriptPath = './app.py';

// Execute the Python script
$output = shell_exec("python3 $pythonScriptPath 2>&1");

// Return the output as JSON
header('Content-Type: application/json');
echo json_encode(['output' => $output]);
