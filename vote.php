<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}
?>

<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap and JQuery CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
          integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <title>Hello, world!</title>
</head>
<body>
<header>
    <div class="navbar navbar-dark bg-dark box-shadow">
        <div class="container d-flex justify-content-between">
            <a href="#" class="navbar-brand d-flex align-items-center">
                <strong>AGM Test Voting System</strong>
            </a>
        </div>
    </div>
</header>

<main>
    <section class="jumbotron text-center">
        <div class="container">
            <h1 class="jumbotron-heading">anySociety AGM Voting System</h1>
            <p class="lead text-muted">Custom Voting System for the anySociety AGM</p>
        </div>
    </section>
    <div class="container-sm">
        <div class="page-header">
            <h1 class="font-weight-light">Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b></h1>
            <?php
            require_once "config.php";
            /** @var $mysqli mysqli */


            $sql = "SELECT position_name, positions_available, status FROM elections WHERE id = ?";

            if($stmt = mysqli_prepare($mysqli, $sql)){
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "s", $position_id);

                // Set parameters
                $position_id = $_GET['election_id'];

                // Attempt to execute the prepared statement
                if(mysqli_stmt_execute($stmt)){
                    // Store result
                    mysqli_stmt_store_result($stmt);

                    // Check if election_id exists, if yes then verify password
                    if(mysqli_stmt_num_rows($stmt) == 1){
                        // Bind result variables
                        mysqli_stmt_bind_result($stmt, $position_name, $positions_available, $status);

                        if(mysqli_stmt_fetch($stmt)){
                            switch ($status) {
                                case 0:
                                    echo "<h3>This election has not begun yet.</h3>
                                            <h4>The candidates are expected to be as follows:</h4>";
                                    $stmt_get_candidates = "SELECT username FROM candidates WHERE election_id = $position_id";
                                    $result = $mysqli->query($stmt_get_candidates);

                                    if ($result->num_rows > 0) {
                                        // output data of each row
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<p>" . $row["username"] . "</p>";
                                        }
                                    } else {
                                        echo "<p>There are no candidates</p>";
                                    }
                            }
                        }
                    } else{
                        // Display an error message if election_id doesn't exist
                        echo "No election was found with that ID";
                    }
                } else{
                    echo "Oops! Something went wrong. Please try again later.";
                }

                // Close statement
                mysqli_stmt_close($stmt);
            }

            ?>
        </div>
    </div>
</main>


<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
        integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
        crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"
        integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6"
        crossorigin="anonymous"></script>

</body>
</html>
