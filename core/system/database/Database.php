<?php

namespace core\system\database;


use app\configuration\DatabaseConfigurator;

class Database{

    private $dbh;
    private static $inst=[];

    private function __construct($config){
        $connection_str = "mysql:dbname={$config->dbname};host={$config->host};port={$config->port};charset={$config->charset}";
        $this->dbh = new \PDO($connection_str, $config->user, $config->pass,[
            \PDO::ATTR_ERRMODE=>\PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE=>\PDO::FETCH_ASSOC
        ]);
    }

    public static function instance($configname = DatabaseConfigurator::DEFAULT_CONFIGURATION):Database{
        if (!empty(self::$inst[$configname])) return self::$inst[$configname];
        $config = DatabaseConfigurator::getConfiguration($configname);
        self::$inst[$configname] = new self($config);
        return self::$inst[$configname];
    }

    public function insert(string $table, array $data){
        $fields = array_keys($data);
        $query = "INSERT INTO `{$table}` (`"
            .implode("`,`", $fields)."`) VALUES (:"
            .implode(",:", $fields).")";
        $this->dbh->prepare($query)->execute($data);
        return $this->dbh->lastInsertId();
    }

    public function selectAll(string $query, array $params=[]){
        $stmt = $this->dbh->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function selectOne(string $query, array $params=[]){
        $stmt = $this->dbh->prepare($query);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    public function selectColumn(string $query, array $params=[]){
        $stmt = $this->dbh->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function selectValue(string $query, array $params=[]){
        $stmt = $this->dbh->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    public function update (string $table, array $values, string $where, array $params){
        $fields = array_keys($values);
        $params_names = array_map(function ($elem){return "bd2017_fw_".$elem;},$fields);
        $_values = array_values($values);
        $_values = array_combine($params_names, $_values);
        $subarr = array_map(function ($f,$p){return "`{$f}`=:{$p}";}, $fields, $params_names);

        $q = "UPDATE `{$table}` SET ".implode(", ", $subarr);
        if (!empty($where)) $q.= " WHERE ".$where;
        $stmt = $this->dbh->prepare($q);
        $stmt->execute(array_merge($_values, $params));
    }

    public function delete(string $table, string $where, array $params=[]){
        $q = "DELETE FROM `{$table}` WHERE ".$where;
        $this->dbh->prepare($q)->execute($params);
    }

    public function rawExec (string $query, array $params=[]){
        $this->dbh->prepare($query)->execute($params);
    }

    public function __get($name){
        return new DatabaseQuery($this, $name);
    }

    public function quote($v){
        return $this->dbh->quote($v);
    }
}