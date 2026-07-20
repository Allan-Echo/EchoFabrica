<?php

namespace sistema\nucleo;
use sistema\nucleo\suporte\Template;

abstract class Controlador
{
    protected Template $template;

    public function __construct(string $diretorio) {
        $this->template = new Template($diretorio);
    }
}