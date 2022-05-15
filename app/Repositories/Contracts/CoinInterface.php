<?php

namespace App\Repositories\Contracts;

interface CoinInterface
{
    public function getBtc($payload);

    public function createCoin($payload);
}
