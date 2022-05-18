<?php

namespace App\Repositories\Contracts;

interface CoinInterface
{
    public function getCoinPrice($payload);

    public function create($payload);
}
