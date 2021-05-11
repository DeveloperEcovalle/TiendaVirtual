<?php

namespace App\Http\Controllers\Intranet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PanelControl extends Controller {

    public function index() {
        return view('intranet.panel_control');
    }
}
