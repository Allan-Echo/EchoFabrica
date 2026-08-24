<?php

namespace sistema\modelo;

use sistema\nucleo\Modelo;
use stdClass;

class Maquina extends Modelo
{
  public function __construct()
  {
    parent::__construct('machine');
  }

  public function buscarMaq(): array
  {
    //podemos organizar o array de acordo com parâmetros ORDERY BY que passamso na query
    $query = "SELECT * FROM machine ORDER BY model ASC";
    return $this->conection->select($query);
  }
}
  