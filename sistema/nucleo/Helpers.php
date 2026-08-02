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

    //A função instacia o objeto sessão e faz uma verificação ao mesmo tempo que já guarda o resultado na variável flash. A verificação chama o método flahs() da class Sessão, a qual pode retornar o Objeto Mesagem 'clonado' ou pode retornar null. Se retonar o método flash da classe Sessão retornar um objeto(o clonado), então essa função vai retornar o Objeto que foi instanciado no controlador, criado pela Classe Mensagem, Clonado pela Classe Sessão com todos seus atributos do "Molde da classe Mensagem", inclusive o __toString(). Por isso, é possível dar um "echo" no objeto sabendo que ele vai "se comportar como um string" como todas as tags html e .css. Porém, se a classe Senssão retornar null, então if vai dar false e o código prossegue para o retorno de null, o que sinifica que não vai ser renderizado nenhuma mensagem na view por meio do twig. Agora vamos ver como funciona a clonagem do objeto na classe Sessão.
    public static function flash(): ?string
    {
        $sessão = new Sessao;
        if($flash = $sessão->flash()) {
            echo $flash;
        }
        return null;
    }
}