<?php

namespace sistema\modelo;

use sistema\nucleo\Modelo;

class Producao extends Modelo
{
  public function __construct() {
    parent::__construct('production');
  }

  public function guardarProducao(string $layout, array $dados)
  {
    $data = DATA;
    foreach ($dados as $id_maquina => $operações) {
      $query = "INSERT INTO {$this->tabela} (fk²_id_layout, fk²_id_machine, operations, production_at) VALUES ";
      $querys[] = $query .= "($layout, $id_maquina, $operações, $data)";
    }

    $this->conection->insertMult($querys);
    // return $querys;
  }

  public function buscarProducao(string $layout)
  {
    $hoje = "'" . DATA_ATUAL . "'";
    $query = "SELECT * FROM fabrica.$layout WHERE data = '2024-04-02'";
    $res = $this->conection->select($query);
    unset($res[0]['data']);
    $res = json_encode($res);
    return $res;
  }
}
