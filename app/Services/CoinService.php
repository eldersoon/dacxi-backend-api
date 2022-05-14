<?php

namespace App\Services;

use App\Repositories\Eloquent\CoinRepository;
use Codenixsv\CoinGeckoApi\CoinGeckoClient;
use GuzzleHttp\Exception\ClientException;

class CoinService
{
    private $client;

    public function __construct(CoinRepository $coinRepository)
    {
        $this->coinRepository = $coinRepository;
        $this->client =  new CoinGeckoClient();
    }

    public function getPriceCoinNow($payload)
    {
        try {

            $coinPrice = $this->client->simple()
                ->getPrice($payload->coin_id, $payload->vs_currency);

            $coinData = $this->client->coins()->getCoin($payload->coin_id, [
                'tickers' => false,
                'market_data' => false,
            ]);

            if(count($coinPrice[$payload->coin_id]) !== 1) {
                return response()->json([
                    'message' => 'Please choose ONE valid currency!'
                ], 404);
            }

            $requestPayload = [
                'coin_id' => $coinData['id'],
                'coin_symbol' => $coinData['symbol'],
                'coin_name'  => $coinData['name'],
                'price'  => $coinPrice[$payload->coin_id][$payload->vs_currency],
            ];

            $this->coinRepository->createCoin($requestPayload);

        }catch (ClientException $clientErr) {
            return response()->json(($clientErr->getMessage()), $clientErr->getCode());
        } catch (\PDOException $err) {
            return response()->json($err);
        }

        return response()->json($coinPrice);
    }

    public function getEstimatedCoinPrice($payload)
    {
        try {

            $result = $this->client->coins()
                        ->getHistory($payload->coin_id, $payload->date);

        } catch (ClientException $clientErr) {
            if(strpos($clientErr->getMessage(), 'invalid date') !== false){
                return response()->json([
                    'message' => 'Invalid date formate. Expected format: dd-mm-yyyy',
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
                    $payload->vs_currency =>
                    $result['market_data']['current_price'][$payload->vs_currency]
                ]
            );
        }

        return response()->json($result['market_data']['current_price']);
    }
}
