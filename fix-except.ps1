param (
    [Parameter(Mandatory=$true)]
    [string]$Ignorar
)

Write-Host "🔧 Corrigindo projeto com PHPCBF ignorando: $Ignorar..." -ForegroundColor Yellow

# Executa o PHPCBF ignorando o parâmetro informado
.\vendor\bin\phpcbf.bat --ignore=$Ignorar

Write-Host "✅ Correção concluída!" -ForegroundColor Green