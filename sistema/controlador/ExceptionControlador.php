<?php

namespace sistema\controlador;

use sistema\nucleo\Sessao;

class ExceptionControlador extends AdminControlador
{
    public function erro500(): void
    {
        /* Aqui é uma mensagem que vai ser renderizada.
        pode só colocar flash no template para usar o modelo de mensagem flash ou usar dessa forma para pegar os dados da sessão e formatar como quiser no template. */
        $sessao = new Sessao();
        echo $this->template->rendenrizar('erro500.twig', [
            'flash' => $sessao->flash()
        ]);
    }

    public function erroDebug(): void
    {
        $sessao = new Sessao();
        echo $this->template->rendenrizar('erroDebug.twig', (array) $sessao->erro_debug);
    }
}
