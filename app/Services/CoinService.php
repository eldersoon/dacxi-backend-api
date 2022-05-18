<?php

namespace App\Services;

use App\Repositories\Eloquent\CoinRepository;
use Codenixsv\CoinGeckoApi\CoinGeckoClient;
use GuzzleHttp\Exception\ClientException;

class CoinService
{
    private $clientCoinGeckoApi;

    public function __construct(CoinRepository $coinRepository)
    {
        $this->coinRepository = $coinRepository;
        $this->clientCoinGeckoApi =  new CoinGeckoClient();
    }

    public function getCoinPriceNow($payload)
    {
        try {

            $coin_id =  $payload->coin_id ? $payload->coin_id : 'bitcoin';

            $coinPrice = $this->clientCoinGeckoApi->simple()
                ->getPrice($coin_id, $payload->vs_currency);

            $coinData = $this->clientCoinGeckoApi->coins()->getCoin($coin_id, [
                'tickers' => false,
                'market_data' => false,
            ]);

            if(count($coinPrice[$coin_id]) !== 1) {
                return response()->json([
                    'message' => 'Please choose ONE valid currency!'
                ], 404);
            }

            $requestPayload = [
                'coin_id' => 'bitcoin',
                'coin_symbol' => $coinData['symbol'],
                'coin_name'  => $coinData['name'],
                'price'  => $coinPrice[$coin_id][$payload->vs_currency],
            ];

            $this->coinRepository->create($requestPayload);

        }catch (ClientException $clientErr) {
            return response()->json(($clientErr->getMessage()), $clientErr->getCode());
        } catch (\PDOException $err) {
            return response()->json($err);
        }

        return response()->json($coinPrice);
    }

    public function getEstimatedCoinPriceByDate($payload)
    {
        try {

            $coin_id =  $payload->coin_id ? $payload->coin_id : 'bitcoin';

            $result = $this->clientCoinGeckoApi->coins()
                        ->getHistory($coin_id, $payload->date);

        } catch (ClientException $clientErr) {
            if(strpos($clientErr->getMessage(), 'invalid date') !== false){
                return response()->json([
                    'message' => 'Invalid date format. Expected format: dd-mm-yyyy',
                ], $clientErr->getCode());
            } else if(strpos($clientErr->getMessage(), 'not find coin') !== false) {
                return response()->json([
                    'message' => 'Could not find coin price for given coin_id',
                ], $clientErr->getCode());
            }
        }

        if($payload->vs_currency) {
            return response()->json(
                [
                    $result['symbol'] . '/' .$payload->vs_currency =>
                    number_format(
                        $result['market_data']['current_price'][$payload->vs_currency], 2, '.', ''
                    )
                ]
            );
        }

        $allParities = [];

        foreach($result['market_data']['current_price'] as $key => $value) {
            array_push($allParities, [
                $result['symbol'] . '/' . $key =>
                number_format(
                    $value, 2, '.', ''
                )
            ]);
        }


        return response()->json($allParities);
    }
}
