<?php
require_once "config.php";
/** @var $mysqli mysqli */

function display_results($election_id){
    global $mysqli;
    $sql_get_candidates = "SELECT id, username FROM candidates WHERE election_id = ?";
    if ($stmt_get_candidates = $mysqli->prepare($sql_get_candidates)) {
        $stmt_get_candidates->bind_param("i", $election_id);

        if ($stmt_get_candidates->execute()) {
            $stmt_get_candidates->store_result();

            echo "<h3>The candidates in this election are referred to by the following IDs</h3>";
            if ($stmt_get_candidates->num_rows > 0) {
                $stmt_get_candidates->bind_result($r_candidate_id, $r_candidate_username);

                while ($stmt_get_candidates->fetch()) {
                    echo "<p>". $r_candidate_id . " : " . $r_candidate_username . "</p>";
                }
            } else {
                echo "There are no candidates for this election.";
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
        $stmt_get_candidates->close();
    }

    $sql_get_votes = "SELECT id, vote_string FROM votes WHERE election_id = ?";
    if ($stmt_get_votes = $mysqli->prepare($sql_get_votes)) {
        $stmt_get_votes->bind_param("i", $election_id);

        if ($stmt_get_votes->execute()) {
            $stmt_get_votes->store_result();

            echo "<h3>The votes cast in this election were as follows</h3>";
            if ($stmt_get_votes->num_rows > 0) {
                $stmt_get_votes->bind_result($r_vote_id, $r_vote_string);

                while ($stmt_get_votes->fetch()) {
                    echo "<p>". $r_vote_id . " : " . $r_vote_string . "</p>";
                }
            } else {
                echo "There were no votes cast this election.";
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
        $stmt_get_votes->close();
    }
}