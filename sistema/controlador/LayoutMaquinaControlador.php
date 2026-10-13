<?php

namespace sistema\controlador;

use sistema\modelo\Layout;
use sistema\modelo\LayoutMaquina;
use sistema\modelo\Maquina;
use sistema\nucleo\Helpers;
use sistema\validacao\LayoutMaquinaValidacao;
use sistema\validacao\LayoutValidacao;

class LayoutMaquinaControlador extends AdminControlador
{
    public function criarLayout(): void
    {
        // depois de depurar tirar o $_POST
        $dados = filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW) ?? $_POST;
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

        echo $this->template->renderizar(
            'cadastrolayout.html',
            [
                'maquinas' => (new Maquina())->buscar()->ordenar('model ASC')->resultado('associativo')/* ,
                'layouts' => (new Layout())->filtrarLayout($id) // não faz mais sentido, é necessário novo form de cadastro, pois só vai exitir layout se der certo asociar as máquinas antes */
            ]
        );
    }
}
