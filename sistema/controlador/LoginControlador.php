<?php

namespace sistema\controlador;

use sistema\nucleo\Controlador;
use sistema\modelo\UsuarioModelo;
use sistema\nucleo\Helpers;

class LoginControlador extends Controlador
{
    public function __construct()
    {
        return parent::__construct('templates/site/views');
    }

    private function checarDados(array $dados): bool
    {
        if (in_array('',$dados, true)) {
            $this->mensagem->alerta('Todos os campos são obrigatórios')->flash();
            return false;
        }
        return true;
    }
    
    public function login():void
    {
        $dados = filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW);
        if (!empty($dados)) {
            if ($this->checarDados($dados)) {
                $usuario = (new UsuarioModelo())->login($dados, 3);
                if ($usuario) {
                    Helpers::redirecionar('login');
                    exit;
                }

            }
        }
        echo $this->template->rendenrizar('login.html',
        [

        ]);
    }
}
