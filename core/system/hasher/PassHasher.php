<?php
/**
 * Created by PhpStorm.
 * User: broker
 * Date: 30.09.2018
 * Time: 18:22
 */

namespace core\system\hasher;


use app\configuration\PassHashConfiguration;

class PassHasher{

    private static $inst = NULL;
    private $_pos, $_len, $_alg;

    public static function instance(){
        return self::$inst === NULL ? self::$inst = new self() : self::$inst;
    }

    private function __construct(){
        $this->_pos = PassHashConfiguration::SALT_POS;
        $this->_len = PassHashConfiguration::SALT_LEN;
        $this->_alg = PassHashConfiguration::ALGORITHM;
    }

    private function generateSalt(){
        $h1 = hash("sha256", time());
        $h2 = hash("sha256", rand(0, PHP_INT_MAX));
        $h3 = hash("sha256", $_SERVER["REMOTE_ADDR"]);
        $hdata = $h1.$h2.$h3.$h2.$h1;
        $base_salt = hash("sha256", $hdata);
        return substr($base_salt,0, $this->_len);

    }
    private function _hash(string $data, string $salt){
        $d = hash($this->_alg, $data);
        $s = hash($this->_alg, $salt);
        $hd = $d.$s.md5($d).$s;
        $h = hash($this->_alg, $hd);
        return substr_replace($h, $salt, $this->_pos, $this->_len);
    }

    public function hash(string $data){
        return $this->_hash($data, $this->generateSalt());
    }

    public function validateHash(string $data, string $hash):bool {
        $salt = substr($hash, $this->_pos, $this->_len);
        $hash2 = $this->_hash($data, $salt);
        return $hash === $hash2;
    }
}