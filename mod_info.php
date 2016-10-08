<?php

function setinfo($args) {

    $split = split(' = ', $args, 2);
    if(count($split)==1) {
        return "[info] Je invoer was niet geldig, heb je wel een = teken gebruikt? Bijvoorbeeld: info+ hacken kun je leren = cool";
    } else {

        $name = trim($split[0]);
        $value = trim($split[1]);

        if(strlen($name)==0 || strlen($value)==0) {
            return "[info] Ik begrijp er niks van, heb je wel een = teken gebruikt? Bijvoorbeeld: info+ hacken kun je leren = cool";
        } elseif(strlen($name)>100 || strlen($value)>255) {
            return "[info] Je invoer was niet geldig, de naam van het infoitem mag maximaal 100 tekens zijn, en de inhoud maximaal 255 tekens!";        
        } else {

            include("sqlconfig.php");
            $dbh = new PDO('mysql:host=localhost;dbname='.$db,$user,$pass,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")); 

            $oldvalue = getinfo($name);

            if($oldvalue == "[info] Geen informatie bekend, gebruik 'info+' om een info item toe te voegen.") {
                $query = $dbh->prepare("INSERT INTO info(name,value) VALUES (:name,:value)");
                $return = "[info] Item toegevoegd";
            } else {
                $query = $dbh->prepare("UPDATE info SET value=:value WHERE name=:name");
                $return = "[info] Item aangepast. Was: ".$oldvalue;
            }
            $query->bindParam(':value',$value,PDO::PARAM_STR);
            $query->bindParam(':name',$name,PDO::PARAM_STR);
            $query->execute();
    
            return $return;
        }
    }
}

function delinfo($args) {

    include("sqlconfig.php");
    $dbh = new PDO('mysql:host=localhost;dbname='.$db,$user,$pass,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

    if(getinfo($args) == "[info] Geen informatie bekend, gebruik 'info+' om een info item toe te voegen.") {
        return "[info] Dit item bestaat helemaal niet!";  
    } else {
        $query = $dbh->prepare("DELETE FROM info WHERE name=:name");
    }
    $query->bindParam(':name',$args,PDO::PARAM_STR);
    $query->execute();

    return "[info] Item verwijderd";

}

function getinfo($args) {

    include("sqlconfig.php");
    $dbh = new PDO('mysql:host=localhost;dbname='.$db,$user,$pass,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

    $query = $dbh->prepare("SELECT value FROM info WHERE name=:name");
    $query->bindParam(':name',$args,PDO::PARAM_STR);
    $query->execute();

    if($query->rowCount() > 0) {
        $value = $query->fetchColumn();
        return $value;
    } else {
        return "[info] Geen informatie bekend, gebruik 'info+' om een info item toe te voegen.";
    }

}
