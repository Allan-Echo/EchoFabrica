param (
    [Parameter(Mandatory=$true)]
    [string]$Ignorar
)

# Força o PHP do processo atual e dos subprocessos a desligar o Xdebug
$env:XDEBUG_MODE="off"

Write-Host "==> Corrigindo projeto com PHPCBF ignorando: $Ignorar..." -ForegroundColor Yellow

# Executa o PHPCBF
.\vendor\bin\phpcbf.bat --ignore=$Ignorar

Write-Host "[OK] Correcao concluida com sucesso!" -ForegroundColor Green