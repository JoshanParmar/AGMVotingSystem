<?php

function write_vote(mysqli $mysqli, $vote_string, $election_id){
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

function register_vote(mysqli $mysqli){
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
                echo "<h2 class='font-weight-light'>You have already voted in this poll.</h2>";
            } else {
                // If they have not already voted, write their vote into the votes table with a voter id.
                write_vote($mysqli, $vote_string, $election_id);
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }

        $stmt_get_if_voted->close();
    }

    $mysqli->close();
}


function register_proxy_vote(mysqli $mysqli){

    $i = 0;
    $vote_string = "";
    // Convert order of votes into storable string
    foreach ($_GET['vote'] as $value) {
        $vote_string = $vote_string . $value . ";";
        $i++;
    }
    $election_id = $_GET["election_id"][0];

    write_vote($mysqli, $vote_string, $election_id);

    $mysqli->close();

    return $election_id;
}