<?php
function display_voting_system($election_id, mysqli $mysqli){
    // Find out if the user has voted
    $sql_get_if_voted = "SELECT id FROM users_voted WHERE user_id = ? AND election_id = ?";
    if ($stmt_get_if_voted = $mysqli->prepare($sql_get_if_voted)) {
        $stmt_get_if_voted->bind_param("ii", $_SESSION["id"], $election_id);

        if ($stmt_get_if_voted->execute()) {
            $stmt_get_if_voted->store_result();

            if ($stmt_get_if_voted->num_rows == 1) {
                echo "<h2>You have already voted in this election.</h2>";
                exit;
            } else {

                // If the user hasn't voted, get the candidates in the election
                $sql_get_candidates = "SELECT id, username FROM candidates WHERE election_id = ?";
                if ($stmt_get_candidates = $mysqli->prepare($sql_get_candidates)) {
                    $stmt_get_candidates->bind_param("i", $election_id);

                    if ($stmt_get_candidates->execute()) {
                        $stmt_get_candidates->store_result();

                        if ($stmt_get_candidates->num_rows > 0) {
                            // If there are candidates, display them using the jquery sortable system
                            echo "<ul class='list-group' id='sortable'>";

                            $stmt_get_candidates->bind_result($r_candidate_id, $r_candidate_username);

                            while ($stmt_get_candidates->fetch()) {
                                $html_id = "vote-" . $r_candidate_id;
                                echo "<li class='list-group-item ui-state-default' id='" . $html_id . "'>" . $r_candidate_username . "</li>";
                            }

                            echo "</ul>";
                            echo "<p>";
                            echo "<a href='#' class='btn btn-primary my-2' id='vote'>Vote</a>";
                            echo "</p>";
                        } else {
                            echo "<p>There are no candidates for this election.</p>";
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
        $stmt_get_if_voted->close();
    }
}
