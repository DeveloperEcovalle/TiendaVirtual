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

if (!function_exists('enviapedido')) {
    function enviapedido($venta, $telefono)
    {
        try{
            $file = file_get_contents('storage/pedidos/'.$venta->codigo.'.pdf');
            $data_file = base64_encode($file);
            $mime = mime_content_type('storage/pedidos/'.$venta->codigo.'.pdf');
            $str_file = 'data:'.$mime.';base64,'.$data_file;
            
            $data = [
                'phone' => '51'.$telefono, // Receivers phone
                'body' => $str_file,
                'filename' => $venta->codigo.'.pdf',
                'caption' => (string)"PEDIDO COD: ".$venta->codigo, // Message// Message
            ];

            $json = json_encode($data); // Encode data to JSON
            // URL for request POST /message
            $token = 'n34tqely1k2fiwzi';
            $instanceId = '242825';
            $url = 'https://api.chat-api.com/instance'.$instanceId.'/sendFile?token='.$token;
            // Make a POST request
            $options = stream_context_create(['http' => [
                    'method'  => 'POST',
                    'header'  => 'Content-type: application/json',
                    'content' => $json
                ]
            ]);
            // Send a request
            $result = file_get_contents($url, false, $options);
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
}






