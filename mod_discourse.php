<?php

function discourse_search($args) {
    
    // Load file containing $discourse_api_key value
    include("mod_discourse_config.php");

    // Force IPv4 (temp hack since communtiy is unreachable over v6)
    $opts = array('socket' => array('bindto' => '149.210.200.17:0'));
    $context = stream_context_create($opts);

    $json = file_get_contents("https://community.hackenkunjeleren.nl/search.json?api_key=".$discourse_api_key."&api_username=Test&q=".urlencode($args),false,$context);
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

function discourse_latest() {

    // Load file containing $discourse_api_key value
    include("mod_discourse_config.php");
    include("sqlconfig.php");

    $dbh = new PDO('mysql:host=localhost;dbname='.$db,$user,$pass,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

    // Force IPv4 (temp hack since communtiy is unreachable over v6)
    $opts = array('socket' => array('bindto' => '149.210.200.17:0'));
    $context = stream_context_create($opts);

    $json = file_get_contents("https://community.hackenkunjeleren.nl/latest.json?api_key=".$discourse_api_key."&api_username=Test", false, $context);

    if($json === FALSE) {
        // Forum JSON is unreachable
        $query = $dbh->prepare("SELECT value FROM storage WHERE name='discourse-online'");
        $query->execute();
        $discourseonline = $query->fetchColumn();
        if($discourseonline=="true") {
            $query = $dbh->prepare("UPDATE storage SET value='false' WHERE name='discourse-online'");
            $query->execute();
            return "[Discourse] Forum is OFFLINE!";
        } else {
            return "";
        }
    } else {
        // Forum JSON is reachable
        $query = $dbh->prepare("SELECT value FROM storage WHERE name='discourse-online'");
        $query->execute();
        $discourseonline = $query->fetchColumn();
        if($discourseonline=="false") {
            $query = $dbh->prepare("UPDATE storage SET value='true' WHERE name='discourse-online'");
            $query->execute();
            return "[Discourse] Forum doet het weer! \o/";
        } else {
            $results = json_decode($json, 1);

            // Skip pinned topics
            $i = 0;
            while($results["topic_list"]["topics"][$i]["pinned"]=="true") {
                $i++;
            }

            $text = "⭐  \"".$results["topic_list"]["topics"][$i]["title"]."\" door ".$results["topic_list"]["topics"][$i]["last_poster_username"]. " - ";
            $url = "https://community.hackenkunjeleren.nl/t/".$results["topic_list"]["topics"][$i]["slug"]."/".$results["topic_list"]["topics"][$i]["id"]."/".$results["topic_list"]["topics"][$i]["highest_post_number"];

            $query = $dbh->prepare("SELECT value FROM storage WHERE name='discourse-last-reported-url'");
            $query->execute();
            $latestreportedurl = $query->fetchColumn();

            if($latestreportedurl == $url) {
                return "";
            } else {
                $query = $dbh->prepare("UPDATE storage SET value=:url WHERE name='discourse-last-reported-url'");
                $query->bindParam(':url',$url,PDO::PARAM_STR);
                $query->execute();
                return "[Discourse] ".$text.$url;
            }
        }
    }
}
