<?php

namespace app\configuration;


class DatabaseConfigurator{

    const CONFIGURATION = [
        'default'=>[
            'host'=>'127.0.0.1',
            'port'=>3306,
            'dbname'=>'blog',
            'user'=>'root',
            'pass'=>'',
            'charset'=>'utf8'
        ],
        'custom1'=>[]
    ];

    const DEFAULT_CONFIGURATION = 'default';

    public static function getConfiguration(string $configName = self::DEFAULT_CONFIGURATION){
        return (object)self::CONFIGURATION[$configName];
    }

}