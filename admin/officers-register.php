<?php
 session_start();
  if(!isset($_SESSION["username"])){
      header('location: ../index.php');
  }

include 'connection/db_connection.php';

?>

<?php include 'header_aside.php'; ?>  

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Register Officer or Sub admin</h1>
      <nav>

    </div><!-- End Page Title -->
    <section class="section">
      <div class="row">
        <div class="col-lg-6">

            </div>
          </div>

          <div class="card">
            <div class="card-body">

              <!-- Multi Columns Form -->
        <form action="officers-register.php" method="post" class="row g-3">

        <div class="col-mt-2">
            <label for="inputDate" class="form-label">Title</label>
            <select id="inputState" class="form-select" name="title" required>
              <!--<option value="">Choose...</option>-->
              <option value="sub_admin">Sub Admin</option>
              <option value="field_officer">Field Officer</option>
              </select>
          </div>
          
        <div class="col-md-6">
         <label for="inputCity" class="form-label">FirstName</label>
         <input type="text" class="form-control" id="inputCity" name="firstname" required>

         </div>

          <div class="col-md-6">
            <label for="inputAddress2" class="form-label">LastName</label>
            <input type="text" class="form-control" id="inputAmount" name="lastname" required>
          </div>

          <div class="col-mt-1">
            <label for="inputAddress2" class="form-label">Email Address</label>
            <input type="email" class="form-control" id="email" name="emailaddress" required>
          </div>

          <div class="col-mt-1">
            <label for="inputAddress2" class="form-label">NIN Number</label>
            <input type="text" class="form-control" id="nin" name="ninNumber" required>
          </div>

          <div class="col-md-6">
            <label for="inputDate" class="form-label">Username</label>
            <input type="text" class="form-control" name="username" id="username" required>
          </div>

          <div class="col-md-6">
            <label for="inputDate" class="form-label">Password</label>
            <input type="password" class="form-control" name="password" id="password" required>
          </div>
               
                <div class="text-center">
                  <button type="submit" class="btn btn-primary">Submit</button>
           
                </div>
              </form><!-- End Multi Columns Form -->

            </div>
          </div>

        </div>

            </div>
          </div>

        </div>
      </div>
    </section>

  </main><!-- End #main -->

  <?php include 'footer.php'; ?>  

<?php

if ($_SERVER['REQUEST_METHOD'] ==='POST') {

  $title = mysqli_real_escape_string($conn, $_POST['title']);
  $firstname = mysqli_real_escape_string($conn,$_POST['firstname']);
  $lastname = mysqli_real_escape_string($conn,$_POST['lastname']);
  $emailaddress = mysqli_real_escape_string($conn,$_POST['emailaddress']);
  $ninNumber = mysqli_real_escape_string($conn,$_POST['ninNumber']);
  $username = mysqli_real_escape_string($conn,$_POST['username']);
  $password = mysqli_real_escape_string($conn,$_POST['password']);

  $query = "INSERT INTO `user`(`title`, `firstname`, `lastname`, `emailaddress`, `ninNumber`, `username`, `password`) 
  VALUES ('$title','$firstname','$lastname','$emailaddress','$ninNumber','$username','$password')";

  $query_run = mysqli_query($conn, $query);

  if($query_run){
    echo "<script>window.location.href = 'view-officers.php';</script>";
    exit();
  }else{
    echo 'script type="text/javascript"> alert("Data not saved"); </script>';
  }
}
?>

