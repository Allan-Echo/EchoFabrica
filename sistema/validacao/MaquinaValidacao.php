<?php

namespace sistema\validacao;

use sistema\nucleo\Validacao;

class MaquinaValidacao extends Validacao
{
    public $dados = ['modelo' => 'abcd', 'marca' => 'php', 'funcao' => '1', 'operacoes' => '2', 'qtd' => '3', 'valor' => '100,50'];

    public function __construct(array $dados)
    {
        return parent::__construct($dados);
    }

    protected array $regras = [
        'modelo'    => 'requirido',
        'marca'     => 'requirido',
        'funcao'    => 'requirido',
        'operacoes' => 'requirido|inteiro|min:1',
        'qtd'       => 'requirido|inteiro|min:1',
        'valor'     => 'float|min:0'
    ];

    // Opcional: Sobrescreve mensagens se quiser padronizar com as mensagens que você já exibia no sistema
    protected array $mensagens = [
        'modelo.requirido'  => 'Campos obrigatórios em branco',
        'marca.requirido'   => 'Campos obrigatórios em branco',
        'funcao.requirido'  => 'Campos obrigatórios em branco',
        'operacoes.inteiro' => 'Campos numéricos inválidos',
        'operacoes.min'     => 'Campos numéricos inválidos',
        'qtd.inteiro'       => 'Campos numéricos inválidos',
        'qtd.min'           => 'Campos numéricos inválidos',
        'valor.float'       => 'Valor de compra inválido',
        'valor.min'         => 'Valor de compra inválido',
    ];
}
