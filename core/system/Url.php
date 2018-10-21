<?php

namespace core\system;


class Url{
    public static function redirect(string $path):void {
        header("Location:".$path);
    }

//    public static function redirectBack(string $path):void {
//        header("Location:".$path);
//    }

    public static function get():string {
        return $_SERVER["REQUEST_URI"];
    }

    public static function getPath():string {
        return explode("?",self::get())[0];
    }

    public static function getQuery():string {
        return explode("?",self::get())[1];
    }
}