# ==========================================
# GESTOR DE CONFIGURACIONES - LOCAL vs IPAGE
# ==========================================

param(
    [Parameter(Mandatory=$true)]
    [ValidateSet("local", "ipage")]
    [string]$Modo
)

Write-Host "CONFIGURANDO PROYECTO PARA: $Modo" -ForegroundColor Cyan
Write-Host "================================" -ForegroundColor Cyan

if ($Modo -eq "local") {
    Write-Host "`nConfigurando para DESARROLLO LOCAL..." -ForegroundColor Yellow
    
    # Aplicar configuración local
    if (Test-Path ".env_local") {
        Copy-Item ".env_local" ".env" -Force
        Write-Host "✓ Archivo .env configurado para desarrollo local" -ForegroundColor Green
    }
    
    # Restaurar database.php para local
    if (Test-Path "config\database_local.php") {
        Copy-Item "config\database_local.php" "config\database.php" -Force
        Write-Host "✓ Configuración de base de datos restaurada para local" -ForegroundColor Green
    }
    
    # Limpiar cache
    Write-Host "`nLimpiando cache..." -ForegroundColor Yellow
    & php artisan config:clear
    & php artisan cache:clear
    & php artisan view:clear
    
    Write-Host "`n✅ CONFIGURACIÓN LOCAL APLICADA" -ForegroundColor Green
    Write-Host "Puedes ejecutar: php artisan serve" -ForegroundColor White
    
} elseif ($Modo -eq "ipage") {
    Write-Host "`nConfigurando para PRODUCCIÓN IPAGE..." -ForegroundColor Yellow
    
    # Crear respaldos
    if (Test-Path ".env") {
        Copy-Item ".env" ".env.local.backup" -Force
        Write-Host "✓ Respaldo de .env local creado" -ForegroundColor Green
    }
    
    # Aplicar configuración iPage
    if (Test-Path ".env_ipage_optimizado") {
        Copy-Item ".env_ipage_optimizado" ".env" -Force
        Write-Host "✓ Archivo .env configurado para iPage" -ForegroundColor Green
    }
    
    if (Test-Path "config\database_ipage.php") {
        Copy-Item "config\database_ipage.php" "config\database.php" -Force
        Write-Host "✓ Configuración de base de datos para iPage aplicada" -ForegroundColor Green
    }
    
    if (Test-Path "public\.htaccess_ipage_basico") {
        Copy-Item "public\.htaccess_ipage_basico" "public\.htaccess" -Force
        Write-Host "✓ Archivo .htaccess optimizado para iPage aplicado" -ForegroundColor Green
    }
    
    if (Test-Path "public\index_ipage.php") {
        Copy-Item "public\index_ipage.php" "public\index.php" -Force
        Write-Host "✓ Archivo index.php optimizado para iPage aplicado" -ForegroundColor Green
    }
    
    # Limpiar cache
    Write-Host "`nLimpiando cache..." -ForegroundColor Yellow
    & php artisan config:clear
    & php artisan cache:clear
    & php artisan view:clear
    
    # Optimizar para producción
    Write-Host "`nOptimizando para producción..." -ForegroundColor Yellow
    & composer dump-autoload -o
    
    Write-Host "`n✅ CONFIGURACIÓN IPAGE APLICADA" -ForegroundColor Green
    Write-Host "El proyecto está listo para subir a iPage" -ForegroundColor White
    Write-Host "`nArchivos de diagnóstico disponibles:" -ForegroundColor Cyan
    Write-Host "- public\diagnostico_ipage.php" -ForegroundColor White
    Write-Host "- public\crear_tabla_sesiones.php" -ForegroundColor White
    Write-Host "- public\optimizar_ipage.php" -ForegroundColor White
}

Write-Host "`n================================" -ForegroundColor Cyan
Write-Host "CONFIGURACIÓN COMPLETADA" -ForegroundColor Green
Write-Host "================================" -ForegroundColor Cyan
