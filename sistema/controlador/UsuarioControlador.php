<?php

namespace sistema\controlador;

use sistema\nucleo\Controlador;
use sistema\nucleo\Sessao;
use sistema\modelo\UsuarioModelo;

class UsuarioControlador extends Controlador
{
    public function __construct()
    {
        return parent::__construct('templates/site/views');
    }

    public static function usuario(): ?object {
        $sessao = new Sessao();
        if(!$sessao->checar('usuarioId')) {
            return null;
        }
        return (new UsuarioModelo())->buscarPorId($sessao->usuarioId);
    }
}
