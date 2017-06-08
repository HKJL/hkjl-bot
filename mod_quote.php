<?php

function addquote($args) {

    if(strlen($args)>450) {
        return "[quote] Deze quote is te lang!";
    } else {
        include("sqlconfig.php");
        $dbh = new PDO('mysql:host=localhost;dbname='.$db,$user,$pass,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
        $query = $dbh->prepare("INSERT INTO quote(id,quote) VALUES (0,:quote) ");
        $query->bindParam(':quote',$args,PDO::PARAM_STR);
        $query->execute();
        return "[quote] Quote toegevoegd (#".$dbh->lastInsertId().")";
    }

}

function delquote($args) {

    include("sqlconfig.php");
    $dbh = new PDO('mysql:host=localhost;dbname='.$db,$user,$pass,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

    if(getquote($args) == "[quote] Geen quote gevonden met dit ID") {
        return "[quote] Deze quote bestaat helemaal niet!";
    } else {
        $query = $dbh->prepare("DELETE FROM quote WHERE id=:id");
    }
    $query->bindParam(':id',$args,PDO::PARAM_STR);
    $query->execute();

    return "[quote] Quote verwijderd";

}

function quote($args) {

    include("sqlconfig.php");
    $dbh = new PDO('mysql:host=localhost;dbname='.$db,$user,$pass,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

    if(!empty($args)) {
        $args = "%$args%";
        $query = $dbh->prepare("SELECT * FROM quote WHERE quote LIKE :search ORDER BY RAND() LIMIT 0,1");
        $query->bindParam('search',$args,PDO::PARAM_STR);
    } else {
        $query = $dbh->prepare("SELECT * FROM quote ORDER BY RAND() LIMIT 0,1");
    }
    $query->execute();

    if($query->rowCount() > 0) {
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return "[quote] #".$result['id']." ".$result['quote'];
    } else {
        return "[quote] Geen quotes gevonden, voeg er eentje toe met 'quote+'";
    }

}

function getquote($args) {

    include("sqlconfig.php");
    $dbh = new PDO('mysql:host=localhost;dbname='.$db,$user,$pass,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

    $query = $dbh->prepare("SELECT quote FROM quote WHERE id=:id");
    $query->bindParam(':id',$args,PDO::PARAM_STR);
    $query->execute();

    if($query->rowCount() > 0) {
        $quote = $query->fetchColumn();
        return "[quote] #".$args." ".$quote;
    } else {
        return "[quote] Geen quote gevonden met dit ID";
    }

}
