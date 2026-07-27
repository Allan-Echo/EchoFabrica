<?php

namespace sistema\nucleo;

class Mensagem
{
    private string $texto;
    private string $css;
    private string $alerta = 'alert';

    public function __toString()
    {
        return $this->renderizar();
    }

    public function sucesso(string $mensagem): Mensagem
    {
        $this->texto = $this->filtrar($mensagem);
        $this->css = 'alert alert-success';
        return $this;
    }

    public function erro(string $mensagem): Mensagem
    {
        $this->texto = $this->filtrar($mensagem);
        $this->css = 'alert alert-danger';
        return $this;
    }

    public function alerta(string $mensagem): Mensagem
    {
        $this->texto = $this->filtrar($mensagem);
        $this->css = 'alert alert-warning';
        return $this;
    }

    public function informa(string $mensagem): Mensagem
    {
        $this->texto = $this->filtrar($mensagem);
        $this->css = 'alert alert-primary';
        return $this;
    }

    public function renderizar(): string
    {
        return "<div class ='{$this->css} role = {$this->alerta}'>{$this->texto}</div>";
    }

    private function filtrar(string $mensagem): string
    {
        return filter_var($mensagem, FILTER_SANITIZE_SPECIAL_CHARS);
    }

    public function flash(): void
    {
        ((new Sessao)->criar('flash',$this));
    }
}
