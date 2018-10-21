<?php

namespace app\models;


use core\base\Model;

class Post extends Model{

    public static $table = "post";

    public function author(){
        return $this->belongsTo(User::class, "user_id", "id");
    }

    public function category(){
        return $this->belongsTo(Category::class, "category_id", "id");
    }
}