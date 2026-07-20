<?php

namespace sistema\controlador;

use sistema\modelo\Maquina;

class MaquinaControlador extends SiteControlador
{
   public function maquinas(): void
   {
      echo $this->template->rendenrizar('maquinas.html.twig', 
      ['maquinas' => (new Maquina)->buscarMaq()]);
   }
}
