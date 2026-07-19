<?php

namespace sistema\modelo;

use sistema\nucleo\Modelo;

class Layout extends Modelo
{
  protected string $tabela = 'layout';

  public function cadastrarLayout(array $dados): void
  {
    $query = "INSERT INTO {$this->tabela} (denomination, observation) 
      VALUES (:nome,:descricao)";

    $this->conection->insert($query, $dados);
  }

  public function listarLayout(): array
  {
    $query = "SELECT * FROM {$this->tabela}";

    return $this->conection->select($query);
  }

  public function buscarLayout(): array
  {
    $query = "SELECT * FROM {$this->tabela} ORDER BY denomination ASC";
    return $this->conection->select($query);
  }

  public function filtrarLayout(string $id): array
  {
    $query = "SELECT * FROM {$this->tabela} WHERE id_layout = $id";

    return $this->conection->select($query);
  }

  //Analisar como seria para deletar, pois o layout está vinculado em outras tabelas
  public function deletar(string $id): void
  {
    $query = "DELETE FROM {$this->tabela} WHERE id_layout = $id";
    $this->conection->delete($query);
  }
}
