<?php

namespace sistema\nucleo;

use sistema\nucleo\suporte\EasyPDO;

abstract class Modelo
{   
    /**
     * @var EasyPDO
     */
    protected EasyPDO $conection;
    protected Mensagem $mensagem;
    protected Erro $erro;
    
    protected string $tabela;
    protected mixed $dados;
    protected mixed $query = null;
    protected mixed $parametros = null; 
    protected mixed $ordem = null;
    protected mixed $limite = null;
    protected mixed $offset = 0;
    
    /**
     * Construtor da classe Modelo.
     *
     * Inicializa a conexão com o banco de dados, cria um objeto de mensagens
     * e define o nome da tabela que será usada para operações CRUD.
     *
     * @param string $tabela Nome da tabela no banco de dados
     */
    public function __construct(string $tabela)
    {
        $this->conection = new EasyPDO();
        $this->mensagem = new Mensagem();
        $this->erro = new Erro;
        $this->tabela = $tabela;
    }

    /**
     * Monta a base de uma consulta SELECT com filtros opcionais.
     *
     * Constrói a cláusula SELECT básica e WHERE (se fornecida).
     * Utiliza prepared statements para segurança contra SQL injection.
     * Retorna $this para permitir encadeamento de métodos (Fluent Interface).
     *
     * @param string|null $where Cláusula WHERE, ex: "status = :status AND ativo = :ativo"
     * @param string|null $parametros String de parâmetros, ex: "status=1&ativo=true"
     * @param string $coluna Colunas a selecionar. Padrão: '*' (todas)
     *
     * @return self Retorna a instância para encadeamento
     */
    public function buscar(?string $where = null, ?string $parametros = null, string $coluna = '*'): self
    {
       $this->query = "SELECT {$coluna} FROM " . $this->tabela;

        if (!empty($where)) {
            $this->query .= " WHERE {$where}";
            parse_str($parametros, $this->parametros);
        }

        return $this;
    }

    /**
     * Define a ordenação da consulta SELECT.
     *
     * Armazena a cláusula ORDER BY que será aplicada no resultado final.
     * Chamado após buscar() e antes de resultado().
     *
     * @param string $ordem Cláusula ORDER BY, ex: "nome ASC" ou "id DESC, data ASC"
     *
     * @return self Retorna a instância para encadeamento
     */
    public function ordenar(string $ordem): self
    {
        $this->ordem = $ordem;
        return $this;
    }

    /**
     * Define a paginação da consulta (LIMIT e OFFSET).
     *
     * Armazena o número máximo de registros a retornar e a posição inicial.
     * Útil para implementar paginação em listas de dados.
     *
     * @param int $limite Quantidade máxima de registros
     * @param int $offset Posição inicial (quantos pular). Padrão: 0
     *
     * @return self Retorna a instância para encadeamento
     */
    public function limitar(int $limite, int $offset = 0): self
    {
        $this->limite = $limite;
        $this->offset = $offset;
        return $this;
    }

    /**
     * Executa a consulta SELECT montada pelos métodos anteriores.
     *
     * Completa a query com ORDER BY e LIMIT/OFFSET (se definidos),
     * executa contra o banco e retorna array de objetos.
     * Sempre chamado por último na cadeia de métodos.
     *
     * @return array Array de objetos do tipo da classe filha
     *
     * @see buscar() Monta a base da consulta
     * @see ordenar() Define ORDER BY
     * @see limitar() Define LIMIT e OFFSET
     */
    public function resultado(): array
    {
        $query = $this->query;

        if (!empty($this->ordem)) {
            $query .= " ORDER BY {$this->ordem}";
        }

        if (!empty($this->limite)) {
            $query .= " LIMIT {$this->limite} OFFSET {$this->offset}";
        }

        return $this->conection->select($query, $this->parametros ?? null, static::class);
    }

    /**
     * Insere um novo registro na tabela.
     *
     * Método protegido chamado internamente por salvar().
     * Sanitiza dados via filtro() antes de inserir.
     * Usa prepared statements com parâmetros nomeados.
     *
     * @param array $dados Array associativo onde chaves são nomes de colunas
     *
     * @return void
     *
     * @see filtro() Sanitiza os dados antes da inserção
     * @see salvar() Método público que chama este
     */
    protected function cadastrar(array $dados): bool|string|null {
        
    try {
       $this->erro->limparErro();

        $dados = $this->filtro($dados);

        $colunas = implode(', ', array_keys($dados));
        $valores = ':' . implode(', :', array_keys($dados));
        $query = "INSERT INTO {$this->tabela} ({$colunas}) VALUES ({$valores})";
        
        $this->conection->insert($query, $dados);

        return true;
    } catch (\Throwable $e) {
        $this->erro->definir('Falha ao inserir no banco' . $e->getMessage());
        return false;
    }
        
    }

    /**
     * Sanitiza e normaliza dados antes de persistência.
     *
     * Chamado por cadastrar() e atualizar().
     * Aplica filtros conforme o tipo:
     * - Strings: trim() remove espaços
     * - Inteiros: FILTER_SANITIZE_NUMBER_INT
     * - Floats: FILTER_SANITIZE_NUMBER_FLOAT
     * - Outros: mantém valor original
     *
     * @param array $dados Array associativo com dados a filtrar
     *
     * @return array Array com dados sanitizados
     *
     * @see cadastrar() Chama antes de INSERT
     * @see atualizar() Chama antes de UPDATE
     */
    private function filtro (array $dados): array
    {
        $dadosFiltrados = [];
        foreach ($dados as $key => $value) {
            if (is_string($value)) {
                $dadosFiltrados[$key] = trim($value);
            } elseif (is_int($value)) {
                $dadosFiltrados[$key] = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
            } elseif (is_float($value)) {
                $dadosFiltrados[$key] = filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, [FILTER_FLAG_ALLOW_FRACTION|FILTER_FLAG_ALLOW_THOUSAND]);
            } else {
                $dadosFiltrados[$key] = $value;
            }
        }

        return $dadosFiltrados;
    }
    /**
     * Atualiza registros existentes na tabela.
     *
     * Método protegido com cláusula WHERE obrigatória.
     * Sanitiza dados via filtro() antes do UPDATE.
     * Suporta atualizar múltiplos registros conforme a condição WHERE.
     *
     * @param array $dados Array com colunas e novos valores
     * @param string $where Cláusula WHERE para identificar registros, ex: "id = :id"
     * @param array $parametros Array com valores para a cláusula WHERE
     *
     * @return void
     *
     * @throws \PDOException Se erro na execução da query SQL
     *
     * @see filtro() Sanitiza os dados antes da atualização
     */
    protected function atualizar(array $dados, string $where, array $parametros): bool 
    {
        $dados = $this->filtro($dados);

        $set = [];
        foreach ($dados as $key => $value) {
            $set[] = "{$key} = :{$key}";
        }
        $setString = implode(', ', $set);
        $query = "UPDATE {$this->tabela} SET {$setString} WHERE {$where}";

        $this->conection->update($query, array_merge($dados, $parametros));

        return true;
    }

    /**
     * Retorna a mensagem de erro da última operação.
     *
     * Getter para a propriedade protegida $erro.
     * Chame após salvar(), cadastrar() ou atualizar()
     * para verificar se houve problema.
     *
     * @return mixed Mensagem de erro ou null
     *
     * @see mensagem() Retorna objeto Mensagem para mais controle
     */
    public function erro(): Erro
    {
        return $this->erro;
    }

    /**
     * Retorna o objeto Mensagem para feedback ao usuário.
     *
     * Permite criar, armazenar e exibir mensagens de sucesso, erro ou aviso.
     * O objeto Mensagem gerencia como as mensagens são apresentadas.
     *
     * @return Mensagem Instância do objeto Mensagem
     *
     * @see erro() Retorna apenas a mensagem de erro anterior
     */
    public function mensagem(): Mensagem
    {
        return $this->mensagem;
    }

    public function dados(): mixed
    {
        return $this->dados;
    }

    /**
     * Magic method que captura atribuições de propriedades dinâmicas.
     *
     * Quando você atribui valor a propriedade não declarada explicitamente,
     * este método é chamado automaticamente.
     * Armazena o atributo em um stdObject dentro de $this->dados.
     * Depois, armazenar() converte em array para persistência.
     *
     * @param string $name Nome da propriedade
     * @param mixed $value Valor a atribuir
     *
     * @return void
     *
     * @see armazenar() Converte dados dinâmicos em array
     * @see salvar() Usa armazenar() para preparar dados
     */
    public function __set(string $name, mixed $value): void
    {
        if(empty($this->dados)) {
            $this->dados = new \stdClass();
        }
        $this->dados->$name = $value;
    }

    public function __isset(string $campo):bool
    {
        return isset($this->dados->$campo);
    }

    public function __get(string $campo): mixed
    {
        return $this->dados->$campo ?? null;
    }

    /**
     * Converte dados dinâmicos em array associativo e sanitizado.
     *
     * Funciona como adaptador entre o objeto $this->dados (criado via __set)
     * e os métodos cadastrar() e atualizar() que requerem arrays.
     * Também chama filtro() para sanitizar valores.
     *
     * @return array Array associativo com dados sanitizados
     *
     * @see __set() Cria os dados dinâmicos
     * @see filtro() Sanitiza antes de retornar
     * @see salvar() Chama este método para preparar dados
     */
    protected function armazenar(): array
    {
        $dados = (array) $this->dados;
        return $this->filtro($dados);
    }

    /**
     * Persiste os dados do modelo no banco de dados.
     *
     * Método orquestrador que decide entre INSERT (novo) ou UPDATE (existente).
     * Se id está vazio, é novo registro; caso contrário, já existe.
     * Chama armazenar() para preparar, depois cadastrar() para inserir.
     * Se erro, cria mensagem flash e retorna false.
     *
     * @return bool true se salvou com sucesso, false se erro
     *
     * @see __set() Atribui dados via propriedades dinâmicas
     * @see armazenar() Converte dados em array
     * @see cadastrar() Executa INSERT
     * @see erro() Obtém mensagem de erro
     */
    public function salvar(): bool
{
    return empty($this->id) ? $this->executarCadastro() : $this->executarAtualizacao();
}

    private function executarCadastro(): bool
    {
        if (!$this->cadastrar($this->armazenar())) {
            $this->mensagem->erro('Erro de sistema ao tentar cadastrar os dados.')->flash();
            return false;
        }

        $this->id = $this->conection->lastInsertId();
        
        return true;
    }

    private function executarAtualizacao(): bool
    {
        if (!$this->atualizar($this->armazenar(), "id = :id", ['id' => $this->id])) {
            $this->mensagem->erro('Erro de sistema ao tentar atualizar os dados.')->flash();
            return false;
        }
        
        $this->dados->buscarPorId($this->id);
        return true;
    }

    /**
     * Busca um registro específico pela chave primária.
     *
     * Método de conveniência para consultas rápidas por ID.
     * Mais direto que usar buscar() para um registro específico.
     * Retorna null se nenhum registro for encontrado.
     *
     * @param int $id Valor do ID (chave primária)
     *
     * @return object|null Objeto da classe filha com dados, ou null se não existe
     *
     * @see buscar() Alternativa mais flexível para consultas customizadas
     */
    public function buscarPorId(int $id): ?object
    {
        $this->buscar("id = :id", "id={$id}");
        $resultado = $this->resultado();
        return !empty($resultado) ? $resultado[0] : null;
    }
}
