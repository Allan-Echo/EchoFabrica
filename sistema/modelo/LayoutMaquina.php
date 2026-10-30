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
    protected ?string $colunas = 'fk_id_layout, fk_id_machine';

    public function __construct()
    {
        parent::__construct('layout_machine');
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
        $cadastroLayout = $layout->prepararCadastro()->obterQuery();

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

    public function removerMaquina(int $maquinaId)//: bool
    {
        $this->verificarExistenciaMaquina($maquinaId);

        if (!$this->existeAssociacao($maquinaId)) {
            $this->hardDelete($maquinaId);
        } else {
            $layoutsAfetadosIds = $this->obterLayoutsAfetados($maquinaId);
        }
    }

    private function verificarExistenciaMaquina(int $maquinaId): true
    {
        $selectQuery = "SELECT id FROM machine WHERE id = :id";

        $registro = $this->conection->select($selectQuery, ['id' => $maquinaId]);

        if (empty($registro)) {
            throw new \InvalidArgumentException("Máquina sem cadastro existente");
        }

        return true;
    }

    private function existeAssociacao(int $maquinaId): bool
    {
        $selectQuery = "SELECT fk_id_machine FROM layout_machine WHERE fk_id_machine =:id AND deleted_at IS NULL";

        $registro = $this->conection->select($selectQuery, ['id' => $maquinaId]);

        if (empty($registro)) {
            return false;
        }

        return true;
    }

    private function hardDelete(int $maquinaId): void
    {
        $deleteQuery = "DELETE FROM machine WHERE id = :id";

        $this->conection->delete($deleteQuery, ['id' => $maquinaId]);
    }

    private function obterLayoutsAfetados(int $maquinaId): array
    {
        $selectQuery = "SELECT fk_id_layout FROM layout_machine WHERE fk_id_machine = :id AND deleted_at IS NULL";

        $resultado = $this->conection->select($selectQuery, ['id' => $maquinaId]);

        return array_column($resultado, 'fk_id_layout');
    }

    private function atualizarRegistroPai(int $maquinaId): void
    {
        $updateQuery = "UPDATE machine SET deleted_at = NOW() WHERE id = :id";

        $this->conection->update($updateQuery, ['id' => $maquinaId]);
    }

    private function atualizarMaquinasAssociados(int $maquinaId): void
    {
        $updateQuery = "UPDATE layout_machine SET delete_at = NOW() WHERE fk_id_machine = :id";

        $this->conection->update($updateQuery, ['id' => $maquinaId]);
    }

    private function obterRegistrosAtualizados(array $layoutsAfetadosIds): array
    {
        $layoutsIds =  implode(',', $layoutsAfetadosIds);

        $selectQuery = "SELECT * FROM wv_layout_ativos WHERE fk_id_layout IN ($layoutsIds)";

        return $this->conection->select($selectQuery);
    }

    private function atualizarLayoutsAssociados(array $layoutsAfetadosIds): void
    {
        $layoutsIds = implode(',', $layoutsAfetadosIds);

        $updateQuery = "UPDATE layout_machine SET deleted_at = NOW() WHERE fk_id_layout IN ({$layoutsIds})";

        $this->conection->update($updateQuery);
    }

    private function versionarLayoutMachine(): void
    {

    }
}
