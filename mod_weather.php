<?php

function weather($args) {

    // Load $weather_api_key
    include("mod_weather_config.php");   

    // Look up city ID (file contains only NL and BE cities)
    $file = file_get_contents('/var/www/hackenkunjeleren.nl/bot/vendor/openweathermap.org/cities_nl_be.json',true);
    $data = explode("\n", $file); 
    $id = 0;
    foreach($data as $dataline)
    {
      $json = json_decode($dataline,true);
      if(strtolower($json["name"])==strtolower($args))
      {
        $id = $json["_id"];
        break;
      }
    }

    if(!$id)
    {
      return "[Weer] Lokatie niet gevonden in NL of BE";
    } else {
      $data = json_decode(file_get_contents('http://api.openweathermap.org/data/2.5/weather?id='.$id."&lang=nl&units=metric&appid=".$weather_api_key),true);
      $output = "[Weer] Het weer op dit moment in ".$data['name']." is: ".$data['weather'][0]['description']." | Temperatuur: ".$data['main']['temp']."°C (min ".$data['main']['temp_min']."°C, max ".$data['main']['temp_max']."°C) | Luchtvochtigheid: ".$data['main']['humidity']."% | Windkracht: ".$data['wind']['speed'];
      return $output;
    }

}