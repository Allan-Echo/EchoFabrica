<?php

namespace sistema\modelo;

use sistema\nucleo\Modelo;

/**
 * Modelo responsável pelo relacionamento entre layouts e máquinas.
 *
 * Cada registro da tabela `layout_machine` associa um layout (`fk_id_layout`)
 * a uma máquina (`fk_id_machine`). Esta classe também reúne as operações para
 * cadastrar um layout com seus vínculos e consultar as máquinas associadas
 * a um layout.
 *
 * @property string $queryBase Consulta INSERT usada para criar os vínculos.
 * @property string $colunaDependente Nome da coluna que armazena o ID da máquina.
 * @property string $colunaPrimaria Nome da coluna que armazena o ID do layout.
 * @property list<int>|false $valoresDependentes IDs das máquinas a serem vinculadas.
 * @property int $fk_id_layout ID do layout criado.
 * @property list<int>|false $fk_id_machine Primeiro ID de máquina informado ao cadastrar.
 */
class LayoutMaquina extends Modelo
{
    public function __construct()
    {
        parent::__construct('layout_machine', 'fk_id_layout, fk_id_machine');
    }

    /**
     * Cadastra um layout e cria os vínculos entre ele e as máquinas informadas.
     *
     * O cadastro do layout e dos vínculos é executado na mesma transação: se
     * alguma gravação falhar, as alterações são revertidas. Após o cadastro,
     * esta instância é atualizada com o ID do layout criado e os IDs das máquinas informado.
     *
     * Exemplo:
     * ```php
     * $layoutMaquina->montarLayout($layout, [
     *     'fk_id_machine' => [1, 2, 3],
     * ]);
     * ```
     *
     * @param Layout $layout Objeto que contém dados do layout que será cadastrado.
     * @param array{fk_id_machine: list<int>} $maquinas IDs das máquinas que serão vinculadas ao layout.
     * @return $this A mesma instância, atualizada com os dados do vínculo cadastrado.
     */
    public function montarLayout(Layout $layout, array $maquinas): static
    {
        $cadastroLayout = $layout->prepararCadastro()->query;
        $moldeLayoutMaquina = $this->montarMolde($maquinas);

        $id_layout = $this->conection->insertComDependencia($cadastroLayout, $layout->dadosComoArray(), $moldeLayoutMaquina);

        $this->limparDados();
        $this->fk_id_layout = $id_layout;
        $this->fk_id_machine = current($maquinas);
        return $this;

    }

    /**
     * Prepara a consulta e os dados usados para vincular máquinas a um layout.
     *
     * Define a consulta INSERT para a tabela de relacionamento e configura
     * nesta instância o nome da coluna dependente, os IDs das máquinas e a
     * coluna que receberá o ID do layout. A gravação dos vínculos é feita
     * posteriormente por `montarLayout`, dentro da transação de cadastro.
     *
     * Exemplo de entrada:
     * ```php
     * [
     *     'fk_id_machine' => [1, 2, 3],
     * ]
     * ```
     *
     * @param array{fk_id_machine: list<int>} $maquinas IDs das máquinas que serão vinculadas ao layout.
     * @return $this A mesma instância, configurada para a gravação dos vínculos.
     * @see montarLayout() Executa o cadastro do layout e dos vínculos preparados.
     */
    private function montarMolde(array $maquinas): static
    {
        $placeholdersNomeados = ':' . str_replace(', ', ', :', $this->colunas);
        $this->queryBase =
        "INSERT INTO {$this->tabela} ({$this->colunas}) VALUES ({$placeholdersNomeados})";

        $this->colunaDependente = implode(array_keys($maquinas));
        $this->valoresDependentes = current($maquinas);
        $this->colunaPrimaria = 'fk_id_layout';

        return $this;
    }

    /**
     * Busca as máquinas associadas a um layout.
     *
     * Consulta os vínculos da tabela `layout_machine`, obtém os dados de cada
     * máquina correspondente e ordena os resultados pelo ID da máquina em
     * ordem crescente. O método é mantido por compatibilidade com os pontos
     * da aplicação que ainda utilizam esta consulta legada.
     *
     * Exemplo:
     * ```php
     * $maquinas = $layoutMaquina->buscarLayoutMachine('12');
     * // [
     * //     ['id_machine' => 1, 'model' => '...', 'designation' => '...'],
     * //     ['id_machine' => 3, 'model' => '...', 'designation' => '...'],
     * // ]
     * ```
     *
     * @param string $layout ID do layout usado para localizar as máquinas associadas.
     * @return list<array{id_machine: int|string, model: string, designation: string}> Máquinas encontradas, cada uma com seu ID, modelo e designação.
     */
    public function buscarLayoutMachine(string $layout): array
    {
        $query = "SELECT m.id_machine, m.model, m.designation FROM {$this->tabela} AS lm JOIN machine AS m ON lm.fk_id_machine = m.id_machine WHERE fk_id_layout = $layout 
        ORDER BY fk_id_machine ASC";

        return $this->conection->select($query);
    }
}
