<?php

namespace App\Models;

use App\Lib\Model;

class Bid extends Model
{
    protected static $table_name = "bids";

    protected $id = 0;
    protected $item_id;
    protected $amount;
    protected $user_id;

    public function __construct($item_id, $user_id, $amount)
    {
        $this->item_id = $item_id;
        $this->user_id = $user_id;
        $this->amount = $amount;
    }
}
