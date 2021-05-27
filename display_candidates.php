<?php
function display_candidates($election_id, mysqli $mysqli){
    echo "<h3>The options in this poll are currently as follows</h3>";
    get_print_candidates_array($election_id, $mysqli);
}