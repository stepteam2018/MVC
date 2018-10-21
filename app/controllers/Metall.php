<?php

namespace app\controllers;


use app\models\Lme;
use app\models\Metal;
use core\base\Controller;
use core\base\View;
use core\system\Request;
use core\system\Url;

class Metall extends Controller{

    public $date;

    public function action_index(){
        $m = new View("metals");
        $date = Request::get("date");
        $m->header = "LME";
        $m->count = Lme::where("date", "?")->count([$date]);
        $m->cur_date = $date;
        $m->lmes = Lme::where("date", "?")->join("metals", "id", "metal_id", "lme")->asc()->all([$date]);
        $m->metals = Metal::asc()->all();

        $m->setTemplate("metal");
        echo $m->render();
    }

    public function action_lmeSave(){
        $date = Request::get("date");
        for ($i = 1; $i <= 6; $i++) {
            Lme::insert(["metal_id"=>$i, "date"=>$date, "price"=>(double)Request::get("{$i}")]);
        }
        Url::redirect("/metall?date=".$date);
    }

    public function action_refresh(){
        $date = Request::get("date");
        Lme::where("date","=",$date)->all();
        Url::redirect("/metall?date=".$date);
    }

    public function action_lmeUpdate(){
        $date = Request::get("date");
        for ($i = 1; $i <= 6; $i++) {
            Lme::where("date", ":date")->andWhere("metal_id", ":id")->update(["price"=>(double)Request::get("{$i}")],["date"=>$date, "id"=>$i]);
        }
        Url::redirect("/metall?date=".$date);
    }

    public function action_lmeDelete(){
        $date = Request::get("date");
        Lme::where("date", "?")->delete([$date]);
        Url::redirect("/metall?date=".$date);
    }
}