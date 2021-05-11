<?php
use Illuminate\Support\Carbon;

if (!function_exists('generaCodigo')) {
    function generaCodigo($id)
    {
        //$id = (string)$id;
        $f_actual = Carbon::now();
        $anio = date_format($f_actual,'Y');
        $tam = (int)mb_strlen($id);
        $codigo = '';
        if($tam < 6)
        {
            while($tam < 6)
            {
                $codigo = $codigo.'0';
                $tam ++;
            }
        }
        $codigo = $codigo.$id.'-'.$anio;
        return $codigo;
    }
}






