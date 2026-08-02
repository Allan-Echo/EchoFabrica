<?php

namespace sistema\controlador;

use sistema\nucleo\Controlador;

class AdminControlador extends Controlador
{
    public function __construct()
    {
        return parent::__construct('templates/site/views');
    }
}
