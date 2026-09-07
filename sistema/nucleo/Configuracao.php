<?php

namespace sistema\Nucleo;

class Configuracao
{
    public const BASE_ROUTE = 'projetofabrica/';
    public const URL_DEV = 'http://localhost/projetofabrica/';
    public const URL_PROD = 'Site sem domínio ainda';

    public static function inicializar(): void
    {
        date_default_timezone_set('America/Sao_Paulo');

        if (!defined('DATA_ATUAL')) {
            define('DATA_ATUAL', date('Y-m-d'));
        }
        if (!defined('DATA')) {
            define('DATA', "'" . date('Y-m-d') . "'");
        }
    }
}
