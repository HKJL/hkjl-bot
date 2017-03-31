<?php

function weather($args) {

    // Load $weather_api_key
    include("mod_weather_config.php");   

    // Look up city ID (file contains only NL and BE cities)
    $file = file_get_contents(__DIR__ . '/vendor/openweathermap.org/cities_nl_be.json', true);
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

function windkracht($args) {

    $windkrachten = array(
        0 => "Windkracht 0: Stil - Rook stijgt recht of bijna recht omhoog",
        1 => "Windkracht 1: Zwak - Windrichting goed af te leiden uit rookpluimen",
        2 => "Windkracht 2: Zwak - Wind merkbaar in gezicht",
        3 => "Windkracht 3: Matig - Stof waait op",
        4 => "Windkracht 4: Matig - Haar in de war, kleding flappert",
        5 => "Windkracht 5: Vrij krachtig - Opwaaiend stof hinderlijk voor de ogen, gekuifde golven op meren en kanalen en vuilcontainers waaien om",
        6 => "Windkracht 6: Krachtig - Paraplu's met moeite vast te houden",
        7 => "Windkracht 7: Hard - Lastig tegen de wind in te lopen of te fietsen",
        8 => "Windkracht 8: Stormachtig - Voortbewegen zeer moeilijk",
        9 => "Windkracht 9: Storm - Schoorsteenkappen en dakpannen waaien weg, kinderen waaien om",
        10 => "Windkracht 10: Zware storm - Grote schade aan gebouwen, volwassenen waaien om",
        11 => "Windkracht 11: Zeer zware storm - Enorme schade aan bossen",
        12 => "Windkracht 12: Orkaan - Verwoestingen");

    $input = round($args);
    if($input >= 0 && $input < 13)
    {
        $output = $windkrachten[$input];
    } else {
        $output = "[Windkracht] Wat? Windkrachten gaan van 0 t/m 12 gast..";
    }

    return $output;

}
