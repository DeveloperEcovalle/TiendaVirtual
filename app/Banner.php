<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model {

    protected $table = 'banners';
    protected $primaryKey = 'id';
    public $timestamps = false;

}
