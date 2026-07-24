<?php

namespace sistema\nucleo;
//use Exception;

class Helpers {
    
    public static function redirecionar(?string $url) :void
    {
        header('HTTP/1.1 302 Found');

        $local = ($url ? self::url($url) : self::url());

        header("Location: {$local} ");
        exit();
    }
    
    public static function url(?string $url = null): string
    {
        $servidor = filter_input(INPUT_SERVER,'SERVER_NAME',FILTER_UNSAFE_RAW);

        $ambiente = ($servidor == 'localhost' ? URL_DEV : URL_PROD);

        return $ambiente.$url;
    }
    
    public static function localhost():bool
    {
        $servidor = filter_input(INPUT_SERVER,'SERVER_NAME',FILTER_UNSAFE_RAW);

        if($servidor == 'localhost') {
            return true;
        }
        return false;
    }
}