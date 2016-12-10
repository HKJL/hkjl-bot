<?php

function ripe($args) {

    if(!filter_var($args, FILTER_VALIDATE_IP) === false) {
        $result = json_decode(file_get_contents("https://stat.ripe.net/data/whois/data.json?resource=".urlencode($args)."/32"),true);
        if($result["status"]=!"ok") {
            $returnstring = "[RIPE] Er is iets mis met de API...";
        } elseif(count($result["data"]["records"])==0) {
            $returnstring = "[RIPE] Geen resultaten";
        } else {
            $data = "IP-block: ";
            foreach($result["data"]["records"][0] as $record) {
                switch($record["key"]) {
                    case "inetnum":
                    case "netname":
                    case "descr":
                    case "country":
                        $data .= "[".$record["value"]."] ";        
                    default:
                } 
            }
            foreach($result["data"]["irr_records"][0] as $record) {
                switch($record["key"]) {
                    case "origin":
                        $data .= "| Network: AS".$record["value"]." |";
                    default:
                }
            }
            $returnstring = "[RIPE] Info about ".$args.": ".$data." rDNS: ".gethostbyaddr($args);    
        }
    } else {
        $ip = gethostbyname($args);
        if(!filter_var($ip, FILTER_VALIDATE_IP) === false) {
            return ripe($ip);
        } else {
            $returnstring = "[RIPE] Dit is geen geldig IP-adres!";
        }
    }
    return $returnstring;
}

