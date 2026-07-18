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
    $query = "SELECT * FROM layout";

    return $this->conection->select($query);
  }

  public function buscarLayout(): array
  {
    $query = "SELECT * FROM {$this->tabela} ORDER BY denomination ASC";
    return $this->conection->select($query);
  }

  public function filtrarLayout(string $id): array
  {
    $query = "SELECT * FROM layout WHERE id_layout = $id";

    return $this->conection->select($query);
  }
}
