<?php

function imdb($args) {

    if(strlen($args)>1) {
        $search = preg_replace('/[^A-Za-z0-9]/', '', preg_replace('/\s/', '_', strtolower($args)));
        $result = file_get_contents("https://v2.sg.media-imdb.com/suggests/".substr($search,0,1)."/".urlencode($search).".json");
        $result = preg_replace('/^imdb.*\(/', '', $result);
        $result = json_decode(rtrim($result, ')'), true);
        if(empty($result)) {
            $returnstring = "[IMDB] Error: www.imdb.com not available!";
        } elseif(empty($result['d']) || count($result['d']) < 1) {
            $returnstring = "[IMDB] Geen resultaat";
        } else {
            $result = $result['d'][0];
            $type = substr($result['id'],0,2);
            if($type == 'nm') {
              $returnstring = "[IMDB] http://www.imdb.com/name/".$result["id"]." - ".$result["l"];
            } else {
              $returnstring = "[IMDB] http://www.imdb.com/title/".$result["id"]." - ".$result["l"].", ".$result["y"];
            }
        }
    } else {
        $returnstring = "[IMDB] Error: Geef een zoekterm van 2 of meer tekens!";
    }
    
    return $returnstring;

}
