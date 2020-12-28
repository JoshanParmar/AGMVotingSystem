<?php
function display_candidates($election_id, mysqli $mysqli){
    echo "<h3>The candidates in this election are currently as follows</h3>";
    get_print_candidates_array($election_id, $mysqli);
}