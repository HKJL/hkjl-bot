<?php

function coin($args) {
    $from = strtoupper($args);
    $results = @file_get_contents("https://min-api.cryptocompare.com/data/price?fsym=" . urlencode($from) . "&tsyms=USD,EUR");
    if(!$results) {
        $returnstring = "[COIN] Er ging iets stuk...";
    } else {
        $results = json_decode($results, true);
        if(!array_key_exists("USD", $results)) {
            $returnstring = "[COIN] Deze cryptocoin ken ik niet...";
        } else {
            $returnstring = "[COIN] " . $from . " waarde is op dit moment: $ " . $results["USD"] . " / € " . $results["EUR"];
        }
    }
    return $returnstring;
}
