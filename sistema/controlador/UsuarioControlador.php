<?php

namespace sistema\controlador;

use sistema\modelo\UsuarioModelo;
use sistema\nucleo\Controlador;
use sistema\nucleo\Sessao;

class UsuarioControlador extends Controlador
{
    public function __construct()
    {
        return parent::__construct('templates/site/views');
    }

    public static function usuario(): ?object
    {
        $sessao = new Sessao();
        if (!$sessao->checar('usuarioId')) {
            return null;
        }
        return (new UsuarioModelo())->buscarPorId($sessao->usuarioId);
    }
}
