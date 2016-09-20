<?php

function wolfram($args) {

    // Load file containing $appid value
    include('mod_wolfram_config.php');

    $xmlresult = file_get_contents("http://api.wolframalpha.com/v2/query?appid=".$appid."&input=".urlencode($args)."&format=plaintext");
    $jsonresult = json_decode(json_encode(simplexml_load_string($xmlresult)),1);

    if($jsonresult["@attributes"]["success"]==="true") {
        foreach($jsonresult["pod"] as $pod) {
            if($pod["@attributes"]["primary"] == "true") {
                $return = $pod["subpod"]["plaintext"]; 
            }
        }
    } 

    if(empty($return)) {
        return "[Wolfram] Geen resultaten...";
    } else {
        return "[Wolfram] " . $return;
    }

}