<?php

namespace sistema\nucleo;
use sistema\nucleo\suporte\Template;
use sistema\nucleo\Mensagem;

abstract class Controlador
{
    protected Template $template;
    protected Mensagem $mensagem;

    public function __construct(string $diretorio) {
        $this->template = new Template($diretorio);
        $this->mensagem = new Mensagem();
    }
}