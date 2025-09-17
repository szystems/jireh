# ==========================================
# SCRIPT DE PREPARACIÓN PARA DESPLIEGUE EN IPAGE
# ==========================================

echo "PREPARANDO ARCHIVOS PARA IPAGE..."
echo "=================================="

# 1. Crear respaldo de archivos originales
Write-Host "1. Creando respaldos..." -ForegroundColor Yellow
if (Test-Path ".env") {
    Copy-Item ".env" ".env.backup" -Force
    Write-Host "   ✓ Respaldo de .env creado" -ForegroundColor Green
}

if (Test-Path "public\.htaccess") {
    Copy-Item "public\.htaccess" "public\.htaccess.backup" -Force
    Write-Host "   ✓ Respaldo de .htaccess creado" -ForegroundColor Green
}

if (Test-Path "public\index.php") {
    Copy-Item "public\index.php" "public\index.php.backup" -Force
    Write-Host "   ✓ Respaldo de index.php creado" -ForegroundColor Green
}

if (Test-Path "config\database.php") {
    Copy-Item "config\database.php" "config\database.php.backup" -Force
    Write-Host "   ✓ Respaldo de database.php creado" -ForegroundColor Green
}

# 2. Aplicar configuraciones para iPage
Write-Host "`n2. Aplicando configuraciones para iPage..." -ForegroundColor Yellow

# Copiar .env optimizado
if (Test-Path ".env_ipage_optimizado") {
    Copy-Item ".env_ipage_optimizado" ".env" -Force
    Write-Host "   ✓ Archivo .env actualizado para iPage" -ForegroundColor Green
} else {
    Write-Host "   ✗ No se encontró .env_ipage_optimizado" -ForegroundColor Red
}

# Copiar .htaccess optimizado
if (Test-Path "public\.htaccess_ipage_basico") {
    Copy-Item "public\.htaccess_ipage_basico" "public\.htaccess" -Force
    Write-Host "   ✓ Archivo .htaccess actualizado para iPage" -ForegroundColor Green
} else {
    Write-Host "   ✗ No se encontró .htaccess_ipage_basico" -ForegroundColor Red
}

# Copiar index.php optimizado
if (Test-Path "public\index_ipage.php") {
    Copy-Item "public\index_ipage.php" "public\index.php" -Force
    Write-Host "   ✓ Archivo index.php actualizado para iPage" -ForegroundColor Green
} else {
    Write-Host "   ✗ No se encontró index_ipage.php" -ForegroundColor Red
}

# Copiar database.php optimizado
if (Test-Path "config\database_ipage.php") {
    Copy-Item "config\database_ipage.php" "config\database.php" -Force
    Write-Host "   ✓ Archivo database.php actualizado para iPage" -ForegroundColor Green
} else {
    Write-Host "   ✗ No se encontró database_ipage.php" -ForegroundColor Red
}

# 3. Verificar archivos críticos
Write-Host "`n3. Verificando archivos críticos..." -ForegroundColor Yellow

$archivos_criticos = @(
    "vendor\autoload.php",
    "bootstrap\app.php",
    ".env",
    "public\index.php",
    "public\.htaccess",
    "config\database.php"
)

foreach ($archivo in $archivos_criticos) {
    if (Test-Path $archivo) {
        Write-Host "   ✓ $archivo" -ForegroundColor Green
    } else {
        Write-Host "   ✗ $archivo NO ENCONTRADO" -ForegroundColor Red
    }
}

# 4. Mostrar archivos de diagnóstico disponibles
Write-Host "`n4. Archivos de diagnóstico disponibles:" -ForegroundColor Yellow

$archivos_diagnostico = @(
    "public\diagnostico_ipage.php",
    "public\crear_tabla_sesiones.php",
    "public\optimizar_ipage.php"
)

foreach ($archivo in $archivos_diagnostico) {
    if (Test-Path $archivo) {
        Write-Host "   ✓ $archivo" -ForegroundColor Green
    } else {
        Write-Host "   ✗ $archivo NO ENCONTRADO" -ForegroundColor Red
    }
}

# 5. Mostrar instrucciones finales
Write-Host "`n=================================="
Write-Host "PREPARACIÓN COMPLETADA" -ForegroundColor Green
Write-Host "=================================="

Write-Host "`nPRÓXIMOS PASOS:" -ForegroundColor Cyan
Write-Host "1. Subir TODA la carpeta del proyecto a iPage en:" -ForegroundColor White
Write-Host "   https://szystems.com/jirehsoft/" -ForegroundColor Yellow

Write-Host "`n2. Una vez subido, ejecutar en el navegador:" -ForegroundColor White
Write-Host "   https://szystems.com/jirehsoft/public/diagnostico_ipage.php" -ForegroundColor Yellow
Write-Host "   (Para ver qué está fallando específicamente)" -ForegroundColor Gray

Write-Host "`n3. Después ejecutar:" -ForegroundColor White
Write-Host "   https://szystems.com/jirehsoft/public/crear_tabla_sesiones.php" -ForegroundColor Yellow
Write-Host "   (Para configurar las sesiones en la base de datos)" -ForegroundColor Gray

Write-Host "`n4. Finalmente probar:" -ForegroundColor White
Write-Host "   https://szystems.com/jirehsoft/public/" -ForegroundColor Yellow
Write-Host "   (Tu aplicación debería funcionar)" -ForegroundColor Gray

Write-Host "`nNOTAS IMPORTANTES:" -ForegroundColor Red
Write-Host "- Los respaldos están guardados con extensión .backup" -ForegroundColor White
Write-Host "- Si algo falla, puedes restaurar los archivos originales" -ForegroundColor White
Write-Host "- Revisa el diagnóstico antes de reportar problemas" -ForegroundColor White

Write-Host "`n¿Estás listo para subir los archivos a iPage? (S/N): " -ForegroundColor Green -NoNewline
$respuesta = Read-Host
if ($respuesta -eq "S" -or $respuesta -eq "s") {
    Write-Host "`n¡Perfecto! Procede con la subida de archivos." -ForegroundColor Green
} else {
    Write-Host "`nOK, puedes subir los archivos cuando estés listo." -ForegroundColor Yellow
}
