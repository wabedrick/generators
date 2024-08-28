<?php
include 'connection/db_connection.php';
session_start();
if (!isset($_SESSION["username"])) {
  header('location: ../index.php');
}
?>



<?php
// select borrower group names and count the number of borrowers and loans in each group
$sql = "SELECT * FROM borrower_groups";
$result = $conn->query($sql);
$labels = array();
$borrowerData = array();
$loanData = array();
while ($row = $result->fetch_assoc()) {
    $groupname = $row['name'];
    $group_id = $row['id'];
    $borrower_count = ($conn->query("SELECT COUNT(*) FROM borrowers WHERE group_id = $group_id")->fetch_assoc()['COUNT(*)'] ?? 0);
    $labels[] = $groupname;
    $borrowerData[] = intval($borrower_count);
}

$jsonLabel = json_encode($labels);
$jsonBorrowerData = json_encode($borrowerData);  

// select borrower group and number of loans taken by each group
$sql = "SELECT * FROM borrower_groups";
$result = $conn->query($sql);
$labels = array();






?>

<?php include 'header_aside.php'; ?>

<!--main content-->
<main id="main" class="main">

  <div class="col-md-12">
    <h2 class="text-center text-danger">Reports</h2>
    <hr>
  </div>


  <section class="section">
    <div class="row">

      <div class="col-lg-6 mt-2">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Borrower Group Members</h5>

            <!-- Pie Chart -->
            <canvas id="pieChart" style="max-height: 400px;"></canvas>
            <script>
              document.addEventListener("DOMContentLoaded", () => {
                new Chart(document.querySelector('#pieChart'), {
                  type: 'pie',
                  data: {
                    labels: <?php echo $jsonLabel ?> ,
                    datasets: [{
                      label: 'My First Dataset',
                      data: <?php echo $jsonBorrowerData ?> ,
                      backgroundColor: [
                        'rgb(255, 99, 132)',
                        'rgb(54, 162, 235)',
                        'rgb(255, 205, 86)',
                        'rgb(75, 192, 192)',
                        'rgb(153, 102, 255)',
                        'rgb(255, 159, 64)',
                        'rgb(255, 99, 132)',
                        'rgb(54, 162, 235)',
                        'rgb(255, 205, 86)',
                        'rgb(75, 192, 192)',
                        'rgb(153, 102, 255)',
                        'rgb(255, 159, 64)',
                        'rgb(255, 99, 132)',
                        'rgb(54, 162, 235)',
                        'rgb(255, 205, 86)',
                        'rgb(75, 192, 192)',
                        'rgb(153, 102, 255)',
                        'rgb(255, 159, 64)',
                        'rgb(255, 99, 132)',
                        'rgb(54, 162, 235)',
                        'rgb(255, 205, 86)',
                        'rgb(75, 192, 192)',
                        'rgb(153, 102, 255)',
                        'rgb(255, 159, 64)',


                      ],
                      hoverOffset: 4
                    }]
                  }
                });
              });
            </script>
            <!-- End Pie CHart -->

          </div>
        </div>
      </div>

            </script>
            <!-- End Radar CHart -->

          </div>
        </div>
      </div>

            </script>
            <!-- End Bubble Chart -->

          </div>
        </div>
      </div>

    </div>
  </section>

</main><!-- End #main -->

<!-- ======= Footer ======= -->
<?php include 'footer.php'; ?>