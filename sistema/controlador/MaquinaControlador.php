<?php

namespace sistema\controlador;

use sistema\modelo\Maquina;
use sistema\nucleo\Helpers;
use sistema\validacao\MaquinaValidacao;

class MaquinaControlador extends AdminControlador
{
    public function maquinas(): void
    {
        $maquina = new Maquina();

        echo $this->template->rendenrizar(
            'maquinas.html.twig',
            ['maquinas' => $maquina->buscar()->ordenar('model ASC')->resultado()]
        );
    }

    public function cadastroMaquina(): void
    {

        // Recebe dados enviados via POST do formulário de cadastro
        $dados = filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW) ?? $_POST;

        // Só processa se houver dados enviados via POST
        if (!empty($dados)) {
            $validacao = new MaquinaValidacao($dados);

            if ($validacao->falhou()) {
                $this->mensagem->erro($validacao->primeiroErro())->flash();
            } else {

                //try {
                $maquina = new Maquina();

                $dadosValidados = $validacao->dados();
                // Mapeamento correto dos dados
                $maquina->model            = $dadosValidados['modelo'];
                $maquina->brand            = $dadosValidados['marca'];
                $maquina->designation      = $dadosValidados['funcao'];
                $maquina->piece_operations = $dadosValidados['operacoes'];
                $maquina->quantity         = $dadosValidados['qtd'];
                $maquina->purchase_price   = $dadosValidados['valor'] ?? null;
                // Salva no banco
                $maquina->salvar();

                // SÓ EXIBE SUCESSO E REDIRECIONA SE REALMENTE SALVOU
                $this->mensagem->sucesso('Máquina cadastrada com sucesso')->flash();
                Helpers::redirecionar('maquinas');
                exit();
                /*  } catch (\Throwable $th) {
                     error_log((string) $th);
                     $this->mensagem
                     ->erro('Não foi possível cadastrar a máquina. Tente novamente.')
                     ->flash();
                     return;
                 } */
            }
        }

        // Renderiza o template de cadastro (se for GET ou se a validação falhou)
        echo $this->template->rendenrizar(
            'cadastromaquina.html',
            []
        );
    }
}
