<?php
/**
 * Created by PhpStorm.
 * User: broker
 * Date: 07.10.2018
 * Time: 7:01
 */

namespace app\controllers;


use app\models\Category;
use app\models\User;
use core\base\Controller;
use core\base\View;
use core\system\Auth;
use core\system\Request;
use core\system\Router;
use core\system\Url;

class Post extends Controller{

    public function action_add(){
        $v = new View("posts/add");
        $v->auth = Auth::instance()->isAuth();
        $v->user = Auth::instance()->getCurrentUser();
        $v->categories = Category::all();
        $v->setTemplate();
        echo $v->render();
    }

    public function action_add_handle(){

        if (!Auth::instance()->isAuth()){
            Url::redirect("/404");
            return;
        }

        try{
            if (!Request::containsPost("name", "category", "content"))
                throw new \Exception("Заполните все поля");
            if (!Request::containsFile("image"))
                throw new \Exception("Файл не задан");
            if (!Request::isAccessFileExtension("image", ['png', 'jpeg', 'gif']))
                throw new \Exception("Формат файла не верен");

            $name = Request::saveIncomingFileWithRandomName("image", "images");
            if ($name === false) throw new \Exception("File load error");

            $post = new \app\models\Post();
            $post->name = Request::post("name");
            $post->content = Request::post("content");
            $post->category_id = (int)Request::post("category");
            $post->image = $name;
            $post->time = time();

            $user = Auth::instance()->getCurrentUser();
            $post->user_id = $user->id;
            try{
                $post->save();
            }catch (\Exception $e){
                throw new \Exception("При сохранении поста произошла ошибка");
            }
            Url::redirect("/");
        }catch (\Exception $e){
            $v = new View("auth/error");
            $v->message = "Ошибка при добавлении поста";
            $v->details = $e->getMessage();
            $v->url = $_SERVER["HTTP_REFERER"];
            $v->auth = Auth::instance()->isAuth();
            $v->user = Auth::instance()->getCurrentUser();
            $v->categories = Category::all();
            $v->setTemplate();
            echo $v->render();
        }
    }

    public function action_view(){

        try{
            $post = \app\models\Post::where("id", (int)Router::instance()->getActiveRoute()->getParam("id"))->first();
            if ($post->isEmpty()) throw new \Exception();
            $post->time = strftime("%d.%m.%Y", $post->time);
            $v = new View("posts/show");
            $v->auth = Auth::instance()->isAuth();
            $v->user = Auth::instance()->getCurrentUser();
            $v->post = $post;
            $v->categories = Category::all();

            $v->setTemplate();
            echo $v->render();

        }catch (\Exception $e){

            Url::redirect("/404");
            return;

        }
    }

    const POST_PER_PAGE = 1;

    public function action_category(){

        try{
            $cat_id = Router::instance()->getActiveRoute()->getParam("catid");
            $category = Category::where("id", $cat_id)->first();
            if ($category->isEmpty()) throw new \Exception("");

            $page = Router::instance()->getActiveRoute()->getParam("page");
            $page = empty($page) ? 1 : $page;

            $posts = $category->posts()->getPage((int)$page, self::POST_PER_PAGE);
            $count = $category->posts()->getPageCount(self::POST_PER_PAGE);

            $v = new View("posts/cat");
            $v->auth = Auth::instance()->isAuth();
            $v->user = Auth::instance()->getCurrentUser();
            $v->categories = Category::all();
            $v->posts = $posts;
            $v->page = $page;
            $v->pc = $count;
            $v->urlbase = Router::instance()->getActiveRoute()->getBasePath()."/".$cat_id;

            $v->setTemplate();
            echo $v->render();

        }catch (\Exception $e){

            Url::redirect("/404");
            return;

        }
    }
}