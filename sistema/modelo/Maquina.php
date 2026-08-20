<?php

namespace sistema\modelo;

use sistema\nucleo\Modelo;

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

  public function cadastrarMaquina(array $dados): ?array
  {
    $colunas = $this->buscarColunas();
    if (!$colunas) {
      $mensagem = $this->erro->obter();
      $this->mensagem->erro($mensagem)->flash();
    }

    // for ($i=0; $i < count($colunas) ; $i++) {
    //   if ($colunas[$i] == 'id_machine')
    //   continue; 
      foreach ($dados as $value) {
        $i = 0;  
        if ($colunas[$i] == 'id_machine')
        continue;
      
        $colunas[$i] = $dados[$value];
        $i++;
      }
      return die(print_r($colunas));
    }

    
    

  }
  //   $query = "INSERT INTO machine (model, brand, designation, piece_operations, purchase_price, quantity) VALUES (:modelo, :marca, :funcao, :operacoes, :valor, :qtd)"; // testar se funciona sem as aspas em values

  //   $this->conection->insert($query, $dados);
  //