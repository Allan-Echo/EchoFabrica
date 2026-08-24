<?php

namespace sistema\nucleo;

use sistema\nucleo\suporte\EasyPDO;

abstract class Modelo
{   
        /**
     * @var EasyPDO Instância da conexão com o banco de dados via PDO.
     */
    protected EasyPDO $conection;

    /**
     * @var Mensagem Instância para gerenciamento de mensagens de feedback/flash.
     */
    protected Mensagem $mensagem;

    /**
     * @var Erro Instância para gerenciamento e captura de erros de banco/sistema.
     */
    protected Erro $erro;
    
    /**
     * @var string Nome da tabela associada ao modelo no banco de dados.
     */
    protected string $tabela;

    /**
     * @var mixed Armazena os dados do registro (geralmente um stdClass).
     */
    protected mixed $dados;

    /**
     * @var string|null Armazena a query SQL que está sendo construída.
     */
    protected mixed $query = null;

    /**
     * @var array|null Parâmetros para substituição no Prepared Statement.
     */
    protected mixed $parametros = null; 

    /**
     * @var string|null Cláusula de ordenação (ORDER BY).
     */
    protected mixed $ordem = null;

    /**
     * @var int|null Limite de registros a serem retornados (LIMIT).
     */
    protected mixed $limite = null;

    /**
     * @var int Deslocamento inicial para a consulta (OFFSET).
     */
    protected int $offset = 0;

    
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
     * Monta a estrutura base de uma consulta SELECT.
     *
     * Este método utiliza uma interface fluida, permitindo o encadeamento de 
     * métodos como ordenar() e limitar() antes de chamar resultado().
     *
     * Exemplo:
     * ```php
     * $usuarios = $modelo->buscar("status = :s", "s=ativo", "nome, email")
     *                    ->ordenar("nome ASC")
     *                    ->resultado();
     * ```
     *
     * @param string|null $where Cláusula WHERE (ex: "id = :id").
     * @param string|null $parametros Query string de parâmetros (ex: "id=1&status=ativo").
     * @param string $coluna Colunas a selecionar (padrão: "*").
     * @return static Retorna a própria instância para encadeamento.
     * @see ordenar() Para definir a ordem dos resultados.
     * @see limitar() Para definir paginação.
     * @see resultado() Para executar a query e obter os dados.
     */
    public function buscar(?string $where = null, ?string $parametros = null, string $coluna = '*'): static
    {
       $this->query = "SELECT {$coluna} FROM " . $this->tabela;



        if (!empty($where)) {
            $this->query .= " WHERE {$where}";
            parse_str($parametros, $this->parametros);
        }

        return $this;
    }

            /**
     * Define a cláusula de ordenação da consulta.
     *
     * @param string $ordem Cláusula ORDER BY (ex: "id DESC" ou "nome ASC, data DESC").
     * @return static Retorna a própria instância para encadeamento.
     * @see buscar() Deve ser chamado após o buscar().
     */
    public function ordenar(string $ordem): static
    {
        $this->ordem = $ordem;


        return $this;
    }

            /**
     * Define limites de paginação para a consulta.
     *
     * @param int $limite Quantidade máxima de registros.
     * @param int $offset Quantidade de registros a pular (padrão: 0).
     * @return static Retorna a própria instância para encadeamento.
     * @see buscar() Utilizado em conjunto com buscar() para listar dados.
     */
    public function limitar(int $limite, int $offset = 0): static
    {
        $this->limite = $limite;


        $this->offset = $offset;
        return $this;
    }

        /**
     * Finaliza e executa a consulta SELECT montada.
     *
     * Este método compila todas as partes da query (WHERE, ORDER BY, LIMIT)
     * e utiliza o EasyPDO para retornar uma coleção de objetos.
     *
     * @return array Array de objetos da classe que estende este Modelo.
     * @see buscar() Inicia a montagem da query.
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
     * Insere um novo registro na tabela do banco de dados.
     *
     * Método protegido que prepara e executa a inserção. Realiza a limpeza de erros prévios,
     * sanitiza os dados através do método filtro() e trata exceções capturadas
     * durante a execução da query INSERT.
     *
     * @param array $dados Dados associativos (coluna => valor) para inserção.
     *
     * @return bool Retorna true em caso de sucesso ou false em caso de falha.
     *
     * @see filtro() Sanitiza os dados antes da persistência.
     * @see Erro::limparErro() Reseta o estado de erro antes da operação.
     */
    protected function cadastrar(array $dados): bool {
        
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
     * Atualiza registros na tabela do banco de dados.
     *
     * @param array $dados Dados associativos para atualização (coluna => valor).
     * @param string $where Condição para atualização (ex: "id = :id").
     * @param array $parametros Valores dos parâmetros da condição WHERE.
     * @return bool True em caso de sucesso, false em caso de falha.
     * @see filtro() Os dados são automaticamente sanitizados antes do UPDATE.
     */
    protected function atualizar(array $dados, string $where, array $parametros): bool
    {
        try {

            $this->erro->limparErro();

            $dados = $this->filtro($dados);

            $set = [];
            foreach ($dados as $key => $value) {
                $set[] = "{$key} = :{$key}";
            }
            $setString = implode(', ', $set);
            $query = "UPDATE {$this->tabela} SET {$setString} WHERE {$where}";

            $this->conection->update($query, array_merge($dados, $parametros));

            return true;
        } catch (\Throwable $e) {
            $this->erro->definir('Erro de sistema ao atualizar dados'.$e->getMessage());
            return false;
        }
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

        /**
     * Retorna os dados do objeto.
     *
     * @return mixed Os dados armazenados no objeto, geralmente um stdClass ou array de resultados.
     */
    public function dados(): mixed
    {
        return $this->dados;
    }

    /**
     * Magic method que captura atribuições de propriedades dinâmicas.
     *
     * Quando você atribui valor a uma propriedade não declarada explicitamente,
     * este método é chamado automaticamente. Armazena o atributo em um stdClass 
     * dentro de $this->dados para posterior persistência.
     * 
     * Exemplo:
     * ```php
     * $usuario->nome = 'Marcos';
     * ```
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

    /**
     * Verifica se uma propriedade dinâmica existe no objeto de dados.
     *
     * @param string $campo Nome da propriedade
     * @return bool
     */
    public function __isset(string $campo):bool
    {
        return isset($this->dados->$campo);
    }

    /**
     * Recupera uma propriedade dinâmica do objeto de dados.
     *
     * @param string $campo Nome da propriedade
     * @return mixed O valor da propriedade ou null se não encontrada
     */
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
     * Orquestra a persistência dos dados no banco de dados.
     *
     * Decide automaticamente entre uma operação de INSERT (novo registro)
     * ou UPDATE (registro existente) com base na existência da propriedade 'id'.
     *
     * @return bool True se a operação foi bem-sucedida, false caso contrário.
     * @see __set() Cria os dados dinâmicos
     * @see executarCadastro() Chamado se não houver ID.
     * @see executarAtualizacao() Chamado se houver ID.
     * @see erro() Caso retorne false, verifique o erro aqui.
     */
    public function salvar(): bool
    {
        return empty($this->id) ? $this->executarCadastro() : $this->executarAtualizacao();
    }


        /**
     * Orquestra a persistência de um novo registro no banco de dados.
     * 
     * Chamado internamente pelo método salvar(). Além de cadastrar, 
     * recupera o último ID inserido e o atribui ao objeto.
     *
     * @return bool True em caso de sucesso, false se houver falha (com mensagem flash).
     * @see salvar()
     * @see cadastrar()
     */
    private function executarCadastro(): bool
    {
        if (!$this->cadastrar($this->armazenar())) {
            $mensagem = $this->erro->obter();
            $this->mensagem->erro($mensagem)->flash();
            
            $this->erro->limparErro();

            return false;
        }

        $this->id = $this->conection->lastInsertId();
        
        return true;
    }

        /**
     * Orquestra a atualização de um registro existente no banco de dados.
     * 
     * Chamado internamente pelo método salvar(). Após a atualização,
     * sincroniza o objeto atual com os dados recém-salvos no banco de dados.
     *
     * @return bool True em caso de sucesso, false se houver falha (com mensagem flash).
     * @see salvar()
     * @see atualizar()
     * @see buscarPorId()
     */
    private function executarAtualizacao(): bool
    {
        if (!$this->atualizar($this->armazenar(), "id = :id", ['id' => $this->id])) {
            $mensagem = $this->erro->obter();
            $this->mensagem->erro($mensagem)->flash();
            return false;
        }
        
        $atualizado = $this->buscarPorId($this->id);
        if ($atualizado) {
            $this->dados = $atualizado->dados();
        }

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
