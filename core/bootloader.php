<?php

use app\configuration\RouteConfigurator;
use core\system\exceptions\RouterException;
use core\system\Router;

define("DOCROOT", __DIR__."/../");
define("VIEW_PATH", DOCROOT."app/views/");
define("TEMPLATE_PATH", DOCROOT."app/templates/");
define("APP_PATH", DOCROOT."app/");

require_once DOCROOT."vendor/autoload.php";

spl_autoload_register(function ($name){
    $path = DOCROOT.str_replace("\\", "/", $name).".php";
    if (file_exists($path)) require_once $path;
});

RouteConfigurator::routerConfigure();

try{
    Router::instance()->navigate();
}catch (RouterException $e){
    RouteConfigurator::onRouterError($e);
}
