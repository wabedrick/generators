<?php
include 'connection/db_connection.php';

if($_SERVER['REQUEST_METHOD'] ==='POST'){
   
    $month = mysqli_real_escape_string($conn,$_POST['month']);
    $household = mysqli_real_escape_string($conn,$_POST['household']);
    $amountInvested =  mysqli_real_escape_string($conn,$_POST['amountInvested']);
    $date = mysqli_real_escape_string($conn, $_POST['date']);

    
    $sql_total = "SELECT SUM(expense_amount) as total_expenses FROM expenses";
    $result = $conn->query($sql_total);
    $row = mysqli_fetch_assoc($result);
    try{
      if($row['total_expenses'] !== null){
        $total_expenses = $row['total_expenses'];
      }
      $total_expenses = 0;
    }
    catch(Exception $e){
      echo 'Error: '.$e;
    }
    

    $query = "INSERT INTO administrative (`year_month`, `household`, `investment`, `total_expenses`, `date_stored`)
    VALUES ('$month', '$household', '$amountInvested', '$total_expenses', '$date')";
    

   if ($conn->query($query) === TRUE) {
  session_start();
  $_SESSION['message'] = "Record added successfully";
  echo "<script>window.location.href = 'view-monthly-investments.php';</script>";

exit;
} else {
  echo "Error: Failed to Add the Record.......";
}

}
?>

