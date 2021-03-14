<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Check if the user is an administrator if not redirect them to welcome
if (!$_SESSION["administrator"]) {
    header("location: welcome.php");
    exit;
}

// Check if an election id has been provided to display the candidates for, if not redirect them to create_elections
if (!isset($_GET["election_id"])) {
    header("location: create_election.php");
    exit;
}

// If the user has just completed the add candidates form, if so add the candidate to the SQL table
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"]);
    $manifesto_url = trim($_POST["manifesto-url"]);

    if ($name != "") {
        require_once "config.php";
        /** @var $mysqli mysqli */
        $sql_write_candidate = "INSERT INTO candidates (username, election_id, manifesto_url) VALUES (?, ?, ?)";
        if ($stmt_write_candidate = $mysqli->prepare($sql_write_candidate)) {
            $stmt_write_candidate->bind_param("sis", $param_name, $_GET["election_id"], $param_url);
            $param_name = $name;
            $param_url = $manifesto_url;
            if (!($stmt_write_candidate->execute())) {
                echo $mysqli->error;
            }
            $stmt_write_candidate->close();
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
            <p class="lead text-muted">Edit candidates standing in elections here.</p>
        </div>
    </section>
    <div class="container-sm">
    </div>
    <div class="container-sm">


        <a type='button' class='btn btn-secondary mb-3' href='create_election.php'>Return to list of elections</a>


        <?php
        require_once "config.php";
        /** @var $mysqli mysqli */

        // If the user has just requested the deletion of a candidate, remove it from the list
        if (isset($_GET['delete_id'])){
            $delete_id = $_GET['delete_id'];

            $sql_delete_candidate = "DELETE FROM candidates WHERE id = ?";
            if ($stmt_delete_candidate = $mysqli->prepare($sql_delete_candidate)) {
                $stmt_delete_candidate->bind_param("i", $delete_id);

                if ($stmt_delete_candidate->execute()) {
                    echo "<h4 class='font-weight-light text-success'> You have successfully deleted candidate id " . $delete_id . " from your Election</h4>";
                }
                else{
                    echo $mysqli->error;
                }

            }
            $stmt_delete_candidate->close();
        }

        $election_id = $_GET["election_id"];

        // Get the details of the election
        $sql_get_election_details = "SELECT position_name, status FROM elections where id = ?";
        if ($stmt_get_election_details = $mysqli->prepare($sql_get_election_details)) {
            $stmt_get_election_details->bind_param("i", $election_id);

            if ($stmt_get_election_details->execute()) {
                $stmt_get_election_details->store_result();

                if ($stmt_get_election_details->num_rows == 1) {
                    $stmt_get_election_details->bind_result($position_name, $status);

                    if ($stmt_get_election_details->fetch()) {

                        // If the election exists, get the candidates of the election
                        $sql_get_candidates = "SELECT id, username, manifesto_url FROM candidates WHERE election_id = ?";

                        if ($stmt_get_candidates = $mysqli->prepare($sql_get_candidates)) {
                            $stmt_get_candidates->bind_param("i", $election_id);

                            if ($stmt_get_candidates->execute()) {
                                $stmt_get_candidates->store_result();
                                if ($stmt_get_candidates->num_rows > 0) {

                                    // Display the candidates for the election in the format depending on the status
                                    switch ($status){
                                        case 0:
                                            echo "<h3 class='font-weight-light'>In your election for " . $position_name
                                                .", you have the following candidates: </h3>";
                                            echo "<table class='table table-hover mt-4'>";
                                            echo "<thead class='thead-dark'>";
                                            echo "<tr>";
                                            echo "<th scope='col'>#</th>";
                                            echo "<th scope='col'>Candidate Name</th>";
                                            echo "<th scope='col'>Manifesto</th>";
                                            echo "<th scope='col'>Delete Candidate</th>";
                                            echo "</tr>";
                                            echo "</thead>";
                                            echo "<tbody>";

                                            $stmt_get_candidates->bind_result($r_id, $r_username, $r_manifesto_url);

                                            while ($stmt_get_candidates->fetch()) {
                                                echo "<tr>";
                                                echo "<th scope='row'>" . $r_id . "</th>";
                                                echo "<td>" . $r_username . "</td>";
                                                echo "<td>";
                                                if ($r_manifesto_url != null) {
                                                    echo "<a href='#' class='text-primary' data-toggle='modal' data-target='#manifestoModal' 
                                                            data-url='". $r_manifesto_url ."' data-candidate_name='". $r_username
                                                            . "'>Manifesto</a>";
                                                }
                                                echo "</td>";
                                                echo "<td>";
                                                echo "<a type='button' class='btn btn-danger' href = '?election_id=" .
                                                    $election_id . "&delete_id=" . $r_id . "'>Delete</a>";
                                                echo "</td></tr>";
                                            }

                                            echo "</tbody>";
                                            echo "</table>";

                                            echo "<button type='button' class='btn btn-success' data-toggle='modal' 
                                                    data-target='#addCandidateModal'>Add additional candidate</button>";

                                            break;
                                        case 1:
                                            echo "<h3 class='font-weight-light'>This election is underway. You can no 
                                                       longer edit candidates.</h3>";
                                            echo "<h5>The candidates were as follows:</h5>";
                                            echo "<ul class='list-group list-group-flush'>";
                                            $stmt_get_candidates->bind_result($r_id, $r_username,$r_manifesto_url);
                                            while ($stmt_get_candidates->fetch()) {
                                                echo "<p>" . $r_username . " ";
                                                if ($r_manifesto_url != null) {
                                                    echo "<a href='#' class='text-primary' data-toggle='modal' data-target='#manifestoModal' 
                                                            data-url='". $r_manifesto_url ."' data-candidate_name='". $r_username
                                                        . "'>Manifesto</a>";
                                                }
                                                echo "</p>";
                                            }
                                            echo "</ul>";
                                            break;

                                        case 2:
                                            echo "<h3 class='font-weight-light'>This election has finished. You can no 
                                                longer edit candidates.</h3>";
                                            echo "<h5>The candidates were as follows:</h5>";
                                            echo "<ul class='list-group list-group-flush'>";
                                            $stmt_get_candidates->bind_result($r_id, $r_username,$r_manifesto_url);
                                            while ($stmt_get_candidates->fetch()) {
                                                echo "<p>" . $r_username . " ";
                                                if ($r_manifesto_url != null) {
                                                    echo "<a href='#' class='text-primary' data-toggle='modal' data-target='#manifestoModal' 
                                                            data-url='". $r_manifesto_url ."' data-candidate_name='". $r_username
                                                        . "'>Manifesto</a>";
                                                }
                                                echo "</p>";
                                            }
                                            echo "</ul>";

                                            echo "<p class='mt-3'> <a href='vote.php?election_id=" . $election_id . "'>
                                                Click Here</a> to view the results of this election";
                                            break;

                                    }
                                } else {
                                    switch ($status){
                                        // If there are no candidates, alert the user to this, in the correct format for
                                        // the status of the election
                                        case 0:
                                            echo "<h3 class='font-weight-light'>In your election for " . $position_name
                                                .", you have the following candidates: </h3>";
                                            echo "<p>There are no candidates for this election.</p>";
                                            echo "<button type='button' class='btn btn-success' data-toggle='modal' 
                                                    data-target='#addCandidateModal'>Add additional candidate</button>";
                                            break;

                                        case 1:
                                            echo "<h3 class='font-weight-light'>This election is underway. You can no 
                                                longer edit candidates.</h3>";
                                            echo "<p>There are no candidates for this election.</p>";
                                            break;

                                        case 2:
                                            echo "<h3 class='font-weight-light'>This election has finished. You can no 
                                                longer edit candidates.</h3>";
                                            echo "<p>There were no candidates for this election.</p>";
                                            echo "<p class='mt-3'> <a href='vote.php?election_id=" . $election_id . "'>
                                                Click Here</a> to view the results of this election";
                                            break;
                                    }
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
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            $stmt_get_election_details->close();
        }


        ?>

    </div>
</main>


<?php include "manifesto_modal.php" ?>

<div class="modal fade" id="addCandidateModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCandidateModalLabel">Add Candidate</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?election_id=" . $election_id; ?>" method="post">
                    <div class="form-group">
                        <label for="name">Candidate Name</label>
                        <input type="text" name="name" class="form-control" id="name"
                               placeholder="Name (e.g. Freddie Poser)">
                    </div>
                    <div class="form-group">
                        <label for="manifesto-url">Manifesto URL</label>
                        <input type="text" name="manifesto-url" class="form-control" id="manifesto-url"
                               placeholder="freddie-poser.png">
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
<script src="show_manifesto.js"></script>

</body>
</html>

