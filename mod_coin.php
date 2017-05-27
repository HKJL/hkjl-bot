<?php

function coin($args) {
    $from = strtoupper($args);
    $result = @file_get_contents("https://apiv2.bitcoinaverage.com/convert/global?from=" . urlencode($from) . "&to=USD&amount=1");
    if(!$result) {
        $returnstring = "[COIN] Ongeldige valuta, probeer eens BTC of ETH";    
    } else {
        $result = json_decode($result, true);
        if($result["success"] =! "ok") {
            $returnstring = "[COIN] Er is iets mis met de API...";
        } else {
            $returnstring = "[COIN] " . $from . " waarde is op dit moment: $ " . $result["price"];
        }
    }
    return $returnstring;
}

