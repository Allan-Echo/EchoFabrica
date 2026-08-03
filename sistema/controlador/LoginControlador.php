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
            if (in_array('',$dados, true)) {
                $this->mensagem->alerta('Preencha todos os campos para efetuar o login')->flash();
            }
        }
        echo $this->template->rendenrizar('login.html',
        [

        ]);
    }
}
