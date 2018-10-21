<?php

namespace core\system\route;

use core\system\Url;

class Route{

    protected $rule;
    protected $controller;
    protected $action;
    protected $filter = null;


    public function setFilter(callable $filter){
        $this->filter = $filter;
        return $this;
    }

    public function getController(): string{
        return $this->controller;
    }

    public function getAction(): string{
        return $this->action;
    }

    public function __construct(string $rule, string $controller, string $action){
        $this->rule = $rule;
        $this->controller = $controller;
        $this->action = $action;
    }

    protected function getClearPath(){
        return trim(Url::getPath(), "/");
    }

    protected function getClearRule(){
        return trim($this->rule,"/");
    }
    public function compareRoute(){
        if ($this->filter !== null && !($this->filter)()) return false;
        return $this->getClearRule() === $this->getClearPath();
    }

    public function getBasePath(){
        return "/".$this->getClearRule();
    }
}