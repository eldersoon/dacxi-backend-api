<?php

namespace App\Http\Controllers;

use App\Services\CoinService;
use Illuminate\Http\Request;

class CoinsController extends Controller
{

    protected $coinService;

    public function __construct(CoinService $coinService)
    {
        $this->coinService = $coinService;
    }

    public function getCoinPriceNow(Request $request)
    {
        return  $this->coinService->getPriceCoinNow($request);
    }

    public function getEstimatedCoinPrice(Request $request)
    {
        return $this->coinService->getEstimatedCoinPrice($request);
    }
}
