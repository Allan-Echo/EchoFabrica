<?php

require __DIR__ . '/vendor/autoload.php';

use sistema\validacao\MaquinaValidacao;

var_dump(class_exists('sistema\\validacao\\MaquinaValidacao')); // deve ser true

$dados = ['modelo' => 'abcd', 'marca' => 'php', 'funcao' => 'testar', 'operacoes' => '2', 'qtd' => '3', 'valor' => '100.50'];

$inst = new MaquinaValidacao($dados);

var_dump($inst);
