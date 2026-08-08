<?php

namespace sistema\nucleo;

use sistema\nucleo\suporte\EasyPDO;

abstract class Modelo
{
    protected EasyPDO $conection;
    protected Mensagem $mensagem;
    protected string $tabela;

    public function __construct()
    {
        $this->conection = new EasyPDO();
        $this->mensagem = new Mensagem();
    }

    public function buscarPorId(int $id): ?object
    {
        $query = "SELECT * FROM " . $this->tabela . " WHERE id = :id LIMIT 1";
        $resultado = $this->conection->select($query, ['id' => $id]) ?? null;
        return $resultado ? (object) $resultado[0] : null;
    }
}
