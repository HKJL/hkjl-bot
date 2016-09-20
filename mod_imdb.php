<?php

function imdb($args) {

    if(strlen($args)>1) {
        $result = json_decode(file_get_contents("http://www.omdbapi.com/?t=".urlencode($args)),true);
        if($result["Response"]=="False") {
            $returnstring = "[IMDB] Error: ".$result["Error"];
        } else {
            $returnstring = "[IMDB] http://www.imdb.com/title/".$result["imdbID"]." - ".$result["Title"].", ".$result["Year"]." (".$result["imdbRating"].") - ".$result["Plot"];
        }
    } else {
        $returnstring = "[IMDB] Error: Geef een zoekterm van 2 of meer tekens!";
    }
    
    return $returnstring;

}