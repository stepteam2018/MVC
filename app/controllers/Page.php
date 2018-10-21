<?php

namespace app\controllers;


use core\base\Controller;
use core\base\View;

class Page extends Controller{

    public function action_index(){
        $p = new View("page");
        $p->header = "Page 1";
        $p->users = ["name"=>"vasya", "surname"=>"petrov"];
        $p->setTemplate();
        echo $p->render();
    }
}