<?php

namespace sistema\modelo;

use sistema\nucleo\Modelo;

class MaquinaModelo extends Modelo
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



  public function montarLayout(string $id, array $dados)
  {
    unset($dados['layout']); // fazer formulario parar de enviar, colocar condicional
    $querys = [];
    //$i= 1;
    foreach ($dados as $chave => $valor) {
      $query = "INSERT INTO layout_machine (fk_id_layout, fk_id_machine) VALUES ";
      $querys[] = $query .= "($id, $valor)";
      //$i++;
    }
    //return $querys;
    // $dados = [
    //   'query1' => "INSERT INTO `layout_machine` (fk_id_layout, fk_id_machine) VALUES ('1','2')",
    //   'query2' => "INSERT INTO `layout_machine` (fk_id_layout, fk_id_machine) VALUES ('1','3')"
    // ];
    $this->conection->insertMult($querys);
  }

  public function buscarLayout_Machine(string $layout): array
  {
    $query = "SELECT m.id_machine, m.model, m.designation FROM layout_machine AS lm JOIN machine AS m ON lm.fk_id_machine = m.id_machine WHERE fk_id_layout = $layout 
        ORDER BY fk_id_machine ASC";

    return $this->conection->select($query);
  }

  public function guardarProducao(string $layout, array $dados)
  {
    $data = DATA;
    foreach ($dados as $id_maquina => $operações) {
      $query = "INSERT INTO production (fk²_id_layout, fk²_id_machine, operations, production_at) VALUES ";
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

  public function deletar(string $id): void
  {
    $query = "DELETE FROM layout WHERE id_layout = $id";
    $this->conection->delete($query);
  }
}
