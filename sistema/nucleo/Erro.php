<?php

namespace sistema\nucleo;

class Erro
{
    protected ?string $erro = null;

    public function definir(string $mensagem): void
    {
        $this->erro = $mensagem;
    }

    public function obter(): ?string
    {
        return $this->erro;
    }

    public function temErro(): bool
    {
        return $this->erro !== null;
    }

    public function limparErro(): void
    {
        $this->erro = null;
    }
}