<?php
function print_round($vote_totals, $round_number){
    echo "<h5>Round " . $round_number . "</h5>";
    foreach ($vote_totals as $candidate => $votes){
        echo "<p>" . $candidate . " : " . $votes . "</p>";
    }
}

function count_votes(array $votes, $positions){
    $round = 1;
    $total_votes = count($votes);
    $quota = ceil($total_votes/2);
    echo "<p>Total Votes case in election: " . $total_votes . ". Quota for election: $quota";
    if ($positions==1){
        $vote_totals = get_vote_totals($votes);
        print_round($vote_totals, $round);
        $round++;
        while(max($vote_totals)<$quota){
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
            $vote_totals = get_vote_totals($votes);
            print_round($vote_totals, $round);
            $round++;
        }
        echo "<h3 class='mb-5'> <b>" . implode("",  get_winner($vote_totals)) . "</b> is the (preliminary) winner of this Election.";
    } else {
        echo "<p>This system is not yet set up to count votes for elections of more than 1 position - please copy the data below into another software to calculate the results of this election</p>";

    }
}

function get_winner($vote_totals){
    return array_keys($vote_totals, max($vote_totals));
}

function get_candidates_with_fewest_votes($vote_totals){
    $to_return = array();
    $minimum_votes = min($vote_totals);
    foreach ($vote_totals as $candidate => $votes){
        if ($votes==$minimum_votes){
            array_push($to_return, $candidate);
        }
    }
    return $to_return;
}

function get_vote_totals(array $votes)
{
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