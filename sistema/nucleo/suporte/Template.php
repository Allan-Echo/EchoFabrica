<?php

namespace sistema\nucleo\suporte;

use sistema\nucleo\Helpers;
use sistema\nucleo\Configuracao;

class Template
{
    private \Twig\Environment $twig;

    public function __construct(string $diretorio)
    {
        $loader = new \Twig\Loader\FilesystemLoader($diretorio);

        $this->twig = new \Twig\Environment($loader);

        $this->helpers();
        $this->globals();
    }

    public function rendenrizar(string $view, array $dados)
    {
        return $this->twig->render($view, $dados);
    }

    private function helpers(): void
    {
        $this->twig->addFunction(
            new \Twig\TwigFunction('url', function (?string $url = null) {
                return Helpers::url($url);
            })
        );

        $this->twig->addFunction(
            new \Twig\TwigFunction('flash', function () {
                return Helpers::flash();
            })
        );
    }

    private function globals(): void
    {
        $this->twig->addGlobal('URL_DEV', Configuracao::URL_DEV);
        $this->twig->addGlobal('DATA_ATUAL', defined('DATA_ATUAL') ? DATA_ATUAL : '');
    }
}
