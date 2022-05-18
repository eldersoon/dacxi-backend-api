<?php

namespace App\Repositories\Eloquent;

use App\Models\Coin;
use App\Repositories\Contracts\CoinInterface;
use App\Repositories\Eloquent\BaseRepository;

class CoinRepository extends BaseRepository implements CoinInterface
{

    /**
     * Set Coin Model
     * @var $model
     */
    protected $model = Coin::class;


    public function getCoinPrice($coin_id)
    {
        $query = $this->newQuery();
        return $query->select([
            'id', 'coin_id', 'coin_symbol', 'coin_name', 'coin_price'
        ])
            ->where('coin_id', $coin_id)
            ->order_by('created_at', 'desc')
            ->first();
    }

    /**
     * Service Create Coin
     * @param array $payload
     * @return Builder
     */
    public function create($payload)
    {
        $query = $this->newQuery();
        return $query->create($payload);
    }
}
