<?php

namespace sistema\nucleo\suporte;

use sistema\nucleo\Helpers;
use Twig\TwigFunction;
use Twig\Lexer;

class Template
{
    private \Twig\Environment $twig;

    public function __construct(string $diretorio)
    {
        $loader = new \Twig\Loader\FilesystemLoader($diretorio);

        $this->twig = new \Twig\Environment($loader);

        $lexer = new Lexer($this->twig, array($this->helpers()));
        $this->twig->setLexer($lexer);
    }

    public function rendenrizar(string $view, array $dados)
    {
        return $this->twig->render($view, $dados);
    }

    private function helpers(): void
    {
        array(
            $this->twig->addFunction(
                new \Twig\TwigFunction('url', function (?string $url = null) {
                    return Helpers::url($url);
                })
            )
        );
    }
}
