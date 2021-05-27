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
    $positions_available = 1;

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
<?php include "header.php"; ?>
<body>
<header>
    <?php include "navigation.php"; ?>
</header>

<main>
    <section class="jumbotron jumbotron-image text-center" style="background-image: url(imgs/vince3.jpg);">
        <div class="container jumbotron-container">
            <h1 class="jumbotron-heading text-white jumbotron-text">CULA Online</h1>
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
                    echo "<h4 class='font-weight-light text-success'> You have successfully deleted vote id " . $delete_election_id . " from your event</h4>";
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
                    echo "<h4 class='font-weight-light text-success'> You have successfully updated the status of vote id " . $change_election_id . " to " . $change_election_status . "</h4>";
                } else {
                    echo $mysqli->error;
                }

            }
            $stmt_change_status->close();

        }


        ?>

        <h1 class="font-weight-light">For your Event, you currently have the following polls planned:</h1>

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
                    echo "<th scope='col'>Vote Name</th>";
                    echo "<th scope='col'>Options</th>";
                    echo "<th scope='col'>Status</th>";
                    echo "<th scope='col'>Add Proxy Votes</th>";
                    echo "<th scope='col'>Delete Poll</th>";
                    echo "</tr>";
                    echo "</thead>";
                    echo "<tbody>";

                    $stmt_get_elections->bind_result($r_election_id, $r_position_name, $r_positions, $r_status);

                    while ($stmt_get_elections->fetch()) {
                        echo "<tr>";
                        echo "<th scope='row'>" . $r_election_id . "</th>";
                        echo "<td>" . $r_position_name . "</td>";
                        echo "<td> <a href='edit_candidates.php?election_id=" . $r_election_id . "'>View/Edit Options</a> </td>";

                        $status_text = "";

                        switch ($r_status) {
                            case 2:
                                $status_text = "<td class='text-success'>Poll Completed - <a href=
                                                        'vote.php?election_id=" . $r_election_id . "' 
                                                        class='text-success'>See results</a></td>
                                                        <td class='text-danger'>Poll over - no votes can be added.</td>";
                                break;
                            case 1:
                                $status_text = "<td class='text-danger'>Poll Underway - <a href=
                                                        '?change_election=" . $r_election_id . "&change_status=" . 2 . "' 
                                                        class='text-danger'>Stop Poll</a></td>
                                                        <td class='text-success'> 
                                                        <a href='add_proxy_votes.php?election_id=" . $r_election_id
                                                        . "'>Add Proxy Votes</a></td>";
                                break;

                            case 0:
                                $status_text = "<td class='text-warning'>Poll Not Yet Started - <a href=
                                                        '?change_election=" . $r_election_id . "&change_status=" . 1 . "' 
                                                        class='text-success'>Start Poll</a></td>
                                                        <td class='text-danger'>Poll unbegun - no votes can be added.</td>";
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
                    echo "<h4 class='font-weight-light text-danger'> There are no polls scheduled your Event</h4>";

                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            $stmt_get_elections->close();
        }

        $mysqli->close();

        ?>

        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#addElectionModal">
            Add additional poll
        </button>


    </div>
</main>

<div class="modal fade" id="addElectionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addElectionModalLabel">Add Poll</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-group">
                        <label for="position_name">Poll title</label>
                        <input type="text" name="position_name" class="form-control" id="position_name"
                               placeholder="Poll title (e.g. THB CULA>CUCA)">
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

