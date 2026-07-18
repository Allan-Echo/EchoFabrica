<?php

use Pecee\SimpleRouter\SimpleRouter;
use sistema\controlador\SiteControlador;
use sistema\nucleo\Helpers;

try {
    // Analisar onde precisar ser get e post
    SimpleRouter::setDefaultNamespace('sistema\Controlador');
    SimpleRouter::get(BASE_ROUTE, 'SiteControlador@index');

    SimpleRouter::get(BASE_ROUTE . 'sobre', 'SiteControlador@sobre');
    SimpleRouter::get(BASE_ROUTE . 'post/{dado}', 'SiteControlador@post');
    SimpleRouter::get(BASE_ROUTE . 'dashboard/{layout}', 'SiteControlador@dashboard');

    SimpleRouter::group([], function() {
        SimpleRouter::match(['get','post'], BASE_ROUTE. 'produção/{layout}', 'SiteControlador@produção');
    });

    // Rotas para Máquinas
    SimpleRouter::group([], function () {

        SimpleRouter::match(['get', 'post'], BASE_ROUTE . 'maquinas/cadastro', 'SiteControlador@cadastroMaq');
    });

    // Rotas para Layouts
    SimpleRouter::group([], function() {

        //SimpleRouter::match(['get', 'post'], BASE_ROUTE . 'layouts/{layout}/producao', 'SiteControlador@producao');
        
        SimpleRouter::match(['get', 'post'], BASE_ROUTE . 'layouts/montar/{id}', 'SiteControlador@montarLayout');

        SimpleRouter::get(BASE_ROUTE . 'layouts/deletar/{id}', 'SiteControlador@deletar');

        SimpleRouter::match(['get', 'post'], BASE_ROUTE . 'layouts', 'SiteControlador@layouts');
       
    });
    
    // Rota para página de erro
    SimpleRouter::group([], function () {
        SimpleRouter::get(BASE_ROUTE . '404', 'SiteControlador@erro404');
    });

    SimpleRouter::start();
    
} catch (Pecee\SimpleRouter\Exceptions\NotFoundHttpException $e) {

    if (Helpers::localhost()) {
        echo $e;
    } else {
        Helpers::redirecionar('404');
    }
}
