<?php

namespace sistema\nucleo;

use sistema\nucleo\suporte\EasyPDO;

abstract class Modelo
{
    protected EasyPDO $conection;

    public function __construct()
    {
        $this->conection = new EasyPDO();
    }
}
