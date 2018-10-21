<?php

namespace app\configuration;

use app\models\Role;
use app\models\User;
use core\system\Auth;
use core\system\exceptions\RouterException;
use core\system\route\ParametricalRoute;
use core\system\route\Route;
use core\system\Router;
use core\system\Url;

class RouteConfigurator{
    public static function routerConfigure(){

        $admin_filter = function (){
            return Auth::instance()->isAuth() && Auth::instance()->getCurrentUser(User::class)->hasRole("admin");
        };

        Router::instance()->addRoute(new Route("", "main", "index"));

        //auth
        Router::instance()->addRoute(new Route("register", "auth", "register"));
        Router::instance()->addRoute(new Route("login", "auth", "login"));
        Router::instance()->addRoute(new Route("signup", "auth", "signup"));
        Router::instance()->addRoute(new Route("signin", "auth", "signin"));
        Router::instance()->addRoute(new Route("logout", "auth", "logout"));

        //post
        Router::instance()->addRoute(new Route("posts/add", "post", "add"));
        Router::instance()->addRoute(new Route("posts/add/handle", "post", "add_handle"));
        Router::instance()->addRoute(new ParametricalRoute("posts/{id}", "post", "view"));
        Router::instance()->addRoute(new ParametricalRoute("posts/category/{catid}/{?page}", "post", "category"));


        //admin
        Router::instance()->addRoute((new Route("admin", "admin", "index"))->setFilter($admin_filter));
        Router::instance()->addRoute((new Route("admin/users", "admin", "users"))->setFilter($admin_filter));
        Router::instance()->addRoute((new Route("admin/users/add_role", "admin", "add_role"))->setFilter($admin_filter));
        Router::instance()->addRoute((new Route("admin/users/delete_role", "admin", "del_role"))->setFilter($admin_filter));
        Router::instance()->addRoute((new Route("admin/users/editlogin", "admin", "editlogin"))->setFilter($admin_filter));
        Router::instance()->addRoute((new Route("admin/posts", "admin", "posts"))->setFilter($admin_filter));
        Router::instance()->addRoute((new Route("admin/posts/editname", "admin", "editname"))->setFilter($admin_filter));
        Router::instance()->addRoute((new Route("admin/posts/editcategory", "admin", "editcategory"))->setFilter($admin_filter));
        Router::instance()->addRoute((new Route("admin/posts/editcontent", "admin", "editcontent"))->setFilter($admin_filter));


        Router::instance()->addRoute(new Route("404", "c404", "index"));
    }

    public static function onRouterError(RouterException $e){
        Url::redirect("/404");
    }
}