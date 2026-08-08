<?php

namespace sistema\modelo;

use sistema\nucleo\Modelo;
use sistema\nucleo\Sessao;

class UsuarioModelo extends Modelo
{
    protected string $tabela = 'users';
    public function buscarPorEmail(string $email): ?Array
    {
        $query = "SELECT * FROM " . $this->tabela . " WHERE email = :email LIMIT 1";
        return $this->conection->select($query, ['email' => $email]) ?? null;
    }
   
    public function login(array $dados, int $level = 1): bool
    {
        // Busca o usuário no banco de dados pelo email fornecido, porém ele vem como um array de arrays, então é necessário pegar o primeiro elemento do array, que é o usuário que queremos e posteriormente transformá-lo em um objeto para facilitar o acesso aos seus atributos.
        $dadosUsuario = (new UsuarioModelo())->buscarPorEmail($dados['email']);

        //Usuario é um objeto com os dados do usuário, ou null caso não exista. O operador de coalescência nula (??) verifica se o valor à esquerda é nulo e, se for, retorna o valor à direita. Nesse caso, se $dadosUsuario[0] for nulo, $usuario será nulo.
        $usuario = (object) $dadosUsuario[0] ?? null;

        if (!$usuario || $dados['senha'] !== $usuario->password) {
            $this->mensagem->erro('Email ou senha inválidos')->flash();
            return false;
        }
        else if ($usuario->level < $level) {
            $this->mensagem->alerta('Você não tem permissão para acessar esta área')->flash();
            return false;
        }
        else if ($usuario->status !== 1) {
            $this->mensagem->alerta('Sua conta está inativa. Entre em contato com o administrador')->flash();
            return false;
        }

        (new Sessao())->criar('usuarioId', $usuario->id);

        $this->mensagem->sucesso('Login realizado com sucesso')->flash();
        return true;
    }
}
