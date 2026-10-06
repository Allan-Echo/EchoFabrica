<?php

namespace sistema\validacao;

use sistema\nucleo\Validacao;

require_once __DIR__ . '/../../vendor/autoload.php';

class LayoutMaquinaValidacao extends Validacao
{
    public function __construct(array $dados)
    {
        return parent::__construct($dados);
    }

    protected array $regras = [
        'fk_id_machine'  => 'requirido|Array:Inteiro'
    ];

    // Opcional: Sobrescreve mensagens se quiser padronizar com as mensagens que você já exibia no sistema
    protected array $mensagens = [
        'fk_id_machine.requirido' => 'Nenhuma máquina foi selecionada',
        'fk_id_machine.Array'   => 'Máquina inválida'
    ];
}
