<?php

namespace core\system;


use app\configuration\SessionConfiguration;
use core\system\models\Session as ModelSession;

class Session{

    private static $inst = NULL;
    private $session = NULL;

    private function __construct(){}

    public static function instance(){
        return self::$inst === NULL ? self::$inst = new self() : self::$inst;
    }

    private static function getIp(){
        return md5($_SERVER["REMOTE_ADDR"]);
    }

    private static function getAgent(){
        return md5($_SERVER["HTTP_USER_AGENT"]);
    }

    private static function getToken(){
        $data = md5(self::getIp().self::getAgent().time());
        return $data; //усложнить
    }

    public function createUserSession(int $id, $long = false){
        $time = $long ? 0 : SessionConfiguration::SESSION_TIME;
        $session = new ModelSession();
        $session->user_ip = self::getIp();
        $session->user_agent = self::getAgent();
        $session->token = self::getToken();
        $session->user_id = $id;
        $session->expires = $long ? 0 : time() + $time;
        $session->created = time();
        $session->save();

        $ctime = $long ? 3600*24*365 : time() + $time;
        setcookie(SessionConfiguration::COOKIE_KEY, $session->token, $ctime, "/");
    }

    public function validateSession(): bool {
        if ($this->session !== NULL) return true;
        if (empty($_COOKIE[SessionConfiguration::COOKIE_KEY])) return false;
        $session = ModelSession::where("token", ":token")->first(["token"=>$_COOKIE[SessionConfiguration::COOKIE_KEY]]);
        if ($session->isEmpty()) return false;
        if ($session->user_ip != self::getIp()) return false;
        if ($session->user_agent != self::getAgent()) return false;
        $this->continueSession();
        $this->session = $session;
        return true;
    }

    public function getUserID():int {
        if (!$this->validateSession()) throw new \Exception("Session invalid");
        return (int) $this->session->user_id;
    }

    public function destroy(bool $deep = false){
        if (!$this->validateSession()) return;
        if ($deep) ModelSession::where("user_id", $this->session->user_id)->delete();
        else $this->session->delete();
        setcookie(SessionConfiguration::COOKIE_KEY,"",time()-1,"/");
    }

    private function continueSession(){
        //TODO:
    }
}