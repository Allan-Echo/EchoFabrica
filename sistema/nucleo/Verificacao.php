<?php

namespace sistema\nucleo;

abstract class Validacao
{
    /**
     * @var array Dados brutos recebidos (ex: $_POST)
     */
    protected array $dadosBrutos = [];

    /**
     * @var array Dados validados e convertidos para seus devidos tipos
     */
    protected array $dadosValidados = [];

    /**
     * @var array Regras declarativas da classe filha
     * Ex: ['modelo' => 'requirido|min:3']
     */
    protected array $regras = [];

    /**
     * @var array Mensagens personalizadas por campo/regra
     * Ex: ['modelo.requirido' => 'Informe o modelo da máquina']
     */
    protected array $mensagens = [];

    /**
     * @var Erro Instância para gerenciamento e captura de erros de banco/sistema.
     */
    protected Erro $erro;

    public function __construct(array $dados)
    {
        $this->dadosBrutos = $dados;
        $this->executar();
        $this->erro = new Erro();
    }

    /**
     * Executa o parse das regras declarativas e valida campo por campo.
     */
    protected function executar(): void
    {
        $this->erro->limparErro();

        foreach ($this->regras as $campo => $regrasString) {
            $valor = $this->dadosBrutos[$campo] ?? null;
            $listaRegras = explode('|', $regrasString);

            foreach ($listaRegras as $regraCompleta) {
                // Separa o nome da regra de seus parâmetros (ex: min:1 -> $regra='min', $parametro='1')
                $partes = explode(':', $regraCompleta, 2);
                $regra = $partes[0];
                $parametro = $partes[1] ?? null;

                // Executa o método da regra correspondente (ex: validarRequirido)
                $metodo = 'validar' . ucfirst($regra);

                if (method_exists($this, $metodo)) {
                    $passou = $this->$metodo($campo, $valor, $parametro);

                    if (!$passou) {
                        $this->erro($campo, $regra, $parametro)->definirMensagem();
                        // Interrompe outras validações para o mesmo campo ao encontrar o primeiro erro dele
                        break;
                    }
                }
            }

            // Se não houve erro para este campo, guarda o valor filtrado no array final
            if (!isset($this->erro->mensagem) && isset($this->dadosBrutos[$campo])) {
                $this->dadosValidados[$campo] = $valor;
            }
        }
    }

    // =========================================================================
    // REGRAS DE VALIDAÇÃO (Métodos Internos)
    // =========================================================================

    protected function validarRequirido(string $campo, mixed $valor): bool
    {
        if (is_null($valor)) {
            return false;
        }
        if (is_string($valor) && trim($valor) === '') {
            return false;
        }
        if (is_array($valor) && empty($valor)) {
            return false;
        }
        return true;
    }

    protected function validarInteiro(string $campo, mixed &$valor): bool
    {
        if (is_null($valor) || $valor === '') {
            return true; // Se for opcional e estiver vazio, ignora. Use 'requirido' junto para obrigatoriedade.
        }

        $validado = filter_var($valor, FILTER_VALIDATE_INT);
        if ($validado === false) {
            return false;
        }

        $valor = $validado; // Atualiza para o tipo inteiro nativo do PHP
        return true;
    }

    protected function validarFloat(string $campo, mixed &$valor): bool
    {
        if (is_null($valor) || $valor === '') {
            return true;
        }

        $validado = filter_var($valor, FILTER_VALIDATE_FLOAT);
        if ($validado === false) {
            return false;
        }

        $valor = $validado; // Atualiza para o tipo float nativo do PHP
        return true;
    }

    protected function validarEmail(string $campo, mixed $valor): bool
    {
        if (is_null($valor) || $valor === '') {
            return true;
        }
        return filter_var($valor, FILTER_VALIDATE_EMAIL) !== false;
    }

    protected function validarMin(string $campo, mixed $valor, ?string $parametro): bool
    {
        if (is_null($valor) || $valor === '' || is_null($parametro)) {
            return true;
        }

        $minimo = (float) $parametro;

        // Se for número (int ou float)
        if (is_numeric($valor)) {
            return $valor >= $minimo;
        }

        // Se for texto/string
        return mb_strlen((string) $valor) >= $minimo;
    }

    protected function validarMax(string $campo, mixed $valor, ?string $parametro): bool
    {
        if (is_null($valor) || $valor === '' || is_null($parametro)) {
            return true;
        }

        $maximo = (float) $parametro;

        if (is_numeric($valor)) {
            return $valor <= $maximo;
        }

        return mb_strlen((string) $valor) <= $maximo;
    }

    // =========================================================================
    // GERENCIAMENTO DE ERROS E RESULTADOS
    // =========================================================================

    protected function definirMensagem(): void
    {
        $chaveMensagem = "{$this->erro->contexto}.{$this->erro->regra}";

        // 1. Procura mensagem personalizada exata (ex: 'modelo.requirido')
        if (isset($this->mensagens[$chaveMensagem])) {
            $this->erro->definirMensagem($this->mensagens[$chaveMensagem]);
            return;
        }

        // 2. Mensagem padrão caso não haja personalizada
        $this->erro->definirMensagem($this->mensagemPadrao());
    }

    protected function mensagemPadrao(): string
    {
        return match ($this->erro->regra) {
            'requirido' => "O campo {$this->erro->contexto} é de preenchimento obrigatório.",
            'float'     => "O campo {$this->erro->contexto} deve ser um número decimal válido.",
            'inteiro'   => "O campo {$this->erro->contexto} deve ser um número inteiro válido.",
            'email'     => "O campo {$this->erro->contexto} deve conter um e-mail válido.",
            'min'       => "O campo {$this->erro->contexto} deve ter o valor ou tamanho mínimo de {$this->erro->parametro}.",
            'max'       => "O campo {$this->erro->contexto} deve ter o valor ou tamanho máximo de {$this->erro->parametro}.",
            default     => "O campo {$this->erro->contexto} é inválido."
        };
    }

    public function passou(): bool
    {
        return empty($this->erro->mensagem);
    }

    public function falhou(): bool
    {
        return !$this->passou();
    }

    public function erro(string $campo, string $regra, string|int|null $parametro = null): static
    {
        $this->erro->definirErro($campo, $regra, $parametro);
        return $this;
    }

    public function primeiroErro(): ?string
    {
        if (!isset($this->erro->mensagem)) {
            return null;
        }

        return $this->erro->mensagem;
    }

    public function dados(): array
    {
        return $this->dadosValidados;
    }
}
