<?php

namespace sistema\controlador;

use sistema\modelo\Layout;
use sistema\modelo\LayoutMaquina;
use sistema\modelo\Maquina;
use sistema\modelo\Producao;
use sistema\nucleo\Helpers;
use sistema\validacao\LayoutMaquinaValidacao;
use sistema\validacao\LayoutValidacao;

class SiteControlador extends AdminControlador
{
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


    public function montarLayout($id): void
    {
        $dados = filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW);
        if (!empty($dados)) {
            //var_dump((new Maquina)->montarLayout($id, $dados));
            (new LayoutMaquina())->montarLayout($id, $dados);
        }

        echo $this->template->rendenrizar(
            'cadastrolayout.html',
            [
                'maquinas' => (new Maquina())->buscar()->ordenar('model ASC')->resultado(),
                'layouts' => (new Layout())->filtrarLayout($id)
            ]
        );
    }

    public function criarLayout() //falta criar e colocar as veirifcações de Layout e LayoutMaquina
    {
        $dados = filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW) ?? $_POST; // depois de depurar tirar o $_POST
        $dadosLayout = array_filter($dados, fn ($chave) => $chave === 'nome' || $chave === 'descricao', ARRAY_FILTER_USE_KEY);
        $maquinasId = array_diff_key($dados, $dadosLayout);

        if (!empty($dados)) {
            $validacaoLayout = new LayoutValidacao($dadosLayout);
            $validacaoMaquina = new LayoutMaquinaValidacao($maquinasId);
            if ($validacaoLayout->falhou()) {
                $this->mensagem->erro($validacaoLayout->primeiroErro())->flash();
            } elseif ($validacaoMaquina->falhou()) {
                $this->mensagem->erro($validacaoMaquina->primeiroErro())->flash();
            } else {
                try {
                    $layout = new Layout();
                    $layoutMaquina = new LayoutMaquina();
                    $dadosValidados = $validacaoLayout->dados();
                    $maquinasIdValidadas = $validacaoMaquina->dados();

                    $layout->denomination = $dadosValidados['nome'];
                    $layout->observation = $dadosValidados['descricao'];

                    $layoutMaquina->montarLayout($layout, $maquinasIdValidadas);

                    $this->mensagem->sucesso('Layout Cadastrado com Sucesso')->flash();
                    Helpers::redirecionar('layouts');
                    exit();
                } catch (\Throwable $th) {
                    throw $th;
                }
            }
        }

        echo $this->template->rendenrizar(
            'cadastrolayout.html',
            [
                'maquinas' => (new Maquina())->buscar()->ordenar('model ASC')->resultado(),
                'layouts' => (new Layout())->filtrarLayout($id) // não faz mais sentido, é necessário novo form de cadastro
            ]
        );
    }

    public function producao($layout): void
    {
        $dados = filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW);
        if (!empty($dados)) {
            (new Producao())->guardarProducao($layout, $dados);
        }

        echo $this->template->rendenrizar(
            'producao.html',
            [
                'maquinas' => (new LayoutMaquina())->buscarLayoutMachine($layout),
                'DATA_ATUAL' => DATA_ATUAL
            ]
        );
    }

    public function layouts(): void
    {
        $dados = filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW);
        if (!empty($dados)) {
            (new Layout())->cadastrarLayout($dados);
        }

        echo $this->template->rendenrizar(
            'layouts.html',
            [
                'layouts' => (new Layout())->buscarLayout()
            ]
        );
    }

    //Analisar como seria para deletar, pois o layout está vinculado em outras tabelas
    public function deletar($id): void
    {
        //$id = filter_input(INPUT_POST, FILTER_UNSAFE_RAW);

        if (!empty($id)) {
            (new Layout())->deletar($id);
        }

        Helpers::redirecionar('layouts');
    }
}
