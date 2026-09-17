<?php

namespace sistema\Nucleo;

class App
{
    public function executar(): void
    {
        TratadorExcecao::registrar();
        Configuracao::inicializar();

        Rotas::registrar();
    }
}
