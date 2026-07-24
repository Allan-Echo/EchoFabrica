<?php

namespace sistema\controlador;

use sistema\modelo\Maquina;
use sistema\nucleo\Helpers;

class MaquinaControlador extends SiteControlador
{
   public function maquinas(): void
   {
      echo $this->template->rendenrizar(
         'maquinas.html.twig',
         ['maquinas' => (new Maquina)->buscarMaq()]
      );
   }

   public function cadastroMaq(): void
   {
      $dados = filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW);
      if (!empty($dados)) {
         // Para validações posteriormente
         foreach ($dados as $key => $value) {
            if ($value == null) {
               if ($key != 'valor') {
                  die('Campos Obrigatórios em branco');
               } else {
                  (new Maquina)->cadastrarMaquina($dados);
                  Helpers::redirecionar('maquinas');
                  die('Enviado com sucesso');
               }
            }
         }
      }
      echo $this->template->rendenrizar(
         'cadastromaquina.html',
         []
      );
   }
}
