<?php

function draw_drag_drop_vote_ui($election_id, mysqli $mysqli){
    $sql_get_candidates = "SELECT id, username, manifesto_url FROM candidates WHERE election_id = ?";
    if ($stmt_get_candidates = $mysqli->prepare($sql_get_candidates)) {
        $stmt_get_candidates->bind_param("i", $election_id);

        if ($stmt_get_candidates->execute()) {
            $stmt_get_candidates->store_result();

            if ($stmt_get_candidates->num_rows > 0) {
                // If there are candidates, display them using the jquery sortable system
                echo "<ul class='list-group' id='sortable'>";

                $stmt_get_candidates->bind_result($r_candidate_id, $r_candidate_username, $r_manifesto_url);

                while ($stmt_get_candidates->fetch()) {
                    $html_id = "vote-" . $r_candidate_id;
                    echo "<li class='list-group-item ui-state-default' id='" . $html_id . "'>" . $r_candidate_username . " ";
                    if ($r_manifesto_url != null) {
                        echo "<a href='#' class='text-primary' data-toggle='modal' data-target='#manifestoModal' 
                                data-url='". $r_manifesto_url ."' data-candidate_name='". $r_candidate_username
                                . "'>Manifesto</a>";
                    }
                    echo "</li>";
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
                draw_drag_drop_vote_ui($election_id, $mysqli);
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
        $stmt_get_if_voted->close();
    }
}

function display_proxy_voting_system($election_id, mysqli $mysqli){
    draw_drag_drop_vote_ui($election_id, $mysqli);
}
