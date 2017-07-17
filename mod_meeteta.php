<?php

function meet_eta() {
    $date = strtotime("third friday of this month 19:00");
    if(time() > $date) {
        $date = strtotime("third friday of next month 19:00");
    }
    $diff = $date - time();
    return "[Meet ETA] " . floor($diff/60/60/24) . " dagen";
}
