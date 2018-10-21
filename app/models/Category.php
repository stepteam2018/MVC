<?php
/**
 * Created by PhpStorm.
 * User: broker
 * Date: 07.10.2018
 * Time: 7:22
 */

namespace app\models;


use core\base\Model;

class Category extends Model{

    public static $table = "categories";
    public function posts(){
        return $this->hasMany(Post::class, "category_id");
    }
}