<?php

namespace core\system;

use core\system\exceptions;
use core\system\exceptions\RouterException;
use core\system\route\Route;

class Router{

    private static $inst;
    private $activeRoute = NULL;

    public static function instance(){
        return self::$inst !== null ? self::$inst : self::$inst = new self();
    }

    private function __construct(){}

    private $routes=[];

    public function addRoute(Route $route){
        $this->routes[] = $route;
    }

    public function navigate(){
        foreach ($this->routes as $route){
            if ($route->compareRoute()){
                $this->activeRoute = $route;
                $this->navigateTo($route->getController(),$route->getAction());
                return;
            }
        }
        throw new RouterException("route not found");
    }

    public function navigateTo($controller, $action){
        $ctrl_class_name = "\\app\\controllers\\".ucfirst(strtolower($controller));
        $action_name = "action_".strtolower($action);

        if (!file_exists(rtrim(DOCROOT,"/").str_replace("\\","/",$ctrl_class_name).".php")) throw new RouterException("controller not found");

        $ctrl = new $ctrl_class_name;
        if (!method_exists($ctrl,$action_name)) throw new RouterException("action not found");

        $ctrl->$action_name();
    }

    public function getActiveRoute():Route{
        return $this->activeRoute;
    }


}