<?php
include "count_votes.php";

// Display the raw list of votes cast
function display_vote_array($vote_name_array){
    $i = 1;
    $vote_string = "";
    foreach ($vote_name_array as $vote_name) {
        $vote_string = $vote_string . "P" . $i . " to " . $vote_name . "; ";
        $i++;
    }
    return $vote_string;
}

// Convert the stored string of votes into an array that can be interpreted by the vote counting algorithm
function convert_vote_string($vote_string, $candidates){
    $vote_id_array = explode(";", $vote_string);
    $vote_name_array = array();

    foreach ($vote_id_array as $vote_id){
        if($vote_id != ""){
            array_push($vote_name_array, $candidates[$vote_id]);
        }
    }
    return $vote_name_array;
}


function display_results($election_id, mysqli $mysqli){
    echo "<h3>The candidates in this election were as follows</h3>";

    $candidates_array = get_print_candidates_array($election_id, $mysqli);

    // Get the data from the SQL table and pass into the various functions
    $sql_get_votes = "SELECT voter_token, vote_string FROM votes WHERE election_id = ?";
    if ($stmt_get_votes = $mysqli->prepare($sql_get_votes)) {
        $stmt_get_votes->bind_param("i", $election_id);

        if ($stmt_get_votes->execute()) {
            $stmt_get_votes->store_result();

            echo "<h3>The votes cast in this election were as follows</h3>";
            if ($stmt_get_votes->num_rows > 0) {
                // Display Votes Cast
                $stmt_get_votes->bind_result($r_vote_id, $r_vote_string);

                $votes = array();
                while ($stmt_get_votes->fetch()) {
                    $vote_array = convert_vote_string($r_vote_string, $candidates_array);
                    array_push($votes, $vote_array);
                    echo "<p>". $r_vote_id . "; " . display_vote_array($vote_array) . "</p>";
                }


                // Display Results of the Election
                echo "<h3>The results of this election were as follows</h3>";
                count_votes($votes, 1);

            } else {
                echo "There were no votes cast this election.";
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
        $stmt_get_votes->close();
    }
}