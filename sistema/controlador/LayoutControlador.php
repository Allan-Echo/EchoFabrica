<?php

namespace sistema\controlador;

use sistema\modelo\Layout;

class LayoutControlador extends AdminControlador
{
    public function exibirLayouts(): void
    {
        echo $this->template->renderizar(
            'layouts.html.twig',
            [
            'layouts' => (new Layout())->buscar()->ordenar('denomination ASC')->resultado('associativo')
            ]
        );
    }
}
