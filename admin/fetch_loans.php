<?php
include 'connection/db_connection.php';

if (isset($_POST['borrowerId'])) {
    $borrowerId = $_POST['borrowerId'];

    // Query to fetch loans for the selected borrower
    $loanQuery = "SELECT * FROM loans WHERE borrower = '$borrowerId' ORDER BY loan_number ASC";
    $loanss = $conn->query($loanQuery);

    $options = '<option value="">Select Loan Number</option>';

    while ($loan = $loanss->fetch_assoc()) {
        $options .= '<option value="' . $loan['loan_number'] . '">' . $loan['borrower'] . ' - Loan Number - ' . $loan['loan_number'] . '</option>';
    }

    echo $options;
}
?>
