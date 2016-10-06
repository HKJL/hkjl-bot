<?php

function karmaup($args) {

    include("sqlconfig.php");
    $dbh = new PDO('mysql:host=localhost;dbname='.$db,$user,$pass,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")); 

    if(karmalookup($args) == $args." heeft nog geen karma.") {
        $query = $dbh->prepare("INSERT INTO karma(name,karma) VALUES (:name,1)");
    } else {
        $query = $dbh->prepare("UPDATE karma SET karma = karma + 1 WHERE name=:name");
    }
    $query->bindParam(':name',$args,PDO::PARAM_STR);
    $query->execute();

    return karmalookup($args);

}

function karmadown($args) {

    include("sqlconfig.php");
    $dbh = new PDO('mysql:host=localhost;dbname='.$db,$user,$pass,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

    if(karmalookup($args) == $args." heeft nog geen karma.") {
        $query = $dbh->prepare("INSERT INTO karma(name,karma) VALUES (:name,-1)");
    } else {
        $query = $dbh->prepare("UPDATE karma SET karma = karma - 1 WHERE name=:name");
    }
    $query->bindParam(':name',$args,PDO::PARAM_STR);
    $query->execute();

    return karmalookup($args);

}

function karmalookup($args) {

    include("sqlconfig.php");
    $dbh = new PDO('mysql:host=localhost;dbname='.$db,$user,$pass,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

    $query = $dbh->prepare("SELECT karma FROM karma WHERE name=:name");
    $query->bindParam(':name',$args,PDO::PARAM_STR);
    $query->execute();

    if($query->rowCount() > 0) {
        $karma = $query->fetchColumn();
        return $args." heeft karma: ".$karma;
    } else {
        return $args." heeft nog geen karma.";
    }

}
