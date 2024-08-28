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
      <h1>Register HouseHold</h1>
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
        <form action="register-household.php" method="post" class="row g-3">

          
        <div class="col-mt-1">
         <label for="inputCity" class="form-label">Household Name</label>
         <input type="text" class="form-control" id="inputCity" name="name" required>

         </div>

          <div class="col-mt-1">
            <label for="inputAddress2" class="form-label">Leader</label>
            <input type="text" class="form-control" id="inputAmount" name="leadername" required>
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

  $household_name = mysqli_real_escape_string($conn,$_POST['name']);
  $leadername = mysqli_real_escape_string($conn,$_POST['leadername']);


  $query = "INSERT INTO `household`(`name`, `leader`) VALUES ('$household_name','$leadername')";

  $query_run = mysqli_query($conn, $query);

  if($query_run){
    echo "<script>window.location.href = 'view-households.php';</script>";
    exit();
  }else{
    echo 'script type="text/javascript"> alert("Household failed to Register"); </script>';
  }
}
?>

