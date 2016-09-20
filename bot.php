<?php

include("mod_youtube.php");
include("mod_discourse.php");
include("mod_weather.php");
include("mod_httpstatus.php");
include("mod_imdb.php");
include("mod_google.php");
include("mod_wolfram.php");

$action = $_GET['action'];
$args = $_GET['args'];

// Weird chars being added by python script? 
$args = str_replace("%0D%0A","",$args);

// Remove any extra whitespace around the incoming data before parsing
$args = trim(preg_replace('/\s\s+/', ' ', $args));

switch($action)
{
    case 'version':
        output("HKJL Bot - Eigenaar: Sling - Help: \$help");
        break;
    case 'base64encode':
        output(base64_encode($args));
        break;
    case 'base64decode':
        output(base64_decode($args));;
        break;	
    case 'imdb':
        output(imdb($args));
        break;
    case 'discourse':
        output(discourse_search($args));
        break;
    case 'google':
        output(google($args));
        break;
    case 'lmgtfy':
        output("http://lmgtfy.com/?q=".urlencode($args));
        break;
    case 'help':
        output("Handleiding van deze bot: https://community.hackenkunjeleren.nl/t/handleiding-hkjl-irc-bot/199");
        break;
    case 'wolfram':
        output(wolfram($args));
        break;
    case 'yt':
    case 'youtube':
        output(youtube($args));
        break;
    case 'md5':
        output(md5($args));
        break;
    case 'echo':
        output($args);
        break;
    case 'bin2hex':
        output(dechex(bindec($args)));
        break;
    case 'reverse':
        output(strrev($args));
        break;
    case 'rot13':
        output(str_rot13($args));
        break;
    case 'ascii2bin':
        for($i = 0; $i != strlen($args); $i++) {
            $value = unpack('H*', $args[$i]);
            $output .= base_convert($value[1], 16, 2).' ';
        }
        output($output);
        break;
    case 'ascii2oct':
        for($i = 0; $i != strlen($args); $i++) {
            $value = unpack('H*', $args[$i]);
            $output .= base_convert($value[1], 16, 8).' ';
        }
        output($output);
        break;
    case 'ascii2dec':
        for($i = 0; $i != strlen($args); $i++) {
            $output .= ord($args[$i]).' ';
        }
        output($output);
        break;
    case 'ascii2hex':
        for($i = 0; $i != strlen($args); $i++) {
            $output .= dechex(ord($args[$i])).' ';
        }
        output($output);
        break;
    case 'levenshtein':
        $args = explode(' ',$args,2);
        output(levenshtein($args[0],$args[1]));
        break;
    case 'dice':
    case 'dobbelsteen':
        output("The dice rolls.... ".rand(1,6));
        break;
    case 'w':
    case 'weer':
        output(weather($args));
        break;
    case 'httpbanner':
        output(http_banner($args));
        break;
    case 'httpstatus':
        output(httpstatus($args));
        break;
    default:
        // Silently ignore invalid actions to prevent unwanted spamming
        break;
}

function http_banner($args) {
    // If user did not supply a HTTP or HTTPS scheme, add a HTTP scheme.
    if(strpos($args,"http://")!==0 && strpos($args,"https://")!==0) {
        $args = "http://".$args;
    }

    // We're going to make a HEAD request, instead of the default GET, since we're not interested in a response body.
    stream_context_set_default(array('http'=>array('method'=>'HEAD')));

    // If the user gave us a valid URL, retrieve the HTTP headers.
    if(filter_var($args, FILTER_VALIDATE_URL)) {
        $headers = get_headers($args);
        if(!$headers) {
            return "This host looks down to me...";
        } else {
            return array_values(array_filter($headers, function($v) { return strpos($v, 'Server:') !== false; }))[0];
        }
    } else {
        return "That doesn't look like a valid URL...";
    }
}

function output($output) {
    // Remove any spaces around output and remove newlines
    $output = trim($output);
    $output = str_replace(array("\r", "\n"), '', $output);

    // Limit output length
    if(strlen($output) > 450) {
      $output = substr($output,0,445)."(...)";
    }

    echo htmlspecialchars($output);
}