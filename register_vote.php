<?php
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
    <?php include "navigation.php"; ?>
</header>

<main>
    <section class="jumbotron text-center">
        <div class="container">
            <h1 class="jumbotron-heading">CULA AGM Online</h1>
            <p class="lead text-muted">Vote registration status</p>
        </div>
    </section>
    <div class="container-sm">
        <?php
        require_once "config.php";
        /** @var $mysqli mysqli */

        $i = 0;
        $vote_string = "";

        // Convert order of votes into storable string
        foreach ($_GET['vote'] as $value) {
            $vote_string = $vote_string . $value . ";";
            $i++;
        }
        // Get the election ID from the URL
        $election_id = $_GET["election_id"][0];

        // Check if the user has already voted
        $sql_get_if_voted = "SELECT id FROM users_voted WHERE user_id = ? AND election_id = ?";

        if ($stmt_get_if_voted = $mysqli->prepare($sql_get_if_voted)) {
            $stmt_get_if_voted->bind_param("ii", $_SESSION["id"], $election_id);


            if ($stmt_get_if_voted->execute()) {
                $stmt_get_if_voted->store_result();

                if ($stmt_get_if_voted->num_rows == 1) {
                    echo "<h2 class='font-weight-light'>You have already voted in this election.</h2>";
                } else {
                    // If they have not already voted, write their vote into the votes table with a voter id.
                    $voter_token = uniqid('', true);
                    $sql_store_vote = "INSERT INTO votes (vote_string, election_id, voter_token) VALUES (?, ?, ?)";

                    if ($stmt_store_vote = $mysqli->prepare($sql_store_vote)) {
                        $stmt_store_vote->bind_param("sis", $vote_string, $election_id, $voter_token);
                        if ($stmt_store_vote->execute()) {

                            $sql_store_user_voted = "INSERT INTO users_voted (user_id, election_id) VALUES (?, ?)";

                            if ($stmt_store_user_voted = $mysqli->prepare($sql_store_user_voted)) {
                                $stmt_store_user_voted->bind_param("ii", $_SESSION["id"], $election_id);


                                if ($stmt_store_user_voted->execute()) {
                                    // Notify the user that their vote has been recorded.
                                    echo "<h2 class='font-weight-light'>Your vote has been recorded.</h2>";
                                    echo "<h3 class='font-weight-light'>Your unique voter token was ". $voter_token.
                                        ". Please make a note of this and do not share this with anyone else.</h3>";
                                    echo "<p>You can use your unique voter token to check that your vote has been counted.</p>";
                                    } else {
                                    echo "Something went wrong. Please try again later.";
                                }

                                $stmt_store_user_voted->close();
                            }
                        } else {
                            echo "Something went wrong. Please try again later.";
                        }
                        $stmt_store_vote->close();
                    }
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            $stmt_get_if_voted->close();
        }

        $mysqli->close();
        ?>
        <h2 class='font-weight-light'><a href='votes-list.php'>Click Here</a> to return to the list of available votes</h2>

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
