<?php

namespace sistema\modelo;

use sistema\nucleo\Modelo;

class LayoutMaquina extends Modelo
{
    public function __construct()
    {
        parent::__construct('layout_machine', 'fk_id_layout, fk_id_machine');
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

    public function montarLayout(Layout $layout, array $maquinas)
    {
        $cadastroLayout = $layout->prepararCadastro()->query;
        $moldeLayoutMaquina = $this->montarMolde($maquinas);
        /* $this->fk_layout_id = null; // so pra me lembrar que o retorno do insert precisa atribuir
           $this->fk_machine_id = null;
           $layout->id = null */

        $this->conection->insertComDependencia($cadastroLayout, $layout->dadosComoArray(), $moldeLayoutMaquina);
    }

    private function montarMolde(array $maquinas): LayoutMaquina // segunda transação
    {
        $placeholdersNomeados = ':' . str_replace(', ', ', :', $this->colunas);
        $this->queryBase =
        "INSERT INTO {$this->tabela} ({$this->colunas}) VALUES ({$placeholdersNomeados})";

        $this->colunaDependente = implode(array_keys($maquinas));
        $this->valoresDependentes = current($maquinas);
        $this->colunaPrimaria = 'fk_id_layout';

        /* foreach ($fk_id_machine as $maquinaId) {
            $query = "INSERT INTO {$this->tabela} ({$colunas}) VALUES ($layoutId, $maquinaId)";
            $querys[] = $query;
        } */

        return $this;
    }

    public function buscarLayoutMachine(string $layout): array
    {
        $query = "SELECT m.id_machine, m.model, m.designation FROM {$this->tabela} AS lm JOIN machine AS m ON lm.fk_id_machine = m.id_machine WHERE fk_id_layout = $layout 
        ORDER BY fk_id_machine ASC";

        return $this->conection->select($query);
    }
}
