<?php

namespace sistema\controlador;

use sistema\nucleo\Controlador;
use sistema\nucleo\Helpers;
use sistema\nucleo\Sessao;

class AdminControlador extends Controlador
{
    protected ?object $usuario;

    public function __construct()
    {
        parent::__construct('templates/site/views');

        $this->usuario = UsuarioControlador::usuario();

        if (!$this->usuario) {
            $this->mensagem->alerta('Você precisa estar logado para acessar essa página')->flash();
            $this->acessoNegado();
        } elseif ($this->usuario->level < 3) {
            $this->mensagem->alerta('Você não tem permissão para acessar essa página')->flash();
            $this->acessoNegado();
        }
    }

    private function acessoNegado(): void
    {
        $sessao = new Sessao();
        $sessao->limpar('usuarioId');
        Helpers::redirecionar('login');
    }
}
