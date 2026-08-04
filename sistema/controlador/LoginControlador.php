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
            if ($this->checarDados($dados)) {
                $this->mensagem->sucesso('Login realizado com sucesso')->flash();
            }
        }
        echo $this->template->rendenrizar('login.html',
        [

        ]);
    }

    private function checarDados(array $dados): bool
    {
        if (in_array('',$dados, true)) {
            $this->mensagem->alerta('Todos os campos são obrigatórios')->flash();
            return false;
        }
        return true;
    }
}
