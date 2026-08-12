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

    protected function cadastrar(array $dados): void {
        $dados = $this->filtro($dados);

        $colunas = implode(', ', array_keys($dados));
        $valores = ':' . implode(', :', array_keys($dados));
        $query = "INSERT INTO {$this->tabela} ({$colunas}) VALUES ({$valores})";
        
        $this->conection->insert($query, $dados);
    }

    private function filtro (array $dados): array
    {
        $dadosFiltrados = [];
        foreach ($dados as $key => $value) {
            if (is_string($value)) {
                $dadosFiltrados[$key] = trim($value);
            } elseif (is_int($value)) {
                $dadosFiltrados[$key] = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
            } elseif (is_float($value)) {
                $dadosFiltrados[$key] = filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            } else {
                $dadosFiltrados[$key] = $value;
            }
        }

        return $dadosFiltrados;
    }
    /**
     * @param array $dados um array associativo contendo os dados a serem atualizados, onde as chaves são os nomes das colunas e os valores são os novos valores a serem atribuídos.
     * @param string $where uma string representando a cláusula WHERE da consulta SQL, que define a linha ou linhas a serem atualizadas. Por exemplo, "id = :id" para atualizar uma linha específica com base no ID.
     * @param array $parametros um array associativo contendo os parâmetros a serem vinculados à cláusula WHERE da consulta SQL. Por exemplo, se a cláusula WHERE for "id = :id", o array de parâmetros deve conter ['id' => $valorId].
     * @return void
     * @throws \PDOException se ocorrer um erro durante a execução da consulta SQL.
     */
    protected function atualizar(array $dados, string $where, array $parametros): void 
    {
        $dados = $this->filtro($dados);

        $set = [];
        foreach ($dados as $key => $value) {
            $set[] = "{$key} = :{$key}";
        }
        $setString = implode(', ', $set);
        $query = "UPDATE {$this->tabela} SET {$setString} WHERE {$where}";

        $this->conection->update($query, array_merge($dados, $parametros));
    }

    public function buscarPorId(int $id): ?object
    {
        $query = "SELECT * FROM " . $this->tabela . " WHERE id = :id LIMIT 1";
        $resultado = $this->conection->select($query, ['id' => $id]) ?? null;
        return $resultado ? (object) $resultado[0] : null;
    }
}
