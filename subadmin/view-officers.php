<?php
session_start();
if (!isset($_SESSION["username"])) {
    header('location: ../index.php');
}
?>

<?php
// Connect to the database
include 'connection/db_connection.php';

// Retrieve the list of borrowers from the database
$sql = 'SELECT * FROM user';
$result = $conn->query($sql);

?>

<style>
    i {
        /* padding: 0px 0px 0px 0px; */
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
    // Get the reference to the div element
    var divElement = document.getElementById("message-container");

    // Function to remove the div from the DOM
    function removeDiv() {
        divElement.parentNode.removeChild(divElement);
    }

    // Add the 'removeDiv' function as an event listener for 'transitionend' event
    divElement.addEventListener("transitionend", removeDiv);

    // Trigger the fade-out effect after a short delay
    setTimeout(function() {
        divElement.classList.add("fade-out");
    }, 2000); // Delay of 2 second (2000 milliseconds)
</script>

<?php include 'header_aside.php'; ?>
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
                    <h2>View Officers</h2>
                    <hr>
                </div>

                <!-- <a href="add-borrower-group.php" class="btn btn-primary">Add borrower group</a> -->

                <table class="table datatable table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">FirstName</th>
                            <th scope="col">LastName</th>
                            <th scope="col">Title</th>
                            <th scope="col">Username</th>
                            <th scope="col">Password</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php while ($user = $result->fetch_assoc()) { ?>

                            <tr>
                                <!-- <td scope="row"><a href="#" title="View"><i class="bi bi-eye-fill"></i></a></td> -->
                                <td scope="row"><?php echo $user['firstname']; ?></td>
                                <td> <?php echo $user['lastname']; ?></td>
                                <td><?php echo $user['title']; ?></td>
                                <td><?php echo $user['username']; ?></td>
                                <td><?php echo $user['password']; ?></td>

                                <td>
                                    <a title="Edit" href="edit-borrower-group.php?id=<?php echo $borrower_group['id']; ?>" class="text-primary"><i class="bi bi-pencil-square"></i></a>
                                    <a title="Delete" href="delete-borrower-group.php?id=<?php echo $borrower_group['id']; ?>" class="text-danger"><i class="bi bi-trash"></i></a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

            </div>
        </div>

    </section>

</main><!-- End #main -->

<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

<!-- ======= Footer ======= -->
<?php include 'footer.php'; ?>