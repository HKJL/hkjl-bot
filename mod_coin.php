<?php

function coin($args) {
    $from = strtoupper($args);
    $resultusd = @file_get_contents("https://apiv2.bitcoinaverage.com/convert/global?from=" . urlencode($from) . "&to=USD&amount=1");
    if(!$resultusd) {
        $returnstring = "[COIN] Ongeldige valuta, probeer eens BTC of ETH";    
    } else {
        $resulteur = @file_get_contents("https://apiv2.bitcoinaverage.com/convert/global?from=" . urlencode($from) . "&to=EUR&amount=1");
        $resultusd = json_decode($resultusd, true);
        $resulteur = json_decode($resulteur, true);
        if($resultusd["success"] =! "ok" || $resulteur["success"] =! "ok") {
            $returnstring = "[COIN] Er is iets mis met de API...";
        } else {
            $returnstring = "[COIN] " . $from . " waarde is op dit moment: $ " . $resultusd["price"] . " / € " . $resulteur["price"];
        }
    }
    return $returnstring;
}

