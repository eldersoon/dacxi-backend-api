<?php

namespace App\Services;

use App\Repositories\Eloquent\CoinRepository;
use Codenixsv\CoinGeckoApi\CoinGeckoClient;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Validator;
use \Datetime;

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
            $vs_currency = $payload->vs_currency ? $payload->vs_currency : 'usd';

            $coinPrice = $this->clientCoinGeckoApi->simple()
                ->getPrice($coin_id, $vs_currency);

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
                'price'  => $coinPrice[$coin_id][$vs_currency],
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
        $validator = Validator::make($payload->all(),[
            'datetime' => 'required',
        ]);

        if($validator->fails()) {
            return response()->json([
                'error' => [
                    'message' => 'Please verify the field(s) values',
                    'fields' => $validator->errors()->getMessages()
                ]
            ]);
        }

        $vs_currency = $payload->vs_currency ? $payload->vs_currency : 'usd';

        $now = date('Y-m-d H:i');
        $from = strtotime($payload->datetime);
        $to = strtotime($now);

        if($from > $to) {
            return response()->json([
                'error' => [
                    'message' => 'The date and time must be less than the current date and time'
                ]
            ], 400);
        }

        try {
            $coin_id =  $payload->coin_id ? $payload->coin_id : 'bitcoin';

            $result = $this->clientCoinGeckoApi->coins()->getMarketChartRange(
                $coin_id,
                $vs_currency,
                $from,
                $to
            );

            $closestDate = $this->find_closest($result['prices'], $payload->datetime, $vs_currency);


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

        return response()->json($closestDate);
    }

    public function find_closest($array, $findDate, $vs_currency)
    {
        $msDates = array();

        foreach($array as $index => $date)
        {
            $msDates[] = $date[0];

            foreach ($msDates as $a)
            {
                if ($a >= strtotime($findDate)){
                    $estimated = [
                        'datetime' => date('Y-m-d H:i', $array[$index][0] / 1000),
                        $vs_currency => number_format($array[$index][1], 2, '.', '')
                    ];
                    return $estimated;
                }

            }
        }
    }
}
