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

    //Os controladres fazem verificações de formulários, caso alguma validação precise retorna uma mensagem direta do servidor(Suceeso,erro..), eles vão instanciar um objeto dessa classe (Mesangem) escolher a mensagem(métodos dessa classe) que vai "parametrizar" os atributos do objeto e a função flash vai armazenar esse objeto na sessão do Usuário. Antes de vermos como a classe sessão vai clonar o obejto instaciado no controlador para depois poder limpar essa mensagem armazenada na sessão, armazena o clone do objeto e devolver o clone quando o método do helpers "pedir o clone" para renderizar na view. Precisamos ver como helpers faz a validação e chama e recebe o clone criado np método da classe Sessão. 
    public function flash(): void
    {
        ((new Sessao)->criar('flash',$this));
    }
}
