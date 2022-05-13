<?php

namespace App\Repositories\Contracts;

interface CoinInterface
{
    public function getCoin($coin_id);

    public function createCoin($payload);
}
