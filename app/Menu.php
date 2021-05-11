<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model {

    protected $table = 'menus';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function modulo() {
        return $this->belongsTo(Menu::class, 'menu_id', 'id');
    }

    public function submenus() {
        return $this->hasMany(Menu::class, 'menu_id', 'id');
    }

    public function permisos() {
        return $this->hasMany(Permiso::class, 'menu_id', 'id');
    }

}
