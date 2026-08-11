<?php

namespace sistema\controlador;

use sistema\modelo\Maquina;
use sistema\nucleo\Helpers;

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
      // Recebe dados enviados via POST do formulário de cadastro
      $dados = filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW);

      // Só processa se houver dados no POST
      if (!empty($dados)) {
         $erro = false;

         // Valida cada campo recebido, exceto o campo 'valor'
         foreach ($dados as $key => $value) {
            if ($key === 'valor') {
               continue;
            }
            if (trim((string) $value) === '') {
               $erro = true;
               break;
            }
         }

         if ($erro) {
            // Se algum campo obrigatório estiver vazio, exibe mensagem de erro
            $this->mensagem->erro('Campos obrigatórios em branco')->flash();
         } else {
            // Se não houver erro, cadastra a máquina e exibe mensagem de sucesso
            (new Maquina)->cadastrarMaquina($dados);
            $this->mensagem->sucesso('Máquina cadastrada com sucesso')->flash();
            Helpers::redirecionar('maquinas');
            exit();
         }
      }

      // Renderiza o template de cadastro de máquina
      echo $this->template->rendenrizar(
         'cadastromaquina.html',
         []
      );
   }
}
