<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}
if (!$_SESSION["administrator"]) {
    header("location: permission.php");
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
            <h1 class="jumbotron-heading">anySociety AGM Election Creation</h1>
            <p class="lead text-muted">Create Elections for your AGM Here</p>
        </div>
    </section>
    <div class="container-sm mb-5">
        <div class="page-header">
            <h1 class="font-weight-light">For you AGM, you currently have the following elections planned:</h1>
        </div>
    </div>

    <div class="container-md">
        <table class="table table-hover">
            <thead class="thead-dark">
            <tr>
                <th scope="col">#</th>
                <th scope="col">Position Name</th>
                <th scope="col">Positions Available</th>
                <th scope="col">Candidates</th>
                <th scope="col">Status</th>
            </tr>
            </thead>
            <tbody>

            <?php
            require_once "config.php";
            /** @var $mysqli mysqli */

            $stmt = "SELECT id, position_name, positions_available, status FROM elections";
            $result = $mysqli->query($stmt);

            if ($result->num_rows > 0) {
                // output data of each row
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<th scope='row'>" . $row["id"] . "</th>";
                    echo "<td>" . $row["position_name"] . "</td>";
                    echo "<td>" . $row["positions_available"] . "</td>";
                    echo "<td> <a href='#'>View/Edit Candidates</a> </td>";

                    $status_text = "";

                    switch ($row["status"]) {
                        case 2:
                            $status_text = "<td class='text-success'>Election Completed - <a href='#' 
                                                class='text-success'>See results</a></td>";
                            break;
                        case 1:
                            $status_text = "<td class='text-danger'>Election Underway - <a href='#' 
                                                class='text-danger'>Stop Election</a></td>";
                            break;

                        case 0:
                            $status_text = "<td class='text-warning'>Election Unbegun - <a href='#' 
                                                class='text-success'>Start Election</a></td>";
                            break;
                    }
                    echo $status_text;

                    echo "</tr>";
                }
            } else {
                echo "0 results";
            }

            $mysqli->close();

            ?>
            </tbody>
        </table>
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

