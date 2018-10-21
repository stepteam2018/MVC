<?php

namespace app\controllers;


use app\models\Category;
use core\base\Controller;
use core\base\View;
use core\system\models\User;
use core\system\Request;
use core\system\Url;
use core\system\Auth as ModuleAuth;

class Auth extends Controller {

    public function action_register(){
        if (ModuleAuth::instance()->isAuth()) {
            Url::redirect("/");
            return;
        }
        $v = new View("auth/register");
        $v->auth = ModuleAuth::instance()->isAuth();
        $v->user = ModuleAuth::instance()->getCurrentUser();
        $v->categories = Category::all();
        $v->setTemplate();
        echo $v->render();
    }

    public function action_login(){
        if (ModuleAuth::instance()->isAuth()) {
            Url::redirect("/");
            return;
        }
        $v = new View("auth/login");
        $v->auth = ModuleAuth::instance()->isAuth();
        $v->user = ModuleAuth::instance()->getCurrentUser();
        $v->categories = Category::all();
        $v->setTemplate();
        echo $v->render();
    }

    public function action_signin(){
        try{
            if (!Request::containsPost("login", "pass"))
                throw new \Exception("Заполните все поля");
            if (!ModuleAuth::instance()->login(Request::post("login"),Request::post("pass")))
                throw new \Exception("Неверный логин или пароль");
            Url::redirect("/");
        }catch (\Exception $e){
            $v = new View("auth/error");
            $v->message = "Ошибка при авторизации";
            $v->details = $e->getMessage();
            $v->url = $_SERVER["HTTP_REFERER"];
            $v->categories = Category::all();
            $v->setTemplate();
            echo $v->render();
        }
    }
    public function action_signup(){
        try{
            if (!Request::containsPost("login", "pass", "cpass"))
                throw new \Exception("Заполните все поля");
            if (!Request::containsFile("image"))
                throw new \Exception("Файл не задан");
            if (Request::post("pass") !== Request::post("cpass"))
                throw new \Exception("Пароли не совпадают");
            if (!Request::isAccessFileExtension("image", ['png', 'jpeg', 'gif']))
                throw new \Exception("Формат файла не верен");

            $name = Request::saveIncomingFileWithRandomName("image", "images");
            if ($name === false) throw new \Exception("File load error");

            $user = new User;
            $user->login = Request::post("login");
            $user->pass = Request::post("pass");
            $user->image = $name;
            try{
                $user->save();
            }catch (\Exception $e){
                throw new \Exception("Пользователь с таким логином уже существует");
            }
            Url::redirect("/login");
        }catch (\Exception $e){
            $v = new View("auth/error");
            $v->message = "Ошибка при регистрации";
            $v->details = $e->getMessage();
            $v->url = $_SERVER["HTTP_REFERER"];
            $v->categories = Category::all();
            $v->setTemplate();
            echo $v->render();
        }
    }

    public function action_logout(){
        \core\system\Auth::instance()->logout();
        Url::redirect($_SERVER["HTTP_REFERER"]);
    }

}