<?php

// 1. Oculta avisos e warnings do PHP 8 no ambiente
error_reporting(0);
ini_set('display_errors', '0');

$jsonFile = __DIR__ . '/phpcs_output.json';
$mdFile   = __DIR__ . '/relatorio.md';

// 2. Executa o PHPCS descartando os warnings de stdErr (2> NUL no Windows)
exec('.\\vendor\\bin\\phpcs.bat --report=json --report-file=' . escapeshellarg($jsonFile) . ' 2> NUL');

if (!file_exists($jsonFile)) {
    exit("Erro ao gerar o arquivo de auditoria.\n");
}

$rawJson = file_get_contents($jsonFile);
$data    = json_decode($rawJson, true);

// Limpa o arquivo JSON temporário
if (file_exists($jsonFile)) {
    unlink($jsonFile);
}

if (!$data) {
    exit("Não foi possível ler os dados do relatório.\n");
}

$totals = $data['totals'];
$files  = $data['files'];

// 3. Monta o documento em Markdown limpo
$md  = "# 📊 Relatório de Auditoria de Código (PSR-12)\n\n";
$md .= "> **Projeto:** `ProjetoFabrica`  \n";
$md .= "> **Data de Geração:** " . date('d/m/Y H:i:s') . "\n\n";

$md .= "## 📈 Resumo Geral\n\n";
$md .= "| 🚨 Erros | ⚠️ Avisos | 🛠️ Erros Corrigíveis | 📁 Arquivos Analisados |\n";
$md .= "| :---: | :---: | :---: | :---: |\n";
$md .= sprintf(
    "| **%d** | **%d** | **%d** | **%d** |\n\n",
    $totals['errors'],
    $totals['warnings'],
    $totals['fixable'],
    count($files)
);

if ($totals['errors'] === 0 && $totals['warnings'] === 0) {
    $md .= "### ✨ Nenhum problema encontrado! Seu código está 100% aderente à PSR-12.\n";
} else {
    $md .= "## 🔍 Detalhes por Arquivo\n\n";

    foreach ($files as $filePath => $info) {
        if ($info['errors'] === 0 && $info['warnings'] === 0) {
            continue;
        }

        // Limpa o caminho do arquivo para exibição
        $relativePath = str_replace('\\', '/', $filePath);
        if (strpos($relativePath, 'projetofabrica/') !== false) {
            $relativePath = explode('projetofabrica/', $relativePath)[1];
        }

        $md .= "### 📄 `" . htmlspecialchars($relativePath) . "`\n";
        $md .= "**Erros:** " . $info['errors'] . " | **Avisos:** " . $info['warnings'] . "\n\n";

        $md .= "| Linha | Coluna | Tipo | Regra | Mensagem |\n";
        $md .= "| :---: | :---: | :---: | :--- | :--- |\n";

        foreach ($info['messages'] as $msg) {
            $badge   = ($msg['type'] === 'ERROR') ? '🔴 ERRO' : '🟡 AVISO';
            $fixable = $msg['fixable'] ? ' 🛠️' : '';

            $parts     = explode('.', $msg['source']);
            $ruleShort = end($parts);

            $md .= sprintf(
                "| %d | %d | %s | `%s` | %s%s |\n",
                $msg['line'],
                $msg['column'],
                $badge,
                htmlspecialchars($ruleShort),
                htmlspecialchars($msg['message']),
                $fixable
            );
        }
        $md .= "\n---\n\n";
    }
}

file_put_contents($mdFile, $md);
echo "Relatório atualizado com sucesso em relatorio.md!\n";
