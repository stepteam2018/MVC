<?php

namespace app\models;


use core\base\Model;

class User extends Model {
//    public static $table = "ggg";
    public function roles(){
        return $this->belongsToMany(Role::class, "user_roles", "users_id", "roles_id");
    }

    private $roles = NULL;

    public function hasRole(string $role){
        if ($this->roles == NULL) $this->roles = $this->roles()->all();
        foreach ($this->roles as $r) if ($r->name == $role) return true;
        return false;
    }
}