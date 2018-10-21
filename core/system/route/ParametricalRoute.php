<?php

namespace core\system\route;


class ParametricalRoute extends Route{

    private $params = [];

    public function __construct(string $rule, string $controller, string $action){

        parent::__construct($rule, $controller, $action);
    }

    const PARAM_REXP = '/^\{\??([a-z0-9]+)\}$/i';
    const REQUIRED_PARAM_REXP = '/^\{[a-z0-9]+\}$/i';

    public function compareRoute(){

        if ($this->filter !== null && !($this->filter)()) return false;
        $rules = explode("/",$this->getClearRule());
        $paths = explode("/", $this->getClearPath());

        if (count($paths) > count($rules)) return false;
        foreach ($rules as $i=>$rule){
            if (!preg_match(self::PARAM_REXP, $rule, $matches)){
                if ($rule != $paths[$i]) return false;
            }else if (isset($paths[$i])) {
                $param_name = $matches[1];
                $this->params[$param_name] = $paths[$i];
            }else if (preg_match(self::REQUIRED_PARAM_REXP, $rule)) return false;
        }
        return true;
    }

    public function getParam($name){
        return @$this->params[$name];
    }

    public function getParams(): array {
        return $this->params;
    }

    public function getBasePath(){
        $rule = $this->getClearRule();
        return $rule = "/".trim(preg_replace('/\{\??([a-z0-9]+)\}/i', "", $rule), "/");
    }

}