<?php

namespace sistema\modelo;

use sistema\nucleo\Modelo;

class LayoutMaquina extends Modelo
{
    protected $tabela = 'layout_machine';

    public function __construct()
    {
        parent::__construct('layout_machine');
    }

    /*   public function montarLayout(string $id, array $dados)
      {
          unset($dados['layout']); // fazer formulario parar de enviar, colocar condicional
          $querys = [];
          //$i= 1;
          foreach ($dados as $chave => $valor) {
              $query = "INSERT INTO {$this->tabela} (fk_id_layout, fk_id_machine) VALUES ";
              $querys[] = $query .= "($id, $valor)";
              //$i++;
          }


          //return $querys;
          // $dados = [
          //   'query1' => "INSERT INTO `layout_machine` (fk_id_layout, fk_id_machine) VALUES ('1','2')",
          //   'query2' => "INSERT INTO `layout_machine` (fk_id_layout, fk_id_machine) VALUES ('1','3')"
          // ];
          $this->conection->insertMult($querys);
      } */

    public function montarLayout()
    {
        $querys = $this->montarQuerys();

        $this->conection->insertMult($querys);
    }

    private function montarQuerys(): array
    {
        $colunas = 'fk_id_layout, fk_id_machine';
        $layoutId = $this->fk_id_layout;
        $maquinasId = $this->maquinas;
        $querys = [];

        foreach ($maquinasId as $fk_id_machine) {
            $query = "INSERT INTO {$this->tabela} ({$colunas}) VALUES ($layoutId, $fk_id_machine)";
            $querys[] = $query;
        }
        return $querys;
    }

    public function buscarLayoutMachine(string $layout): array
    {
        $query = "SELECT m.id_machine, m.model, m.designation FROM {$this->tabela} AS lm JOIN machine AS m ON lm.fk_id_machine = m.id_machine WHERE fk_id_layout = $layout 
        ORDER BY fk_id_machine ASC";

        return $this->conection->select($query);
    }
}
