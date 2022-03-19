<?php
// Convert the vote_totals array into a readable format and echo it out to the html
function print_round($vote_totals, $round_number, $candidates_remaining){
    echo "<h5>Round " . $round_number . "</h5>";
    foreach($candidates_remaining as $candidate){
        echo "<p>" . $candidate . " : " . $vote_totals[$candidate] . "</p>";
    }
}

// Get the candidate with the maximum vote total from the vote_total array
function get_winner($vote_totals){
    return array_keys($vote_totals, max($vote_totals));
}

// Get the candidate with the fewest vote total from the vote_total array
function get_candidates_with_fewest_votes($vote_totals){
    return array_keys($vote_totals, min($vote_totals));
}

// Convert the list of votes into an array detailing the number of votes for each candidates
function get_vote_totals(array $votes, array $candidates_remaining) {
    // To change to get an array of candidates?
    $vote_totals = array();
    foreach ($candidates_remaining as $candidate) {
        $vote_totals[$candidate] = 0;
    }
    foreach ($votes as $vote) {
        if (isset($vote_totals[$vote[0]])) {
            $vote_totals[$vote[0]]++;
        } else {
            $vote_totals[$vote[0]] = 1;
        }
    }
    return $vote_totals;
}


function count_votes(array $votes, $positions){
    // Setup variables
    $round = 1;
    
    $eliminated = array();
    $total_votes = count($votes);
    $quota = ceil($total_votes/2);
    echo "<p>Total Votes cast: " . $total_votes . ". Quota for victory: $quota";
    $candidates = array_values($votes)[0];

    if ($positions==1){
        // Run count once
        $vote_totals = get_vote_totals($votes, $candidates);
        print_round($vote_totals, $round, $candidates);
        $round++;

        while(max($vote_totals)<$quota){
            // Eliminate Candidates
            $eliminate = get_candidates_with_fewest_votes($vote_totals);
            $eliminated = array_merge($eliminate, $eliminated);
            $new_votes = array();
            foreach ($votes as $vote){
                foreach ($vote as $preference){
                    if (in_array($preference, $eliminate)){
                        $vote = array_diff($vote, array($preference));
                    }
                }
                array_push($new_votes, array_values($vote));
            }
            $votes = $new_votes;

            // Count votes
            $candidates_remaining = array_diff($candidates, $eliminated);
            $vote_totals = get_vote_totals($votes, $candidates_remaining);
            print_round($vote_totals, $round, $candidates_remaining);
            $round++;
        }

        // Echo winner of election to HTML
        echo "<h3 class='mb-5'> <b>" . implode("",  get_winner($vote_totals)) . "</b> wins this vote.";
    } else {
        // Echo error in counting votes
        echo "<p>This system is not yet set up to count votes for elections of more than 1 position - please copy the 
                data below into another software to calculate the results of this election</p>";

    }
}

