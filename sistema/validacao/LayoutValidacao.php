<?php

namespace sistema\validacao;

use sistema\nucleo\Validacao;

require_once __DIR__ . '/../../vendor/autoload.php';

class LayoutValidacao extends Validacao
{
    public function __construct(array $dados)
    {
        return parent::__construct($dados);
    }

    protected array $regras = [
        'nome'       => 'requirido|texto|max:15',
        'descricao'  => 'requirido|texto|max:255',
        'observacao' => 'texto|max:255'
    ];

    // Opcional: Sobrescreve mensagens se quiser padronizar com as mensagens que você já exibia no sistema
    protected array $mensagens = [
        'nome.requirido'        => 'Campos obrigatórios em branco',
        'nome.texto'            => 'Tipo inválido de dado para o nome',
        'nome.max'              => 'Nome do Layout muito longo',
        'descricao.requirido'   => 'Campos obrigatórios em branco',
        'descricao.texto'       => 'Tipo inválido de dado para o nome',
        'descricao.max'         => 'Descrição superou o limite de caracters',
        'observacao.texto'      => 'Observação precisa ser um texto',
        'observacao.max'        => 'Descrição superou o limite de caracters'
    ];
}
