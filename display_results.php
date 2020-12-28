<?php
require_once "config.php";
/** @var $mysqli mysqli */

function convert_vote_string($vote_string, $candidates){
    $vote_id_array = explode(";", $vote_string);
    $vote_name_array = array();

    foreach ($vote_id_array as $vote_id){
        if($vote_id != ""){
            array_push($vote_name_array, $candidates[$vote_id]);
        }
    }
    $i = 1;
    $vote_string = "";
    foreach ($vote_name_array as $vote_name) {
        $vote_string = $vote_string . "P" . $i . " to " . $vote_name . "; ";
        $i++;
    }
    return $vote_string;
}

function display_results($election_id){
    global $mysqli;
    $candidates_array = array();

    $sql_get_candidates = "SELECT id, username FROM candidates WHERE election_id = ?";
    if ($stmt_get_candidates = $mysqli->prepare($sql_get_candidates)) {
        $stmt_get_candidates->bind_param("i", $election_id);

        if ($stmt_get_candidates->execute()) {
            $stmt_get_candidates->store_result();

            if ($stmt_get_candidates->num_rows > 0) {
                $stmt_get_candidates->bind_result($r_candidate_id, $r_candidate_username);

                while ($stmt_get_candidates->fetch()) {
                    $candidates_array[$r_candidate_id] = $r_candidate_username;
                }

            } else {
                echo "There are no candidates for this election.";
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
        $stmt_get_candidates->close();
    }

    $sql_get_votes = "SELECT voter_token, vote_string FROM votes WHERE election_id = ?";
    if ($stmt_get_votes = $mysqli->prepare($sql_get_votes)) {
        $stmt_get_votes->bind_param("i", $election_id);

        if ($stmt_get_votes->execute()) {
            $stmt_get_votes->store_result();

            echo "<h3>The votes cast in this election were as follows</h3>";
            if ($stmt_get_votes->num_rows > 0) {
                $stmt_get_votes->bind_result($r_vote_id, $r_vote_string);

                while ($stmt_get_votes->fetch()) {
                    echo "<p>". $r_vote_id . " : " . convert_vote_string($r_vote_string, $candidates_array) . "</p>";
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