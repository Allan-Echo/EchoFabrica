<?php

namespace sistema\modelo;

use sistema\nucleo\Modelo;

class Maquina extends Modelo
{

  public function buscarMaq(): array
  {
    //podemos organizar o array de acordo com parâmetros ORDERY BY que passamso na query
    $query = "SELECT * FROM machine ORDER BY model ASC";
    return $this->conection->select($query);
  }

  public function cadastrarMaquina(array $dados): void
  {
    $query = "INSERT INTO machine (model, brand, designation, piece_operations, purchase_price, quantity) VALUES (:modelo, :marca, :funcao, :operacoes, :valor, :qtd)"; // testar se funciona sem as aspas em values

    $this->conection->insert($query, $dados);
  }
}
