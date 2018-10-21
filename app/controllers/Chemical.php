<?php

namespace app\controllers;


use core\base\Controller;
use core\base\View;

class Chemical extends Controller {

    public function action_index() {
        $c = new View("chemical");
        $c->content = "content";
        $c->setTemplate();
        echo $c->render();
    }
}