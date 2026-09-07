<?php

namespace sistema\modelo;

use sistema\nucleo\Modelo;
use sistema\nucleo\Sessao;

class UsuarioModelo extends Modelo
{
    public function __construct()
    {
        parent::__construct('users');
    }

    public function buscarPorEmail(string $email): ?array
    {
        $query = 'SELECT * FROM ' . $this->tabela . ' WHERE email = :email LIMIT 1';
        return $this->conection->select($query, ['email' => $email]) ?? null;
    }

    public function login(array $dados, int $level = 1): bool
    {

        $dadosUsuario = (new UsuarioModelo())->buscarPorEmail($dados['email']);

        $usuario = (object) $dadosUsuario[0] ?? null;

        if (!$usuario || $dados['senha'] !== $usuario->password) {
            $this->mensagem->erro('Email ou senha inválidos')->flash();
            return false;
        } elseif ($usuario->level < $level) {
            $this->mensagem->alerta('Você não tem permissão para acessar esta área')->flash();
            return false;
        } elseif ($usuario->status !== 1) {
            $this->mensagem->alerta('Sua conta está inativa. Entre em contato com o administrador')->flash();
            return false;
        }

        (new Sessao())->criar('usuarioId', $usuario->id);

        $this->mensagem->sucesso('Login realizado com sucesso')->flash();
        return true;
    }
}
