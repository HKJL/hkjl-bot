<?php

function urban($args) {
    
    $json = file_get_contents("http://api.urbandictionary.com/v0/define?term=".urlencode($args));
    $results = json_decode($json, 1);

    if($results["result_type"]=="exact") {
        return "[Urban] ".$results["list"][0]["definition"];
    } else {
        return "[Urban] Niet gevonden op urbandictionary.com...";
    }
}
