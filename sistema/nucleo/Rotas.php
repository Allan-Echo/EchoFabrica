<?php

namespace sistema\Nucleo;

use Pecee\SimpleRouter\Exceptions\NotFoundHttpException;
use Pecee\SimpleRouter\SimpleRouter;
use sistema\nucleo\Helpers;
use sistema\nucleo\Configuracao;

class Rotas
{
    public static function registrar(): void
    {
        try {
            SimpleRouter::setDefaultNamespace('sistema\controlador');

            // 1. Rotas de Autenticação (Mantendo a compatibilidade com seu LoginControlador e DashboardControlador)
            SimpleRouter::match(['get', 'post'], Configuracao::BASE_ROUTE . 'login', 'LoginControlador@login');
            SimpleRouter::get(Configuracao::BASE_ROUTE . 'logout', 'DashboardControlador@logout');

            // 2. Rotas do Site / Públicas
            SimpleRouter::get(Configuracao::BASE_ROUTE, 'SiteControlador@index');
            SimpleRouter::get(Configuracao::BASE_ROUTE . 'sobre', 'SiteControlador@sobre');
            SimpleRouter::get(Configuracao::BASE_ROUTE . 'post/{dado}', 'SiteControlador@post');
            SimpleRouter::get(Configuracao::BASE_ROUTE . 'dashboard/{layout}', 'SiteControlador@dashboard');

            // 3. Rotas Protegidas / Internas
            SimpleRouter::match(['get', 'post'], Configuracao::BASE_ROUTE . 'producao/{layout}', 'SiteControlador@producao');
            SimpleRouter::get(Configuracao::BASE_ROUTE . 'maquinas', 'MaquinaControlador@maquinas');
            SimpleRouter::match(['get', 'post'], Configuracao::BASE_ROUTE . 'maquinas/cadastro', 'MaquinaControlador@cadastroMaq');

            // 4. Rotas para Layouts
            SimpleRouter::match(['get', 'post'], Configuracao::BASE_ROUTE . 'layouts/montar/{id}', 'SiteControlador@montarLayout');
            SimpleRouter::get(Configuracao::BASE_ROUTE . 'layouts/deletar/{id}', 'SiteControlador@deletar');
            SimpleRouter::match(['get', 'post'], Configuracao::BASE_ROUTE . 'layouts', 'SiteControlador@layouts');

            // 5. Rota de Erro
            SimpleRouter::get(Configuracao::BASE_ROUTE . '404', 'SiteControlador@erro404');

            SimpleRouter::start();
        } catch (NotFoundHttpException $e) {
            if (Helpers::localhost()) {
                echo $e;
            } else {
                Helpers::redirecionar('404');
            }
        }
    }
}
