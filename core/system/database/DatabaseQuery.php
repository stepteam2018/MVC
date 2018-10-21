<?php

namespace core\system\database;

class DatabaseQuery{

    private $db;
    private $table;
    private $comp;
    private $clazz = NULL;

    public function __construct(Database $database, string $table){
        $this->db = $database;
        $this->table = $table;
        $this->comp = (object)[
            "where"=>[],
            "having"=>[],
            "order"=>[],
            "group"=>NULL,
            "limit"=>NULL,
            "offset"=>NULL,
            "fields"=>NULL,
            "join"=>[]
        ];
    }

    public function setClazz($clazz){
        $this->clazz = $clazz;
        return $this;
    }


    private static function _field(string $field):string {
        $t = "`".str_replace(".", "`.`", $field)."`";
        return str_replace("`*`", "*", $t);
    }

    public function fields(array $field){
        $this->comp->fields = array_map(function ($f){
            return is_array($f) ? self::_field($f[0])." AS ".self::_field($f[1]) : self::_field($f);
            }, $field);
        return $this;
    }

    public function groupBy(array $fields){
        $this->comp->group = array_map(function ($f){return self::_field($f);}, $fields);
        return $this;
    }
    public function asc(string $field = "id"){
        $this->comp->order[] = ["ASC", self::_field($field)];
        return $this;
    }

    public function desc(string $field = "id"){
        $this->comp->order[] = ["DESC", self::_field($field)];
        return $this;
    }

    public function limit (int $limit){
        $this->comp->limit = $limit;
        return $this;
    }

    public function offset (int $offset){
        $this->comp->offset = $offset;
        return $this;
    }

    private static function _where($type, $field, $sign, $value){
        if ($value === NULL){
            $value = $sign;
            $sign = "=";
        }
        return [$type, self::_field($field), $sign, $value];
    }

    public function where($field, $sign, $value = NULL){
        $this->comp->where[] = self::_where("", $field, $sign, $value);
        return $this;
    }

    public function andWhere($field, $sign, $value = NULL){
        $this->comp->where[] = self::_where("AND", $field, $sign, $value);
        return $this;
    }

    public function orWhere($field, $sign, $value = NULL){
        $this->comp->where[] = self::_where("OR", $field, $sign, $value);
        return $this;
    }

    private function _whereGroup(callable $callback, $type){
        if ($type !== NULL) $this->comp->where[] = $type;
        $this->comp->where[] = "(";
        $callback($this);
        $this->comp->where[] = ")";
        return $this;
    }

    public function whereGroup(callable $callback){
        return $this->_whereGroup($callback, NULL);
    }

    public function andWhereGroup(callable $callback){
        return $this->_whereGroup($callback, "AND");
    }

    public function orWhereGroup(callable $callback){
        return $this->_whereGroup($callback, "OR");
    }


    private function _having($type, $field, $sign, $value){
        if ($value === NULL){
            $value = $sign;
            $sign = "=";
        }
        return [$type, $field, $sign, $value];
    }

    public function having($field, $sign, $value = NULL){
        $this->comp->having[] = self::_having("", $field, $sign, $value);
        return $this;
    }

    public function andHaving($field, $sign, $value = NULL){
        $this->comp->having[] = self::_having("AND", $field, $sign, $value);
        return $this;
    }

    public function orHaving($field, $sign, $value = NULL){
        $this->comp->having[] = self::_having("OR", $field, $sign, $value);
        return $this;
    }

    private function _havingGroup(callable $callback, $type){
        if ($type !== NULL) $this->comp->having[] = $type;
        $this->comp->having[] = "(";
        $callback($this);
        $this->comp->having[] = ")";
        return $this;
    }

    public function havingGroup(callable $callback){
        return $this->_havingGroup($callback, NULL);
    }

    public function andHavingGroup(callable $callback){
        return $this->_havingGroup($callback, "AND");
    }

    public function orHavingGroup(callable $callback){
        return $this->_havingGroup($callback, "OR");
    }

    private function _join($far_table, $far_field, $cur_field, $cur_table, string $type = "INNER"){
        $cur_table = $cur_table === NULL ? $this->table : $cur_table;
        $far_field = self::_field($far_table.".".$far_field);
        $cur_field = self::_field($cur_table.".".$cur_field);

        $on = "({$far_field} = {$cur_field})";
        $this->comp->join[] = [$type, self::_field($far_table), $on];
        return $this;
    }

    public function join($far_table, $far_field, $cur_field = "id", $cur_table = NULL){
        return $this->_join($far_table, $far_field, $cur_field, $cur_table);
    }

    public function leftJoin($far_table, $far_field, $cur_field = "id", $cur_table = NULL){
        return $this->_join($far_table, $far_field, $cur_field, $cur_table, "LEFT");
    }

    public function rightJoin($far_table, $far_field, $cur_field = "id", $cur_table = NULL){
        return $this->_join($far_table, $far_field, $cur_field, $cur_table, "RIGHT");
    }

    private function buildWhere($where_word = true){
        if (empty($this->comp->where)) return "";
        $q = $where_word ? " WHERE " : "";
        foreach ($this->comp->where as $w){
            if (!is_array($w)) $q.= " {$w} ";
            else $q.= " {$w[0]} {$w[1]} {$w[2]} {$w[3]}";
        }
        return $q;
    }

    private function buildHaving(){
        if (empty($this->comp->having)) return "";
        $q = " HAVING ";
        foreach ($this->comp->having as $h){
            if (!is_array($h)) $q.= " {$h} ";
            else $q.= " {$h[0]} {$h[1]} {$h[2]} {$h[3]}";
        }
        return $q;
    }

    private function buildOrder(){
        if (empty($this->comp->order)) return "";
        return " ORDER BY " . implode(",", array_map(function ($elem){
                return "{$elem[1]} {$elem[0]}";
            }, $this->comp->order));
    }

    private function buildLimit(){
        return $this->comp->limit !== NULL ? " LIMIT {$this->comp->limit}" : "";
    }

    private function buildOffset(){
        return $this->comp->offset !== NULL ? " OFFSET {$this->comp->offset}" : "";
    }

    private function buildFields(){
        if ($this->comp->fields === NULL) return "*";
        return implode(", ", $this->comp->fields);
    }

    private function buildGroup(){
        if ($this->comp->group === NULL) return "";
        return " GROUP BY " . implode(", ", $this->comp->group);
    }

    private function buildTableReference(){
        $q = "`{$this->table}` ";
        foreach ($this->comp->join as $join){
            $q.= "{$join[0]} JOIN {$join[1]} ON {$join[2]} ";
        }
        return $q;
    }

    private function buildSelect(){
        return "SELECT "
            .$this->buildFields()
            ." FROM "
            .$this->buildTableReference()
            .$this->buildWhere()
            .$this->buildGroup()
            .$this->buildHaving()
            .$this->buildOrder()
            .$this->buildLimit()
            .$this->buildOffset();
    }

    private function buildSelectCount(){
        return "SELECT COUNT(*) FROM "
            .$this->buildTableReference()
            .$this->buildWhere()
            .$this->buildGroup()
            .$this->buildHaving()
            .$this->buildOrder()
            .$this->buildLimit()
            .$this->buildOffset();
    }

    private function buildDelete(){
        return "DELETE FROM "
            .$this->buildTableReference()
            .$this->buildWhere()
            .$this->buildLimit()
            .$this->buildOffset();
    }

    public function update(array $data, array $params = []){
        $this->db->update($this->table, $data, $this->buildWhere(false), $params);
    }

    public function delete(array $params=[]){
        $this->db->rawExec($this->buildDelete(),$params);
    }

    public function insert (array $data){
        $this->db->insert($this->table, $data);
    }

    public function all(array $params = []){
        $result = $this->db->selectAll($this->buildSelect(), $params);
        if ($this->clazz === NULL) return $result;
        if (!$result) return [];
        $res = [];
        foreach ($result as $r) {
            $inst = new $this->clazz;
            $inst->setData($r);
            $res[] = $inst;
        }
        return $res;
    }

    public function first (array $params = []){
        $result = $this->db->selectOne($this->buildSelect(), $params);
        if ($this->clazz === NULL) return $result;
        $inst = new $this->clazz;
        if ($result) $inst->setData($result);
        return $inst;
    }

    public function value(array $params = []){
        return $this->db->selectValue($this->buildSelect(), $params);
    }

    public function count(array $params = []):int {
        return (int)$this->db->selectValue($this->buildSelectCount(), $params);
    }

    public function getPage(int $page, int $count_per_page, array $params = []){
        $offset = ($page-1) * $count_per_page;
        return $this->limit($count_per_page)->offset($offset)->all($params);
    }

    public function getPageCount(int $count_per_page, array $params = []){
        return ceil($this->count($params) / $count_per_page);
    }

    public function dumpSelect(){
        echo $this->buildSelect();
        return $this;
    }
}