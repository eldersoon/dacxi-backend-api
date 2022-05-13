<?php

namespace App\Repositories\Eloquent;

use App\Models\Coin;
use App\Repositories\Contracts\CoinInterface;

class CoinRepository extends BaseRepository implements CoinInterface
{

    /**
     * Set User Model
     *
     * @var $modelClass
     */
    protected $modelClass = Coin::class;


    public function getCoin($coin_id)
    {
        $query = $this->newQuery();
        $query->find($coin_id);
        return $query->select(['id', 'coin_id', 'coin_symbol', 'coin_name', 'coin_from_date', 'coin_to_date', 'coin_price']);
    }

    /**
     * Service Create User
     *
     * @param array $payload
     * @return Builder
     */
    public function createCoin($payload)
    {
        $query = $this->newQuery();
        return $query->create($payload);
    }
}
