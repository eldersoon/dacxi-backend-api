<?php

namespace App\Services;
use Codenixsv\CoinGeckoApi\CoinGeckoClient;

class CoinService
{
    public function getPriceCoin()
    {
        /**
         * accepted formats:
         * 'now'
         * '2022-05-13' or '2022-05-13 09:45:45'
         * '12 May 2022' or '12 May 2022 09:45:45'
         */
        $fromString = '12 May 2022 09:15:45';
        $toString = '2022-05-13 00:00:00';

        $fromUnix = strtotime($fromString);
        $toUnix = strtotime($toString);

        $client = new CoinGeckoClient();
        // $data = $client->simple()->getPrice('0x,bitcoin', 'usd,rub'); // get by currency
        // $data = $client->simple()->getSupportedVsCurrencies(); // get suported currency
        $data = $client->coins()->getList(); //
        // $data = $result = $client->coins()->getMarkets('btc');
        // $result = $client->coins()->getCoin('bitcoin', ['tickers' => 'false', 'market_data' => 'false']);
        // $result = $client->coins()->getTickers('bitcoin');
        // $result = $client->coins()->getHistory('bitcoin', '30-12-2017'); // get bay date
        // $result = $client->coins()->getMarketChart('bitcoin', 'usd', 'max');
        $result = $client->coins()->getMarketChartRange('bitcoin', 'usd', $fromUnix, $toUnix); // id: bitcoin, vs_currency: usd,
        return response()->json($data);
    }

    public function getPriceCoinFromTo()
    {
        /**
         * accepted formats:
         * 'now'
         * '2022-05-13' or '2022-05-13 09:45:45'
         * '12 May 2022' or '12 May 2022 09:45:45'
         */
        $fromString = '12 May 2022 09:15:45';
        $toString = '2022-05-13 00:00:00';

        $fromUnix = strtotime($fromString);
        $toUnix = strtotime($toString);

        $client = new CoinGeckoClient();
        // $data = $client->simple()->getPrice('0x,bitcoin', 'usd,rub'); // get by currency
        // $data = $client->simple()->getSupportedVsCurrencies(); // get suported currency
        $data = $client->coins()->getList(); //
        // $data = $result = $client->coins()->getMarkets('btc');
        // $result = $client->coins()->getCoin('bitcoin', ['tickers' => 'false', 'market_data' => 'false']);
        // $result = $client->coins()->getTickers('bitcoin');
        // $result = $client->coins()->getHistory('bitcoin', '30-12-2017'); // get bay date
        // $result = $client->coins()->getMarketChart('bitcoin', 'usd', 'max');
        $result = $client->coins()->getMarketChartRange('bitcoin', 'usd', $fromUnix, $toUnix); // id: bitcoin, vs_currency: usd,
        return response()->json($data);
    }
}
