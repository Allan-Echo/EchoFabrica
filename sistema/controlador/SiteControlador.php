<?php

namespace sistema\controlador;

use sistema\modelo\Maquina;
use sistema\modelo\Layout;
use sistema\modelo\LayoutMaquina;
use sistema\modelo\Producao;
use sistema\nucleo\Controlador;
use sistema\nucleo\Helpers;

class SiteControlador extends Controlador
{
    public function __construct()
    {
        parent::__construct('templates/site/views');
    }

    public function erro404(): void
    {
        echo $this->template->rendenrizar('404.html', []);
    }

    public function index(): void
    {
        echo $this->template->rendenrizar('index.html', []);
    }

    public function sobre(): void
    {
        echo $this->template->rendenrizar(
            'sobre.html',
            [
                'dados' => (new Maquina())->buscarMaq()
            ]
        );
    }

    // public function post($dado = null) :void
    // {
    //      $modelo = (new MaquinaModelo())->filtrar($dado);

    //      if(!$modelo) {
    //          Helpers::redirecionar('404');
    //      }
    //     echo $this->template->rendenrizar('post.html',
    //     [
    //         'modelo' => (new MaquinaModelo())->filtrar($dado)
    //     ]);
    // }

    // public function dashboard(string $layout): void
    // {
    //      var_dump((new MaquinaModelo)->buscarProducao($layout));
    //     echo $this->template->rendenrizar('dashboard.html',
    //     [
    //         'producao' => (new MaquinaModelo)->buscarProducao($layout)
    //     ]);
    // }

    public function cadastroMaq(): void
    {
        $dados = filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW);
        if (!empty($dados)) {

            (new Maquina)->cadastrarMaquina($dados);
            // Para validações posteriormente
            // foreach ($dados as $key => $value) 
            //{
            //     if ($value == null) 
            //     {
            //         if ($key != 'operacoes' && $key != 'valor') {
            //             die('Campos Obrigatórios em branco');
            //         } else {die ('enviado com sucesso');}
            //     } 
            // }
        }
        echo $this->template->rendenrizar(
            'cadastromaquina.html',
            []
        );
    }

    public function montarLayout($id): void
    {
        $dados = filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW);
        if (!empty($dados)) {
            //var_dump((new Maquina)->montarLayout($id, $dados));
            (new LayoutMaquina)->montarLayout($id, $dados);
        }

        echo $this->template->rendenrizar(
            'cadastrolayout.html',
            [
                'maquinas' => (new Maquina)->buscarMaq(),
                'layouts' => (new Layout)->filtrarLayout($id)
            ]
        );
    }

    public function produção($layout): void
    {
        $dados = filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW);
        if (!empty($dados)) {
            (new Producao)->guardarProducao($layout, $dados);
        }

        echo $this->template->rendenrizar(
            'producao.html',
            [
                'maquinas' => (new LayoutMaquina)->buscarLayout_Machine($layout),
                'DATA_ATUAL' => DATA_ATUAL
            ]
        );
    }

    public function layouts(): void
    {
        $dados = filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW);
        if (!empty($dados)) {
            (new Layout)->cadastrarLayout($dados);
        }

        echo $this->template->rendenrizar(
            'layouts.html',
            [
                'layouts' => (new Layout)->buscarLayout(),
                'URL_DEV' => URL_DEV
            ]
        );
    }

    //Analisar como seria para deletar, pois o layout está vinculado em outras tabelas
    public function deletar($id): void
    {
        //$id = filter_input(INPUT_POST, FILTER_UNSAFE_RAW);

        if (!empty($id)) {
            (new Layout)->deletar($id);
        }

        Helpers::redirecionar('layouts');
    }
}
