<?php

namespace sistema\controlador;

use sistema\modelo\Maquina;
use sistema\nucleo\Helpers;
use sistema\nucleo\Erro;

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

   public function cadastroMaq(): void
   {
      $maquina = new Maquina;

      // Recebe dados enviados via POST do formulário de cadastro
      $dados = filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW);

      // Só processa se houver dados enviados via POST
      if (!empty($dados)) {
         $erro = new Erro();

         // Lista de campos que são ESTRITAMENTE OBRIGATÓRIOS
         $obrigatorios = ['modelo', 'marca', 'funcao', 'operacoes', 'qtd'];
         $camposInteiros = ['qtd', 'valor', 'operacoes'];
         $opcoes = array('options' => array('min_range' => 1));

         foreach ($dados as $chave => $campo) {
            if (!isset(array_key_exists) || trim((string) $dados[$campo]) === '') {
               $erro->definir('Campos obrigatórios em branco');
               break;
            }
            elseif (strlen(trim($dados[$campo])) > 255) {
               $erro->definir('Campos obrigatórios com mais de 255 caracteres');
               break;  
            }   
         }
         

         foreach ($camposInteiros as $campo) {
            if (isset($dados[$campo])) {
               $campoFiltrado = filter_var($dados[$campo], FILTER_VALIDATE_INT, $opcoes);

               if ($campoFiltrado === false) {
                  $erro->definir('Campos numéricos inválidos');
                  break;
               } else {
                  $dados[$campo] = $campoFiltrado;
               }
            }
         }

         if ($erro->temErro()) {
            // Exibe o erro e NÃO redireciona (para permitir que o usuário corrija no formulário)
            $this->mensagem->erro($erro->obter())->flash();
         } else {
            // Mapeamento correto dos dados
            $maquina->model            = $dados['modelo'];
            $maquina->brand            = $dados['marca'];
            $maquina->designation      = $dados['funcao'];
            $maquina->piece_operations = $dados['operacoes'];
            $maquina->quantity         = $dados['qtd'];

            // Trata o campo 'valor' (se vier vazio '', grava NULL ou 0 no banco)
            $valorFormatado = trim((string)($dados['valor'] ?? ''));
            $maquina->purchase_price = $valorFormatado !== '' ? $valorFormatado : null;

            // Salva no banco
            $maquina->salvar();

            // SÓ EXIBE SUCESSO E REDIRECIONA SE REALMENTE SALVOU
            $this->mensagem->sucesso('Máquina cadastrada com sucesso')->flash();
            Helpers::redirecionar('maquinas');
            exit();
         }
      }

      // Renderiza o template de cadastro (se for GET ou se a validação falhou)
      echo $this->template->rendenrizar(
         'cadastromaquina.html',
         []
      );
   }
}
