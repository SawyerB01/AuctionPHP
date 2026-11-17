<?php

namespace App\Models;

use App\Exceptions\ClassException;
use App\Lib\Model;

class Item extends Model
{
    protected static $table_name = "items";
    protected $id = 0;
    protected $user_id;
    protected $cat_id;
    protected $name;
    protected $price;
    protected $description;
    protected $date;
    protected $notified = 0;

    protected $imageObj = null;
    protected $bidObjs = [];


    public function getImage() {

        try {
            $this->imageObj = Image::findFirst(["item_id" => $this->id]);
        } catch (ClassException $e) {
            $this->imageObj = null;
        }
        return $this->imageObj;
    }

    public function getBids() {
        $this->bidObjs = Bid::find(["item_id" => $this->id],
        null, "amount DESC");

        return $this->bidObjs;
    }


    public function __construct($user_id, $cat_id, $name, $price, $description, $date)
    {
        $this->user_id = $user_id;
        $this->cat_id = $cat_id;
        $this->name = $name;
        $this->price = $price;
        $this->description = $description;
        $this->date = $date;
    }


    public static $errorArray = array (
      "lowprice" => "The bid entered is too low. Please enter a higher price.",
      "letter" => "The value entered is not a number, try again."
    );
}