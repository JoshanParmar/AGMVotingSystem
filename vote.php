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
            <?php
            include "display_results.php";
            require_once "config.php";
            /** @var $mysqli mysqli */

            if(isset($_GET['election_id'])){
                $election_id = $_GET['election_id'];

                $sql_get_election_data = "SELECT position_name, positions_available, status FROM elections WHERE id = ?";
                if ($stmt_get_election_data = $mysqli->prepare($sql_get_election_data)) {
                    $stmt_get_election_data->bind_param("i", $election_id);

                    if ($stmt_get_election_data->execute()) {
                        $stmt_get_election_data->store_result();

                        if ($stmt_get_election_data->num_rows == 1) {
                            $stmt_get_election_data->bind_result($position_name, $positions_available, $status);
                            if ($stmt_get_election_data->fetch()) {
                                switch ($status){
                                    case 0:
                                        echo "Election unbegun";
                                        break;
                                    case 1:
                                        echo "<h1 class='font-weight-light'>Vote in election for <b>" . $position_name . "</b></h1>";
                                        echo "<h2 class='font-weight-light'>There are <b>" . $positions_available . "</b> positions available for " . $position_name . "</h2>";
                                        echo "<p class='text-secondary'> Technical Information: Election ID =
                                                    <span id='elec_id'>" . $election_id . "</span>";

                                        $sql_get_if_voted = "SELECT id FROM users_voted WHERE user_id = ? AND election_id = ?";
                                        if ($stmt_get_if_voted = $mysqli->prepare($sql_get_if_voted)) {
                                            $stmt_get_if_voted->bind_param("ii", $_SESSION["id"], $election_id);


                                            // Attempt to execute the prepared statement
                                            if ($stmt_get_if_voted->execute()) {
                                                // store result
                                                $stmt_get_if_voted->store_result();

                                                if ($stmt_get_if_voted->num_rows == 1) {
                                                    echo "<h2 class='font-weight-light'>You have already voted in this election.</h2>";
                                                    exit;
                                                } else {

                                                    $sql_get_candidates = "SELECT id, username FROM candidates WHERE election_id = ?";
                                                    if ($stmt_get_candidates = $mysqli->prepare($sql_get_candidates)) {
                                                        $stmt_get_candidates->bind_param("i", $election_id);

                                                        if ($stmt_get_candidates->execute()) {
                                                            $stmt_get_candidates->store_result();

                                                            if ($stmt_get_candidates->num_rows > 0) {
                                                                echo "<ul class='list-group' id='sortable'>";

                                                                $stmt_get_candidates->bind_result($r_candidate_id,
                                                                    $r_candidate_username);

                                                                while ($stmt_get_candidates->fetch()) {
                                                                    $html_id = "vote-" . $r_candidate_id;
                                                                    echo "<li class='list-group-item ui-state-default' id='" .
                                                                        $html_id . "'>" . $r_candidate_username . "</li>";
                                                                }

                                                                echo "</ul>";
                                                                echo "<p>
                                                        <a href='#' class='btn btn-primary my-2' id='vote'>Vote</a>
                                                        <a href='#' class='btn btn-secondary my-2'>Abstain</a>
                                                    </p>";
                                                            } else {
                                                                echo "There are no candidates for this election.";
                                                            }
                                                        } else {
                                                            echo "Oops! Something went wrong. Please try again later.";
                                                        }
                                                        $stmt_get_candidates->close();
                                                    }
                                                }
                                            } else {
                                                echo "Oops! Something went wrong. Please try again later.";
                                            }
                                            // Close statement
                                            $stmt_get_if_voted->close();
                                        }
                                        break;
                                    case 2:
                                        display_results($election_id);
                                        break;
                                }

                            }
                        } else {
                            echo "No election found with that ID.";
                        }
                    } else {
                        echo "Oops! Something went wrong. Please try again later.";
                    }
                    $stmt_get_election_data->close();
                }

            } else {
                echo "No election id provided, please return to the votes list";
            }

            $mysqli->close();

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
<script src=sortable_list_script.js></script>

</body>
</html>
