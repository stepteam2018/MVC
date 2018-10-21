<?php

namespace core\base;

use core\system\database\Database;

abstract class Model{

    protected $db;

    private $data = [];

    public function __construct(){
        $this->db = Database::instance();
    }

    public static function getTableName(){
        $Clazz = get_called_class();
        if (!empty($Clazz::$table)){
            $table = $Clazz::$table;
        } else {
            $parts = explode("\\", $Clazz);
            $table = strtolower(end($parts))."s";
        }
        return $table;
    }

    public static function __callStatic($name, $arguments){
        $Clazz = get_called_class();
        $table = self::getTableName();
        return call_user_func_array([Database::instance()->$table->setClazz($Clazz), $name],$arguments);
    }

    public function setData(array $data): void{
        $this->data = $data;
    }

    public function __get($name){
        return $this->data[$name];
    }

    public function __set($name, $value){
        $this->data[$name] = $value;
    }

    public function __isset($name){
        return isset($this->data[$name]);
    }

    public function save(){
        $table = self::getTableName();
        $data = $this->data;
        if (!empty($data["id"])) Database::instance()->$table->where("id", (int)($data['id']))->update($data);
        else Database::instance()->$table->insert($data);
    }

    public function delete(){
        if (empty($this->data["id"])) return;
        $table = self::getTableName();
        Database::instance()->$table->where("id",$this->data["id"])->delete();
    }

    //один ко многим
    protected function hasMany(string $class, string $link_field, string $key = "id"){
        return call_user_func("{$class}::where", $link_field, $this->data[$key]);
    }

    //многие к одному
    protected function belongsTo(string $class, string $link_field, string $key = "id"){
        return call_user_func("{$class}::where", $key, $this->data[$link_field])->first();
    }

    //многие ко многим
    protected function belongsToMany(string $class, string $middle_table, string $middle_cur_field, string $middle_far_field, $key="id",$far_key="id"){
        return call_user_func("{$class}::join", $middle_table, $middle_far_field, $far_key)
            ->where("{$middle_table}.{$middle_cur_field}", $this->data[$key])
            ->fields([call_user_func("{$class}::getTableName").".*"]);
    }

    public function isEmpty(){
        return empty($this->data);
    }

}