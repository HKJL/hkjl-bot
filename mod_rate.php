<?php

/**
 * Class modRate
 * Convert currency rates
 *
 * TODO merge with mod_coin.php or better, find an API that also supports digital currencies
 *
 * @Version: 0.1
 * @Author:  rab1t
 * @Since:   30-5-2017
 */
class modRate {

    const DEFAULT_FROM = 'EUR';
    const SECOND_FROM = 'USD';

    private static function getCurrencies() {
        $jsonResponse = file_get_contents('http://api.fixer.io/latest');
        if(!$result = json_decode($jsonResponse, true)) {
            return 'Er ging iets fout.';
        }

        // assuming 'rates' is set
        return implode(', ', array_keys($result['rates']));
    }


    public static function rate($args = '') {
        // get params as array and remove empty ones
        $params = array_filter(explode(' ', trim(strtoupper($args))));
        $times = 1;

        // no params
        if(count($params) < 1) {
            return '[RATE] !rate [aantal] [van] naar'. PHP_EOL .
                '[RATE] Beschikbare valuta: '. self::getCurrencies();
        } // 1 param
        elseif(count($params) === 1) {
            $fromCurrency = $params[0] === self::DEFAULT_FROM ? self::SECOND_FROM : self::DEFAULT_FROM;
        } // first param is numeric
        elseif(is_numeric($params[0])) {
            $times        = (int) array_shift($params);
            $fromCurrency = array_shift($params);
        } // multiple string params
        else {
            $fromCurrency = array_shift($params);
        }

        $jsonResponse = file_get_contents('http://api.fixer.io/latest?base=' . $fromCurrency . '&symbols=' . urlencode(implode(',', $params)));
        if(!$result = json_decode($jsonResponse, true)) {
            return '[RATE] Er ging iets fout.';
        }

        $return = [];

        //assuming 'rates' is set
        foreach($result['rates'] as $curr => $rate) {
            // format the currency/rate and add it to an array so we can return it
            $return[] = $times . ' ' . $fromCurrency . ' = ' . number_format($times * $rate, 2, ',', '.') . ' ' . $curr;
        }

        return '[RATE] ' . (implode(', ', $return) ?: 'Valuta niet gevonden');
    }
}
