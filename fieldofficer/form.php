
<?php
session_start();
if(!isset($_SESSION["username"])){
    header('location: ../index.php');
}
?>

<?php
include 'connection/db_connection.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $borrower = $conn->real_escape_string($_POST['borrower']);
  $loanNumber = $conn->real_escape_string($_POST['loan']);
  $collateralName = $conn->real_escape_string($_POST['collateralName']);
  $serialNumber = $conn->real_escape_string($_POST['serialNumber']);
  // $photo = $conn ->mysqli_real_escape_string($_POST['photo']);

  $photoName = $_FILES['photo']['name'];
  $imageFileType = strtolower(pathinfo($photoName, PATHINFO_EXTENSION));
  $photoTempPath = $_FILES['photo']['tmp_name'];
  $photoUploadPath = '../uploads/' . $loanNumber . "_" . $borrower.".".$imageFileType; 

    // Move the uploaded photo to the desired location
    move_uploaded_file($photoTempPath, $photoUploadPath);

  $sql = "INSERT INTO collaterals (borrower, loan, collateral_name, serial_number, collateral_photo)
  VALUES ('$borrower', '$loanNumber', '$collateralName', '$serialNumber', '$photoUploadPath')";

  if ($conn->query($sql) === TRUE) {
    echo "New record created successfully";
    header("Location: view-collaterals.php");
  } else {
    echo "Error: " . $sql . "<br>" . $conn->error;
  }
}
?>

