<?php

namespace app\models;

use core\base\Model;

class Lme extends Model{

    public static $table = "lme";

    public function metalName(){
        return $this->belongsTo(Metal::class, "metal_id", "id");
    }
}