<?php

namespace app\controllers;


use app\models\Category;
use app\models\Role;
use app\models\User;
use core\base\Controller;
use core\base\View;
use core\system\database\Database;
use core\system\Request;
use core\system\Url;
use app\models\Post;

class Admin extends Controller{

    public function action_index(){

        $v = new View("admin/index");
        $v->setTemplate("admin");
        echo $v->render();
    }

    public function action_users (){
        $v = new View("admin/users");
        $v->users = User::all();
        $v->roles = Role::all();
        $v->setTemplate("admin");
        echo $v->render();
    }

    public function action_add_role(){
        $user_id = (int) Request::post("userid");
        $role_id = (int) Request::post("roleid");

        try{
            Database::instance()->user_roles->insert([
                "users_id"=>$user_id,
                "roles_id"=>$role_id
            ]);
        }catch (\Exception $e){}

        Url::redirect($_SERVER["HTTP_REFERER"]);
    }

    public function action_del_role(){
        $user_id = (int) Request::post("userid");
        $role_id = (int) Request::post("roleid");

        try{
            Database::instance()->user_roles->where("users_id", $user_id)->andWhere("roles_id", $role_id)->delete();
        }catch (\Exception $e){}

        Url::redirect($_SERVER["HTTP_REFERER"]);
    }

    public function action_editlogin(){
        $user_id = (int) Request::post("userid");
        $login = Request::post("value");
        $u = User::where("id", $user_id)->first();
        $u->login = $login;
        $u->save();
    }

    public function action_posts(){
        $v = new View("admin/posts");
        $v->posts = Post::all();
        $v->categories = Category::all();
        $v->setTemplate("admin");
        echo $v->render();
    }

    public function action_editname(){
        $post_id = (int) Request::post("postid");
        $name = Request::post("postname");
        $p = Post::where("id", $post_id)->first();
        $p->name = $name;
        $p->save();
    }

    public function action_editcategory(){
        $post_id = (int) Request::post("postid");
        $category_id = Request::post("categoryid");
        $p = Post::where("id", $post_id)->first();
        $p->category_id = $category_id;
        $p->save();
    }

    public function action_editcontent(){
        $post_id = (int) Request::post("postid");
        $content = Request::post("content");
        $p = Post::where("id", $post_id)->first();
        $p->content = $content;
        $p->save();
    }
}