<?php
/**
 * Created by PhpStorm.
 * User: broker
 * Date: 26.09.2018
 * Time: 12:32
 */

namespace app\models;


use core\base\Model;

class Metal extends Model {

    public function prices(){
        return $this->hasMany(Lme::class, "metal_id", "id");
    }
}