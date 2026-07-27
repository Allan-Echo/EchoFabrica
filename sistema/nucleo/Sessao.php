<?php

namespace sistema\nucleo;

class Sessao
{
    public function __construct()
    {
        if(!session_id()) {
            session_start();
        }
    }

    public function criar(string $chave, mixed $valor): Sessao
    {
        $_SESSION[$chave] = (is_array($valor) ? (object) $valor : $valor);
        return $this;
    }

    public function carregar(): ?object
    {
        return (object) $_SESSION;
    }

    //Apaga uma única chave do array. O usuário continua logado e o ID da sessão é mantido. Limpeza pontual de dados.
    public function limpar(string $chave): Sessao
    {
        unset($_SESSION[$chave]);
        return $this;
    }

    public function checar(string $chave): bool
    {
        return isset($_SESSION[$chave]);
    }

    //Destrói o arquivo de sessão inteiro no servidor e invalida a sessão atual. Destrói todas as informações salvas na sessão. Logout completo do usuário.
    public function deletar(): Sessao
    {
        session_destroy();
        return $this;
    }

    //Toda vez que alguém tentar ler uma propriedade neste objeto que NÃO EXISTE, não dê erro! Em vez disso, chame a função __get, me dê o nome do que tentaram ler e deixa que eu busco no lugar certo." No nosso caso, o "lugar certo" é a superglobal $_SESSION.
    public function __get($chave)
    {
        if(!empty($_SESSION[$chave])) {
            return $_SESSION[$chave];
        }
    }

    public function flash(): ?Mensagem
    {
        if($this->checar('flash')){
            $flash = $this->flash;
            $this->limpar('flash');
            return $flash;
        }
            return null;    
    }
}
