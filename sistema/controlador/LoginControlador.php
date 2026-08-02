<?php

namespace sistema\controlador;

use sistema\nucleo\Controlador;

class LoginControlador extends Controlador
{
    public function __construct()
    {
        return parent::__construct('templates/site/views');
    }

    public function Login():void
    {
        echo $this->template->rendenrizar('login.html',
        [

        ]);
    }
}
