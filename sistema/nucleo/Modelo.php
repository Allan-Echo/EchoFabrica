<?php

namespace sistema\nucleo;

use sistema\nucleo\suporte\EasyPDO;

abstract class Modelo
{
    protected EasyPDO $conection;
    protected Mensagem $mensagem;
    
    protected string $tabela;
    protected mixed $dados;
    protected mixed $query = null;
    protected mixed $erro;
    protected mixed $parametros = null; 
    protected mixed $ordem = null;
    protected mixed $limite = null;
    protected mixed $offset = 0;
    
    public function __construct(string $tabela)
    {
        $this->conection = new EasyPDO();
        $this->mensagem = new Mensagem();
        $this->tabela = $tabela;
    }

    public function buscar(?string $where = null, ?string $parametros = null, string $coluna = '*'): self
    {
       $this->query = "SELECT {$coluna} FROM " . $this->tabela;

        if (!empty($where)) {
            $this->query .= " WHERE {$where}";
            parse_str($parametros, $this->parametros);
        }

        return $this;
    }

    public function ordenar(string $ordem): self
    {
        $this->ordem = $ordem;
        return $this;
    }

    public function limitar(int $limite, int $offset = 0): self
    {
        $this->limite = $limite;
        $this->offset = $offset;
        return $this;
    }

    public function resultado(): array
    {
        $query = $this->query;

        if (!empty($this->ordem)) {
            $query .= " ORDER BY {$this->ordem}";
        }

        if (!empty($this->limite)) {
            $query .= " LIMIT {$this->limite} OFFSET {$this->offset}";
        }

        return $this->conection->select($query, $this->parametros ?? null);
    }

    public function buscarPorId(int $id): ?object
    {
        $query = "SELECT * FROM " . $this->tabela . " WHERE id = :id LIMIT 1";
        $resultado = $this->conection->select($query, ['id' => $id]) ?? null;
        return $resultado ? (object) $resultado[0] : null;
    }
}
