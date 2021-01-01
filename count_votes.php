<?php
// Convert the vote_totals array into a readable format and echo it out to the html
function print_round($vote_totals, $round_number){
    echo "<h5>Round " . $round_number . "</h5>";
    foreach ($vote_totals as $candidate => $votes){
        echo "<p>" . $candidate . " : " . $votes . "</p>";
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
function get_vote_totals(array $votes) {
    $vote_totals = array();
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
    $total_votes = count($votes);
    $quota = ceil($total_votes/2);
    echo "<p>Total Votes case in election: " . $total_votes . ". Quota for election: $quota";


    if ($positions==1){
        // Run couunt once
        $vote_totals = get_vote_totals($votes);
        print_round($vote_totals, $round);
        $round++;

        while(max($vote_totals)<$quota){
            // Eliminate Candidates
            $eliminate = get_candidates_with_fewest_votes($vote_totals);
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
            $vote_totals = get_vote_totals($votes);
            print_round($vote_totals, $round);
            $round++;
        }

        // Echo winner of election to HTML
        echo "<h3 class='mb-5'> <b>" . implode("",  get_winner($vote_totals)) . "</b> is the (preliminary) 
                winner of this Election.";
    } else {
        // Echo error in counting votes
        echo "<p>This system is not yet set up to count votes for elections of more than 1 position - please copy the 
                data below into another software to calculate the results of this election</p>";

    }
}

