<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

if (!$_SESSION["administrator"]) {
    header("location: welcome.php");
    exit;
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
        <div class="page-header">
            <?php
            include "get_candidates_array.php";
            include "display_results.php";
            include "display_voting_system.php";
            include "display_candidates.php";

            require_once "config.php";
            /** @var $mysqli mysqli */

            if(isset($_GET['election_id'])){
                $election_id = $_GET['election_id'];

                // Get the status of the election and display the appropriate action to the user.
                $sql_get_election_data = "SELECT position_name, positions_available, status FROM elections WHERE id = ?";
                if ($stmt_get_election_data = $mysqli->prepare($sql_get_election_data)) {
                    $stmt_get_election_data->bind_param("i", $election_id);

                    if ($stmt_get_election_data->execute()) {
                        $stmt_get_election_data->store_result();

                        if ($stmt_get_election_data->num_rows == 1) {
                            $stmt_get_election_data->bind_result($position_name, $positions_available,
                                $status);
                            if ($stmt_get_election_data->fetch()) {
                                switch ($status){
                                    case 0:
                                        echo "<h1 class='font-weight-light'>This election has not yet begun</h1>";
                                        display_candidates($election_id, $mysqli);
                                        break;
                                    case 1:
                                        echo "<h1 class='font-weight-light'>Vote in election for <b>" . $position_name
                                            . "</b></h1>";
                                        echo "<h2 class='font-weight-light'>There are <b>" . $positions_available
                                            . "</b> positions available for " . $position_name . "</h2>";
                                        echo "<p class='text-secondary'> Technical Information: Election ID = 
                                            <span id='election_id'>" . $election_id . "</span></p>";
                                        echo "<p> Drag and drop the candidates into the order you want to vote for 
                                            them in</p>";

                                        display_proxy_voting_system($election_id, $mysqli);
                                        break;
                                    case 2:
                                        display_results($election_id, $mysqli);
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
<script src=sortable_list_script_proxy.js></script>

</body>
</html>
