<?php

namespace core\system;


use core\system\hasher\PassHasher;
use core\system\models\User;

//use core\system\models\User;

class Auth{

    private static $inst = NULL;
    private $user = NULL;

    private function __construct(){}

    public static function instance(){
        return self::$inst === NULL ? self::$inst = new self() : self::$inst;
    }

    public function login(string $login, string $pass, bool $save = false){
        $user = User::where("login", "?")->first([$login]);
        if ($user->isEmpty()) return false;
        if (!PassHasher::instance()->validateHash($pass, $user->pass)) return false;
        Session::instance()->createUserSession($user->id, $save);
        return true;
    }

    public function logout(bool $deep = false) {
        Session::instance()->destroy($deep);
    }

    public function isAuth(){
        return Session::instance()->validateSession();
    }

    public function getCurrentUser($clazz = User::class){
        if (!$this->isAuth()) return NULL;
        if ($this->user === NULL) $this->user = call_user_func("{$clazz}::where", "id", Session::instance()->getUserID())->first();
        return $this->user;
    }
}