<?php

namespace sistema\controlador;

use sistema\nucleo\Controlador;

class LoginControlador extends Controlador
{
    public function __construct()
    {
        return parent::__construct('templates/site/views');
    }

    public function login():void
    {
        $dados = filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW);
        if (!empty($dados)) {
            if 
        }
        echo $this->template->rendenrizar('login.html',
        [

        ]);
    }
}
