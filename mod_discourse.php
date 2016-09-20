<?php

function discourse_search($args) {
    
    // Load file containing $discourse_api_key value
    include("mod_discourse_config.php");

    $json = file_get_contents("https://community.hackenkunjeleren.nl/search.json?api_key=".$discourse_api_key."&api_username=Test&q=".urlencode($args));
    $results = json_decode($json, 1);

    if(array_key_exists("topics",$results) && $results["topics"] > 0) {
        return "[Discourse] https://community.hackenkunjeleren.nl/t/".$results["topics"][0]["slug"]."/".$results["topics"][0]["id"]." - ".$results["topics"][0]["title"]." - ".$results["posts"][0]["blurb"];
    } else {
        if(count($results["posts"])>0) {
            return "[Discourse] https://community.hackenkunjeleren.nl/p/".$results["posts"][0]["id"]." - ".$results["posts"][0]["blurb"];
        } else {
            return "[Discourse] Geen topics of posts gevonden...";
        }
    }

}