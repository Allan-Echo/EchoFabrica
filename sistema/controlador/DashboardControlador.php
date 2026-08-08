<?php

namespace sistema\controlador;
use sistema\nucleo\Helpers;
use sistema\nucleo\Sessao;

class DashboardControlador extends AdminControlador
{
    public function dashboard(): void
    {
        echo $this->template->rendenrizar(
            'dashboard.html.twig',
            []
        );
    }

    public function logout(): void
    {
        $sessao = new Sessao;
        $sessao->limpar('usuarioId');

        $this->mensagem->informa('Logout realizado com sucesso')->flash();
        
        Helpers::redirecionar('login');
        //exit();
    }
         
}
