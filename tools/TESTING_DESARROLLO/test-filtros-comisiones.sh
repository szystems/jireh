#!/bin/bash

echo "=== PRUEBA DE FILTROS DE COMISIONES ==="
echo "Probando filtros implementados en el sistema de gestión de comisiones"
echo ""

# 1. Filtro por trabajador específico
echo "1. Probando filtro por trabajador específico..."
curl -s -G "http://localhost:8000/comisiones/gestion/todas" \
  --data-urlencode "trabajador_id=1" \
  -H "Accept: application/json" \
  | jq '.comisiones[0:2] | {total: length, primeras_comisiones: .}' 2>/dev/null || echo "Error en respuesta JSON"

echo ""

# 2. Filtro por tipo de trabajador
echo "2. Probando filtro por tipo de trabajador (mecánico)..."
curl -s -G "http://localhost:8000/comisiones/gestion/todas" \
  --data-urlencode "tipo_trabajador=mecanico" \
  -H "Accept: application/json" \
  | jq '.comisiones[0:2] | {total: length, primeras_comisiones: .}' 2>/dev/null || echo "Error en respuesta JSON"

echo ""

# 3. Filtro por vendedor específico
echo "3. Probando filtro por vendedor específico..."
curl -s -G "http://localhost:8000/comisiones/gestion/todas" \
  --data-urlencode "vendedor_id=1" \
  -H "Accept: application/json" \
  | jq '.comisiones[0:2] | {total: length, primeras_comisiones: .}' 2>/dev/null || echo "Error en respuesta JSON"

echo ""

# 4. Filtro por período de meta
echo "4. Probando filtro por período de meta (mensual)..."
curl -s -G "http://localhost:8000/comisiones/gestion/todas" \
  --data-urlencode "periodo_meta=mensual" \
  -H "Accept: application/json" \
  | jq '.comisiones[0:2] | {total: length, primeras_comisiones: .}' 2>/dev/null || echo "Error en respuesta JSON"

echo ""

# 5. Combinación de filtros
echo "5. Probando combinación de filtros (estado + tipo_comision)..."
curl -s -G "http://localhost:8000/comisiones/gestion/todas" \
  --data-urlencode "estado=pendiente" \
  --data-urlencode "tipo_comision=venta_meta" \
  -H "Accept: application/json" \
  | jq '.estadisticas' 2>/dev/null || echo "Error en respuesta JSON"

echo ""
echo "=== PRUEBA COMPLETADA ==="
