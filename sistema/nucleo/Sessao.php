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
}
