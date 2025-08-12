#!/bin/bash

echo "=== VALIDACIÓN DE SINTAXIS JAVASCRIPT - GESTIÓN DE COMISIONES ==="

# Extraer solo el JavaScript del archivo Blade
sed -n '/<script>/,/<\/script>/p' resources/views/admin/comisiones/gestion.blade.php | \
sed 's/<script>//g' | \
sed 's/<\/script>//g' | \
sed 's/{{[^}]*}}/PLACEHOLDER/g' > temp_js_validation.js

echo "Archivo JavaScript extraído:"
echo "Líneas totales: $(wc -l < temp_js_validation.js)"

# Validar con Node.js si está disponible
if command -v node &> /dev/null; then
    echo "Validando sintaxis con Node.js..."
    if node -c temp_js_validation.js 2>/dev/null; then
        echo "✅ SINTAXIS JAVASCRIPT VÁLIDA"
    else
        echo "❌ ERROR EN SINTAXIS JAVASCRIPT:"
        node -c temp_js_validation.js
    fi
else
    echo "Node.js no disponible, validación manual..."
    
    # Contar llaves de apertura y cierre
    open_braces=$(grep -o '{' temp_js_validation.js | wc -l)
    close_braces=$(grep -o '}' temp_js_validation.js | wc -l)
    
    open_parens=$(grep -o '(' temp_js_validation.js | wc -l)
    close_parens=$(grep -o ')' temp_js_validation.js | wc -l)
    
    echo "Llaves de apertura { : $open_braces"
    echo "Llaves de cierre  } : $close_braces"
    echo "Paréntesis de apertura ( : $open_parens"
    echo "Paréntesis de cierre   ) : $close_parens"
    
    if [ "$open_braces" -eq "$close_braces" ] && [ "$open_parens" -eq "$close_parens" ]; then
        echo "✅ BALANCE DE LLAVES Y PARÉNTESIS CORRECTO"
    else
        echo "❌ DESEQUILIBRIO EN LLAVES O PARÉNTESIS"
    fi
fi

# Buscar posibles errores comunes
echo ""
echo "=== VERIFICACIÓN DE ERRORES COMUNES ==="

# Funciones sin cerrar
echo "Funciones definidas:"
grep -n "function " temp_js_validation.js | head -10

# Llaves sueltas al final
echo ""
echo "Últimas 10 líneas del JavaScript:"
tail -10 temp_js_validation.js

# Limpiar archivo temporal
rm -f temp_js_validation.js

echo ""
echo "=== VALIDACIÓN COMPLETADA ==="
