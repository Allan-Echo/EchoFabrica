<?php

namespace sistema\modelo;

use sistema\nucleo\Modelo;

class UsuarioModelo extends Modelo
{
    protected string $tabela = 'users';
    
    public function buscarPorEmail(string $email): ?UsuarioModelo
    {
        $query = "SELECT * FROM {$this->tabela} WHERE email = :email LIMIT 1";
        return $this->conection->select($query, ['email' => $email]);
    }
    
    public function login(array $dados, int $level = 1): bool
    {
        $usuario = (new UsuarioModelo())->buscarPorEmail($dados['email']);

        if (!$usuario || !password_verify($dados['senha'], $usuario->senha) || $usuario->level < $level) {
            $this->mensagem->erro('Email ou senha inválidos')->flash();
            return false;
        }

        $this->mensagem->sucesso('Login realizado com sucesso')->flash();
        return true;
    }
}
