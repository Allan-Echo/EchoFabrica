<?php

namespace sistema\nucleo;

use sistema\nucleo\suporte\EasyPDO;

abstract class Modelo
{
    protected EasyPDO $conection;
    protected Mensagem $mensagem;

    public function __construct()
    {
        $this->conection = new EasyPDO();
        $this->mensagem = new Mensagem();
    }
}
