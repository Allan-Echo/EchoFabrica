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
     * @var string Nome das colunas para modelos que não recebem o nome das colunas dinamicamente
     */
    protected ?string $colunas;

    /**
     * @var mixed Armazena os dados do registro (geralmente um stdClass).
     */
    protected array|\stdClass $dados;

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
    public function __construct(string $tabela, ?string $colunas = null)
    {
        $this->conection = new EasyPDO();
        $this->mensagem = new Mensagem();
        $this->erro = new Erro();
        $this->tabela = $tabela;
        $this->colunas = $colunas;
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
     * @param string|array|null $parametros Query string de parâmetros (ex: "id=1&status=ativo") ou um array associativo (ex: ['id' => 1, 'status' => 'ativo']).
     * @param string $coluna Colunas a selecionar (padrão: "*").
     * @return static Retorna a própria instância para encadeamento.
     * @see ordenar() Para definir a ordem dos resultados.
     * @see limitar() Para definir paginação.
     * @see resultado() Para executar a query e obter os dados.
     */
    public function buscar(?string $where = null, string|array|null $parametros = null, bool $ativos = true, string $coluna = '*'): static
    {
        $this->query = "SELECT {$coluna} FROM " . $this->tabela;

        $this->tiposDeRegistros($ativos);

        if (!empty($where)) {
            $this->query .= " AND {$where}";

            if (is_string($parametros)) {
                parse_str($parametros, $this->parametros);
            } elseif (is_array($parametros)) {
                $this->parametros = $parametros;
            }
        }

        return $this;
    }

    private function tiposDeRegistros(bool $ativo = true): static
    {
        if ($ativo) {
            $this->query .= " WHERE deleted_at IS NULL";
        } else {
            $this->query .= " WHERE deleted_at IS NOT NULL";
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
    public function resultado(string $estrutura = 'objeto'): array
    {
        $query = $this->query;
        $estrutura = strtolower($estrutura);


        if (!empty($this->ordem)) {
            $query .= " ORDER BY {$this->ordem}";
        }

        if (!empty($this->limite)) {
            $query .= " LIMIT {$this->limite} OFFSET {$this->offset}";
        }

        // Ao invés de retornar o objeto inteiro, seria melhor retonar um \stdClass para não expor métodos internos do Modelo. Isso mantém o encapsulamento e a segurança. Ou poderia retornar um array de arrays associativos, não pasasndo o argumento static::class, mas isso depende de como você quer que os dados sejam manipulados fora do modelo.
        $resultado = match ($estrutura) {
            'objeto'      => $this->conection->select($query, $this->parametros, static::class),
            'associativo' => $this->conection->select($query, $this->parametros),
            default       => throw new \InvalidArgumentException("Estrutura inválida: {$estrutura}. Use 'objeto' ou 'associativo'."),
        };

        if ($estrutura === 'objeto' && is_array($resultado)) {
            foreach ($resultado as $indice => $objeto) {
                $resultado[$indice] = $objeto->getDados();
            }
        }

        return $resultado ?? [];

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
     *  @throws \Throwable
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
        return $this->cadastrar($this->dadosComoArray());
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
        $this->atualizar($this->dadosComoArray(), 'id = :id', ['id' => $this->id]);

        $atualizado = $this->buscarPorId($this->id);
        if ($atualizado) {
            $this->dados = $atualizado->getDados();
        }

        return true;
    }


    /**
     * Atualiza registros na tabela do banco de dados.
    *
    * @param array $dados Dados associativos para atualização (coluna => valor).
    * @param string $where Condição para atualização (ex: "id = :id").
    * @param array $parametros Valores dos parâmetros da condição WHERE.
    * @return bool True em caso de sucesso, false em caso de falha.
    */
    protected function atualizar(array $dados, string $where, array $parametros): bool
    {
        try {
            $this->erro->limparErro();

            $assignments = [];
            foreach ($dados as $campo => $valor) {
                $assignments[] = "{$campo} = :{$campo}";
            }
            $setters = implode(', ', $assignments);
            $query = "UPDATE {$this->tabela} SET {$setters} WHERE {$where}";

            $this->conection->update($query, array_merge($dados, $parametros));

            return true;
        } catch (\Throwable $e) {
            $this->erro->definirMensagem('Erro de sistema ao atualizar dados.');
            throw $e;
        }
    }

    /**
     * Insere um novo registro na tabela do banco de dados.
     *
     * Método protegido que prepara e executa a inserção. Realiza a limpeza de erros prévios,
     * durante a execução da query INSERT.
     * @param array $dados Dados associativos (coluna => valor) para inserção.
     * @return bool Retorna true em caso de sucesso ou false em caso de falha.
     * @see Erro::limparErro() Reseta o estado de erro antes da operação.
    */
    protected function cadastrar(array $dados): bool
    {

        try {
            $this->erro->limparErro();

            $query = $this->prepararCadastro($dados)->query;

            $this->id = $this->conection->insertComUltimoId($query, $dados);

            return true;
        } catch (\Throwable $e) {
            $this->erro->definirMensagem('Falha ao inserir no banco.');
            throw $e;
        }
    }

    protected function prepararCadastro(?array $dados = null): static
    {
        if ($dados === null) {
            $dados = $this->dadosComoArray();
        }

        $colunas = implode(', ', array_keys($dados));
        $valores = ':' . implode(', :', array_keys($dados));
        $this->query = "INSERT INTO {$this->tabela} ({$colunas}) VALUES ({$valores})";
        return $this;
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
    public function buscarPorId(int $id, string $estrutura = 'objeto'): \stdClass|array|null
    {
        $resultado = $this->buscar('id = :id', ["id" => $id])->resultado($estrutura);
        return !empty($resultado) ? $resultado[0] : null;
    }

    /**
     * Marca um registro como excluído sem removê-lo fisicamente do banco de dados.
     * Em caso de falha, registra uma mensagem de feedback em $this->erro e relança a exceção.
     *
     * @param int $id Identificador do registro a marcar como excluído
     *
     * @return bool True quando a atualização é executada com sucesso
     *
     * @throws \PDOException Se ocorrer uma falha ao atualizar o registro
     */
    public function deletar(int $maquinaId, ?Modelo $tabelaIntermediaria = null, ?Modelo $tabelaRelacionada = null, ?array $colunasRelacionadas = null): bool
    {
        try {
            $this->erro->limparErro();

            $query = "DELETE FROM {$this->tabela} WHERE id = :id";
            $this->conection->delete($query, ['id' => $maquinaId]);

            return true;
        } catch (\PDOException $hardDelete) {
            // é útil definir uma mensagem de erro para feedback, mesmo que a exceção seja lançada?
            $this->erro->definirMensagem('Delete físico falhou (provavelmente por relacionamentos). Tentando Soft Delete...\n');

            try {
                //$relacionamento = [layout_machine = fk_id_machine] ou o inverso?
                //$tabelaIntermediaria = 'layout_machine'
                //$colunaIdMaquina = 'fk_id_machine'
                //colunasRelacionadas$colunasRelacionadas$colunasRelacionadas = 'fk_id_layout'
                // id é a chave primária da tabela atual e precisa ser passada como parâmetro quando for executada a query

                $nometabelaIntermediaria = $tabelaIntermediaria->getNomeTabela();
                $colunaChavesIntermediarias = $tabelaIntermediaria->getColunas();
                $colunaIdMaquina =
                Helpers::extrairTrecho($colunaChavesIntermediarias, 'fk_id_machine,', ','); // precisa de selectQuery
                $colunaIdLayout = 'fk_id_layout'; // precisa de selectQuery
                $nometabelaRelacionada = $tabelaRelacionada->getNomeTabela();
                $colunasRelacionadas = 'id, denomination, observation'; // precisa de selectQuery
                $colunaIdRelacionada = Helpers::extrairTrecho($colunasRelacionadas, 'id', ',');
                $this->selectQuerys = [];
                $this->updateQuerys = [];
                $this->insertQuerys = [];
                $this->selectRespostas = ['idsCampoRelacionado' => [], 'GetRegistrosAtualizados' => []];
                //o campo de referência é a coluna da tabela relacionada que aponta para os registro mais importantes, ou seja, os fk_id da tabela layout. Já os $registrosDependetes são os ids da tabela layout.
                //passo 2 [fk_id_layout =[ 1,2,3]] configurar PDO para columnar os resultados e retornar um array de valores, não um array de arrays associativos
                $this->selectQuerys['idsCampoRelacionado'] =
                [
                    'query' => "SELECT {$colunaIdLayout} FROM {$tabelaIntermediaria} WHERE {$colunaIdMaquina} = :{$colunaIdMaquina}",
                    'parametros' => [$colunaIdMaquina => $maquinaId]
                ];

                $this->idsCampoRelacionado = $selectRespostas['idsCampoRelacionado'] ?? null;

                $this->updateQuerys['deletarRegistroPai'] =
                [
                    'query' => "UPDATE {$this->tabela} SET deleted_at = NOW() WHERE id = :id",
                    'parametros' => ['id' => $maquinaId]
                ];

                $this->updateQuerys['deletarRegistrosFilhos'] =
                [
                    'query' => "UPDATE {$tabelaIntermediaria} SET deleted_at = NOW() WHERE {$colunaIdMaquina} = :{$colunaIdMaquina}",
                    'parametros' => [$colunaIdMaquina => $maquinaId]
                ]; // passo 3

                //Isso é um array
                //necessário retirar id dos dados
                //melhor trazer os dados em colunas e retirar a coluna id do array, para não precisar fazer um foreach e retirar o id de cada registro. Apenas tentar usar o array_column com array_map para trazer apenas os valores da coluna desejada. Isso evita a necessidade de percorrer cada registro e remover o id manualmente, tornando o código mais eficiente e limpo.
                $idsCampoRelacionado = implode(', ', $this->selectRespostas['idsCampoRelacionado'] ?? []);

                // passo 4, buscar os dados atualizados para recadastrar os registros filhos que não foram deletados para criar um novo registro na tabela relacionada, mantendo a integridade referencial. Isso pode envolver a criação de uma nova entrada na tabela relacionada com os mesmos dados, mas com um novo ID, ou a restauração de registros previamente marcados como deletados.
                $this->selectQuerys['GetRegistrosAtualizados'] =
                ['query' => "SELECT * FROM 'vw_layouts_ativos' WHERE {$colunaIdLayout} IN ({$this->idsCampoRelacionado})"
                ];

                $this->updateQuerys['deletarRegistrosAssociados'] = // passo 5
                ['query' => "UPDATE {$tabelaIntermediaria} SET deleted_at = NOW() WHERE {$colunaIdLayout} IN ({$idsCampoRelacionado})"
                ];

                $this->updateQuerys['deletarEmTabelaRelacionada'] =
                [
                    'query' => "UPDATE {$nometabelaRelacionada} SET deleted_at = NOW() WHERE {$colunaIdRelacionada} IN ({$idsCampoRelacionado})"
                ];

                $this->insertQuerys['layoutAtualizado'] =
                [
                    'query' => "INSERT "
                ];

                $this->conection->softDelete($this->updateQuerys, ['id' => $maquinaId, $colunaIdMaquina => $maquinaId]);

                return true;
            } catch (\PDOException $softDelete) {
                $this->erro->definirMensagem('Soft Delete falhou. Registro não foi excluído.');
                throw $softDelete;
            }
        }
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
    * @see dadosComoArray() Converte dados dinâmicos em array
    * @see salvar() Usa dadosComoArray() para preparar dados
     */
    public function __set(string $name, mixed $value): void
    {
        if (empty($this->dados)) {
            $this->dados = new \stdClass();
        }
        $this->dados->$name = $value;
    }

    /**
     * Converte dados dinâmicos em array associativo e sanitizado.
     *
     * Funciona como adaptador entre o objeto $this->dados (criado via __set)
     * e os métodos cadastrar() e atualizar() que requerem arrays.
     *
     * @return array Array associativo com dados sanitizados
     * @see __set() Cria os dados dinâmicos
     * @see salvar() Chama este método para preparar dados
     */
    protected function dadosComoArray(): array
    {
        return $dados = (array) $this->dados;
    }

    /**
     * Verifica se uma propriedade dinâmica existe no objeto de dados.
     *
     * @param string $campo Nome da propriedade
     * @return bool
     */
    public function __isset(string $campo): bool
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

    // ========================================================================
    // Métodos para avaliar se exiti utilidade
    // ========================================================================

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
    public function erro(): string
    {
        return $this->erro->mensagem;
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
    public function mensagemDeErro(string $mensagem): Mensagem
    {
        return $this->mensagem->erro($mensagem);
    }

    /**
     * Retorna os dados do objeto.
     *
     * @return mixed Os dados armazenados no objeto, geralmente um stdClass ou array de resultados.
     */
    protected function getDados(): mixed
    {
        return $this->dados;
    }

    protected function limparDados()
    {
        unset($this->dados);
    }

    protected function getNomeTabela(): string
    {
        return $this->tabela;
    }

    protected function getColunas(): string
    {
        return $this->colunas;
    }
}
