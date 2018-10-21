<?php

namespace app\controllers;

use app\models\Category;
use app\models\Lme;
use app\models\Metal;
use app\models\Role;
use app\models\User;
use core\base\Controller;
use core\base\View;
use core\system\Auth;
use app\models\Post;
use core\system\database\Database;
use core\system\database\DatabaseQuery;

class Main extends Controller{

    public function action_index(){

        $v = new View("main");
        $v->posts = Post::limit(10)->desc("id")->all();
        $v->auth = Auth::instance()->isAuth();
        $v->user = Auth::instance()->getCurrentUser(User::class);
        $v->categories = Category::all();
        $v->setTemplate();
        echo $v->render();



//        $user = User::where("login", "?")->first(["sdsdsd"]);
//        User::insert(["name"=>"Oleg", "pass"=>"2345"]);
//        $users = User::where("id", ">", ":minid")->all(["minid"=>1]);
//        User::where("pass", "?")->delete(["2222"]);
//        User::where("name", ":name")->update(["pass"=>"7777"],["name"=>"leondebex"]);
//        Lme::where("date", ":date")->andWhere("metal_id", ":id")->update(["price"=>2500],["date"=>"2018-09-29", "id"=>3]);
//        $users = User::all();
//        $users[0]->dump();
//        echo "<pre>";
//        print_r($users);

//        $metal = Metal::where("name", "?")->first(["zinc"]);
//        $user->name = "Alexandr";
//        $user->pass = "111111111111111111";
//        $user->save();
//        $prices = $metal->prices()->all();
//        echo "<pre>";
//        foreach ($prices as $price) {
//            var_dump($price);
//        }
//        $price = Lme::where("metal_id", 3)->first();
//        var_dump($price->metalName());
//        $db = Database::instance();
//        var_dump(Role::join("user_roles", "roles_id", "id")->fields(["user_roles.*", "roles.name"]));
//        var_dump($db->roles
//            ->join("user_roles", "roles_id", "id")
//            ->fields(["posts.*", "post_name", ["users.name", "author"]]));
//        var_dump($db->roles
//            ->join("sposts", "user_id")
//            ->fields(["posts.*", "post_name", ["users.name", "author"]]));
//            ->where("age",">", ":minAge")
//            ->andWhere("age","<", ":maxAge")
//            ->andWhereGroup(function (DatabaseQuery $t){
//                $t->where("a", ">", ":a");
//                $t->orWhere("b", "<", ":b");
//            })
//            ->asc("name")
//            ->desc("id")
//            ->having("salary", ">", 15000)
//            ->orHaving("age","<", 30)
//            ->limit(50)
//            ->get();
    }
}