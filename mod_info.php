<?php

function setinfo($args) {

    $matched = preg_match('/^(.*?)=(.*)$/', $args, $split);
    if(!$matched || empty($split[1]) || empty($split[2])) {
        return "[info] Je invoer was niet geldig, heb je wel een = teken gebruikt? Bijvoorbeeld: info+ hacken kun je leren = cool";
    } else {

        $name = trim($split[1]);
        $value = trim($split[2]);

        if(strlen($name)==0 || strlen($value)==0) {
            return "[info] Ik begrijp er niks van, heb je wel een = teken gebruikt? Bijvoorbeeld: info+ hacken kun je leren = cool";
        } elseif(strlen($name)>100 || strlen($value)>255) {
            return "[info] Je invoer was niet geldig, de naam van het infoitem mag maximaal 100 tekens zijn, en de inhoud maximaal 255 tekens!";        
        } else {

            include("sqlconfig.php");
            $dbh = new PDO('mysql:host=localhost;dbname='.$db,$user,$pass,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")); 
            $query = $dbh->prepare("INSERT INTO info(name,value) VALUES (:name,:value)");
            $query->bindParam(':value',$value,PDO::PARAM_STR);
            $query->bindParam(':name',$name,PDO::PARAM_STR);
            $query->execute();

            return "[info] Item toegevoegd";
        }
    }
}

function delinfo($args) {

    $index = end(explode(' ',$args));

    if(!is_numeric($index)) {
        return "[info] Je hebt geen geldige index opgegeven, gebruik: 'info- <naam> <N>' om het N-de infoitem van <naam> te verwijderen.";
    } else {
        preg_match("/^(.*)\ [0-9]+$/",$args,$names);
        $name = $names[1];

        include("sqlconfig.php");
        $dbh = new PDO('mysql:host=localhost;dbname='.$db,$user,$pass,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
        $query = $dbh->prepare("SELECT value FROM info WHERE name=:name");
        $query->bindParam(':name',$name,PDO::PARAM_STR);
        $query->execute();
        $rowcount = $query->rowCount();
        if($rowcount == 0) {
            return "[info] Dit item bestaat helemaal niet! ";
        } else {
            if($rowcount < $index) {
                return "[info] Deze index is iets te hoog, helaas..";
            } else {
                $values = $query->fetchAll(PDO::FETCH_COLUMN);
                $value = $values[$index - 1];
                $query = $dbh->prepare("DELETE FROM info WHERE name=:name AND value=:value");
                $query->bindParam(':name',$name,PDO::PARAM_STR);
                $query->bindParam(':value',$value,PDO::PARAM_STR);
                $query->execute();
                return "[info] Item verwijderd";
            }
        }
    }
}

function getinfo($args) {

    include("sqlconfig.php");
    $dbh = new PDO('mysql:host=localhost;dbname='.$db,$user,$pass,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

    $query = $dbh->prepare("SELECT value FROM info WHERE name=:name");
    $query->bindParam(':name',$args,PDO::PARAM_STR);
    $query->execute();

    if($query->rowCount() > 0) {

        $values = $query->fetchAll(PDO::FETCH_COLUMN);
        for($i=1; $i<count($values); $i++) {
            $list .= "(" . $i . ") " . $values[$i-1] . ", ";
        }
        $list .= "(" . $i . ") " . $values[$i-1];

        return "[info] ".$args." = ".$list;
    } else {
        return "[info] Geen informatie bekend, gebruik 'info+' om een info item toe te voegen.";
    }

}
