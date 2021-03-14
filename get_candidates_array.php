<?php

// Get array of candidates in the election, used only in displaying results of the election
function get_print_candidates_array($election_id, mysqli $mysqli){
    $candidates_array = array();
    $sql_get_candidates = "SELECT id, username, manifesto_url FROM candidates WHERE election_id = ?";
    if ($stmt_get_candidates = $mysqli->prepare($sql_get_candidates)) {
        $stmt_get_candidates->bind_param("i", $election_id);

        if ($stmt_get_candidates->execute()) {
            $stmt_get_candidates->store_result();


            if ($stmt_get_candidates->num_rows > 0) {
                $stmt_get_candidates->bind_result($r_candidate_id, $r_candidate_username, $r_manifesto_url);

                while ($stmt_get_candidates->fetch()) {
                    $candidates_array[$r_candidate_id] = $r_candidate_username;
                    echo "<p>";
                    echo $r_candidate_username . " ";
                    if ($r_manifesto_url != null) {
                        echo "<a href='#' class='text-primary' data-toggle='modal' data-target='#manifestoModal' 
                                data-url='". $r_manifesto_url ."' data-candidate_name='". $r_candidate_username
                            . "'>Manifesto</a>";
                    }
                    echo "</p>";
                }

            } else {
                echo "<p>There are no candidates for this election.</p>";
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
        $stmt_get_candidates->close();
    }
    return $candidates_array;
}