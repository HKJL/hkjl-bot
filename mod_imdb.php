<?php

function imdb($args) {

    if(strlen($args)>1) {
        $search = preg_replace('/[^A-Za-z0-9]/', '', preg_replace('/\s/', '_', strtolower($args)));
        $result = file_get_contents("https://v2.sg.media-imdb.com/suggests/a/".urlencode($search).".json");
        $result = preg_replace('/^imdb.*\(/', '', $result);
        $result = json_decode(rtrim($result, ')'), true);
        if($result == NULL) {
            $returnstring = "[IMDB] Error: www.imdb.com not available!";
        } elseif(count($result['d']) < 1) {
            $returnstring = "[IMDB] Geen resultaat";
        } else {
            $result = $result['d'][0];
            $returnstring = "[IMDB] http://www.imdb.com/title/".$result["id"]." - ".$result["l"].", ".$result["y"];
        }
    } else {
        $returnstring = "[IMDB] Error: Geef een zoekterm van 2 of meer tekens!";
    }
    
    return $returnstring;

}
