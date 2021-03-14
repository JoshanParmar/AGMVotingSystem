<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Check if the user is an administrator, otherwise redidrect them to the homepage
if (!$_SESSION["administrator"]) {
    header("location: welcome.php");
    exit;
}

// Create the new election in the SQL table
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $position_name = trim($_POST["position_name"]);
    $positions_available = trim($_POST["positions_available"]);

    if ($position_name != "") {
        require_once "config.php";
        /** @var $mysqli mysqli */

        $sql_write_election = "INSERT INTO elections (position_name, positions_available) VALUES (?, ?)";

        if ($stmt_write_election = $mysqli->prepare($sql_write_election)) {
            $stmt_write_election->bind_param("si", $param_position_name, $param_positions_available);

            $param_position_name = $position_name;
            $param_positions_available = $positions_available;

            // Refresh the page on complete
            if ($stmt_write_election->execute()) {
                header("location: create_election.php");
                exit;
            } else {
                echo $mysqli->error;
            }

            $stmt_write_election->close();
        }
    }
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

    <title>CULA Elections</title>
</head>
<body>
<header>
    <?php include "navigation.php"; ?>
</header>

<main>
    <section class="jumbotron text-center">
        <div class="container">
            <h1 class="jumbotron-heading">CULA AGM Election Management</h1>
            <p class="lead text-muted">This section allows you to create and manage elections for your AGM.</p>
        </div>
    </section>
    <div class="container-sm">
        <?php

        // If user has just clicked delete election, delete the election from the SQL table
        if (isset($_GET['delete_election'])) {
            require_once "config.php";
            /** @var $mysqli mysqli */
            $delete_election_id = $_GET['delete_election'];


            $sql_delete_row = "DELETE FROM elections WHERE id = ?";
            if ($stmt_delete_row = $mysqli->prepare($sql_delete_row)) {
                $stmt_delete_row->bind_param("i", $delete_election_id);

                if ($stmt_delete_row->execute()) {
                    echo "<h4 class='font-weight-light text-success'> You have successfully deleted election id " . $delete_election_id . " from your AGM</h4>";
                } else {
                    echo $mysqli->error;
                }

            }
            $stmt_delete_row->close();

        }

        // If the user has just clicked change election status, make the change to the election status in the SQL table
        if (isset($_GET['change_status']) && isset($_GET['change_election'])) {
            require_once "config.php";
            /** @var $mysqli mysqli */

            $change_election_id = $_GET['change_election'];
            $change_election_status = $_GET['change_status'];

            $sql_change_status = "UPDATE elections SET status = ? WHERE id = ?";

            if ($stmt_change_status = $mysqli->prepare($sql_change_status)) {
                $stmt_change_status->bind_param("ii", $change_election_status, $change_election_id);

                if ($stmt_change_status->execute()) {
                    echo "<h4 class='font-weight-light text-success'> You have successfully updated the status of election id " . $change_election_id . " to " . $change_election_status . "</h4>";
                } else {
                    echo $mysqli->error;
                }

            }
            $stmt_change_status->close();

        }


        ?>

        <h1 class="font-weight-light">For your AGM, you currently have the following elections planned:</h1>

        <?php

        require_once "config.php";
        /** @var $mysqli mysqli */

        // Get list of elections from the SQL table and create a table on the HTML page displaying the list of elections
        $sql_get_elections = "SELECT id, position_name, positions_available, status FROM elections";
        if ($stmt_get_elections = $mysqli->prepare($sql_get_elections)) {
            if ($stmt_get_elections->execute()) {
                $stmt_get_elections->store_result();

                if ($stmt_get_elections->num_rows > 0) {
                    echo "<table class='table table-hover mt-4'>";
                    echo "<thead class='thead-dark'>";
                    echo "<tr>";
                    echo "<th scope='col'>#</th>";
                    echo "<th scope='col'>Position Name</th>";
                    echo "<th scope='col'>Positions Available</th>";
                    echo "<th scope='col'>Candidates</th>";
                    echo "<th scope='col'>Status</th>";
                    echo "<th scope='col'>Add Proxy Votes</th>";
                    echo "<th scope='col'>Delete Election</th>";
                    echo "</tr>";
                    echo "</thead>";
                    echo "<tbody>";

                    $stmt_get_elections->bind_result($r_election_id, $r_position_name, $r_positions, $r_status);

                    while ($stmt_get_elections->fetch()) {
                        echo "<tr>";
                        echo "<th scope='row'>" . $r_election_id . "</th>";
                        echo "<td>" . $r_position_name . "</td>";
                        echo "<td>" . $r_positions . "</td>";
                        echo "<td> <a href='edit_candidates.php?election_id=" . $r_election_id . "'>View/Edit Candidates</a> </td>";

                        $status_text = "";

                        switch ($r_status) {
                            case 2:
                                $status_text = "<td class='text-success'>Election Completed - <a href=
                                                        'vote.php?election_id=" . $r_election_id . "' 
                                                        class='text-success'>See results</a></td>
                                                        <td class='text-danger'>Election over - no votes can be added.</td>";
                                break;
                            case 1:
                                $status_text = "<td class='text-danger'>Election Underway - <a href=
                                                        '?change_election=" . $r_election_id . "&change_status=" . 2 . "' 
                                                        class='text-danger'>Stop Election</a></td>
                                                        <td class='text-success'> 
                                                        <a href='add_proxy_votes.php?election_id=" . $r_election_id
                                                        . "'>Add Provy Votes</a></td>";
                                break;

                            case 0:
                                $status_text = "<td class='text-warning'>Election Not Yet Started - <a href=
                                                        '?change_election=" . $r_election_id . "&change_status=" . 1 . "' 
                                                        class='text-success'>Start Election</a></td>
                                                        <td class='text-danger'>Election unbegun - no votes can be added.</td>";
                                break;
                        }
                        echo $status_text;

                        echo "<td>";
                        echo "<a type='button' class='btn btn-danger' href = '?delete_election=" . $r_election_id . "'>Delete</a>";
                        echo "</td>";

                    }
                    echo "</tbody>";
                    echo "</table>";

                } else {
                    echo "<h4 class='font-weight-light text-danger'> There are no elections scheduled your AGM</h4>";

                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            $stmt_get_elections->close();
        }

        $mysqli->close();

        ?>

        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#addElectionModal">
            Add additional election
        </button>


    </div>
</main>

<div class="modal fade" id="addElectionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addElectionModalLabel">Add Election</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-group">
                        <label for="position_name">Position Name</label>
                        <input type="text" name="position_name" class="form-control" id="position_name"
                               placeholder="Position Name (e.g. Publications Officer)">
                    </div>

                    <div class="form-group">
                        <label for="positions_available">Positions Available</label>
                        <input type="number" name="positions_available" class="form-control" id="positions_available"
                               placeholder="Positions Available (e.g. 3)">
                    </div>

                    <div class="form-group mt-2">
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <input type="reset" class="btn btn-secondary" value="Reset">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

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

