<?php
session_start();
if (!isset($_SESSION["username"])) {
  header('location: ../index.php');
}
?>

<?php
// Connect to the database
include 'connection/db_connection.php';
// Get the site class name
$class_name = isset($_GET['class']) ? $_GET['class'] : '';

// $sql = "SELECT * FROM sites WHERE class='$class_name'";

$sql = "SELECT
    s.site_id AS site_id,
    s.primary_id AS primary_id,
    s.secondary_id AS secondary_id,
    s.site_name AS site_name,
    s.site_auto_status AS site_auto_status,
    s.site_status AS site_status,
    s.tx_site_type AS tx_site_type,
    s.department AS department,
    (SELECT COUNT(*) FROM dependencies sd WHERE s.primary_id = sd.site_id) AS dependent_sites_count,
    (SELECT COUNT(DISTINCT g.generator_id) FROM generators g WHERE s.primary_id = g.site_id) AS number_of_generators_count
FROM 
    sites s
WHERE 
    s.class = '$class_name'";

$result = $conn->query($sql);

?>

<style>
  i {
    font-size: 20px;
  }

  .table a {
    padding: 1px 1px 1px 1px;
  }

  input[type="search"] {
    width: 100%;
    padding: 5px 5px 5px 5px;
    border: 1px solid #ccc;
    border-radius: 4px;
    resize: vertical;
  }
</style>

<script>
  // Function to download the table as CSV
  function downloadCSV() {
    const rows = document.querySelectorAll("table tr");
    let csvContent = "";

    rows.forEach(row => {
      const cells = row.querySelectorAll("th, td");
      const rowContent = Array.from(cells).map(cell => `"${cell.textContent}"`).join(",");
      csvContent += rowContent + "\n";
    });

    const blob = new Blob([csvContent], {
      type: "text/csv"
    });
    const link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = "sites.csv";
    link.click();
  }

  // Function to download the table as Excel
  function downloadExcel() {
    const table = document.querySelector("table").outerHTML;
    const html = `
      <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
        <head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Sheet1</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head>
        <body>${table}</body>
      </html>
    `;

    const blob = new Blob([html], {
      type: "application/vnd.ms-excel"
    });
    const link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = "sites.xls";
    link.click();
  }
</script>

<?php include 'header_aside.php'; ?>

<!-- main -->
<main id="main" class="main">

  <?php if (isset($_SESSION['message'])) { ?>
    <div class="container" id="message-container">
      <div class="row">
        <div class="col-md-12">
          <div class="alert alert-success d-flex justify-content-between align-items-center">
            <?php echo $_SESSION['message']; ?>
            <!-- add button for removing the alert message -->
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        </div>
      </div>
    </div>
  <?php }
  unset($_SESSION['message']); ?>


  <section>
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <h2 class="text-center">List of Class <?php echo ($class_name != '' ? $class_name : 'Nothing'); ?> Sites</h2>
          <hr>
          <!-- Download Buttons -->
          <!-- <button onclick="downloadCSV()" class="btn btn-primary">Download as CSV</button>
          <button onclick="downloadExcel()" class="btn btn-secondary">Download as Excel</button> -->
          <button onclick="window.location.href='export-sites.php?class=<?php echo $class_name; ?>&format=csv'" class="btn btn-primary">Download as CSV</button>
          <button onclick="window.location.href='export-sites.php?class=<?php echo $class_name; ?>&format=excel'" class="btn btn-secondary">Download as Excel</button>

          <hr>
        </div>

        <table class="table datatable table-striped">
          <thead class="thead-dark">
            <tr>
              <th scope="col">Edit</th>
              <th scope="col">Site ID</th>
              <th scope="col">Site Name</th>
              <th scope="col">Dependents</th>
              <th scope="col">Generators</th>
              <th scope="col">Automation Status</th>
              <th scope="col">Site Status</th>
              <th scope="col">Priority</th>
              <th scope="col">Department</th>
            </tr>
          </thead>
          <tbody>

            <?php while ($site = $result->fetch_assoc()) { ?>
              <tr>
                <td scope="row">
                  <a href="edit-site.php?site_id=<?php echo $site['site_id']; ?>" title="Edit" class="text-success"><i class="bi bi-pencil-square"></i></a>
                </td>
                <td><?php echo $site['secondary_id']; ?></td>
                <td><?php echo $site['site_name']; ?></td>
                <td>
                  <?php echo $site['dependent_sites_count']; ?>
                  <?php if ($site['dependent_sites_count'] > 0): ?>
                    <a href="view-dependent-site.php?site_id=<?php echo $site['primary_id']; ?>"
                      title="View" class="text-"><i class="bi bi-eye"></i></a>
                  <?php endif; ?>
                </td>
                <td>
                  <?php echo $site['number_of_generators_count']; ?>
                  <?php if ($site['number_of_generators_count'] > 0): ?>
                    <a href="view-generator.php?site_id=<?php echo $site['primary_id']; ?>"
                      title="View" class="text-"><i class="bi bi-eye"></i></a>
                  <?php endif; ?>
                </td>
                <td><?php echo $site['site_auto_status']; ?></td>
                <td><?php echo $site['site_status']; ?></td>
                <td><?php echo $site['tx_site_type']; ?></td>
                <td><?php echo $site['department']; ?></td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>

  </section>

</main><!-- End #main -->

<!-- ======= Footer ======= -->
<?php include 'footer.php'; ?>