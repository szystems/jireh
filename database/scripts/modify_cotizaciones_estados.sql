-- Script para modificar la columna estado de la tabla cotizaciones
-- Agregar los nuevos estados 'Generado' y 'Aprobado'
-- Ejecutar este script directamente en MySQL

-- Paso 1: Modificar la columna estado para incluir los nuevos valores
ALTER TABLE `cotizaciones` 
MODIFY COLUMN `estado` ENUM('vigente','vencida','aprobada','rechazada','convertida','Generado','Aprobado') 
NOT NULL DEFAULT 'Generado';

-- Paso 2: Actualizar registros existentes
-- Cambiar todas las cotizaciones que están como 'vigente' a 'Generado'
UPDATE `cotizaciones` SET `estado` = 'Generado' WHERE `estado` = 'vigente';

-- Paso 3: Cambiar todas las cotizaciones que están como 'aprobada' a 'Aprobado'
UPDATE `cotizaciones` SET `estado` = 'Aprobado' WHERE `estado` = 'aprobada';

-- Verificar los cambios
SELECT estado, COUNT(*) as cantidad FROM cotizaciones GROUP BY estado;