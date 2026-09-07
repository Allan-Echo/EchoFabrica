<?php

namespace sistema\Nucleo;

class App
{
    public function executar(): void
    {
        Configuracao::inicializar();

        Rotas::registrar();
    }
}
