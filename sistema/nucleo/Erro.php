<?php

namespace sistema\nucleo;

class Erro
{
    protected string $mensagem;

    protected string $regra;

    protected string $contexto;

    protected string|int $parametro;

    public function definirErro(string $contexto, string $regra, string|int|null $parametros = null): void
    {
        $this->contexto = $contexto;
        $this->regra = $regra;

        if (isset($parametros) && $parametros !== '') {
            $this->parametro = $parametros;
        }
    }

    public function definirMensagem(string $mensagem): void
    {
        $this->mensagem = $mensagem;
    }

    public function __get($atributo)
    {
        isset($this->$atributo) ? $this->$atributo : null;
    }

    public function temErro(): bool
    {
        return $this->erro['mensagem'] !== null;
    }

    public function limparErro(): void
    {
        array_walk($this->erro, function (&$valor) {
            $valor = null;
        });
    }
}
