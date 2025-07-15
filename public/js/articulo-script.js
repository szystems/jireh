document.addEventListener('DOMContentLoaded', function () {
    console.log('Iniciando configuración unificada del formulario de artículo');

    // Referencias a elementos principales
    const tipoSelect = document.getElementById('tipo');
    const precioCompraInput = document.getElementById('precio_compra');
    const precioVentaInput = document.getElementById('precio_venta');
    const costoMecanicoInput = document.getElementById('costo_mecanico');
    const comisionCarwashInput = document.getElementById('comision_carwash');
    const seccionMecanico = document.getElementById('seccion-mecanico');
    const seccionComponentes = document.getElementById('seccion-componentes');
    const componentesTab = document.getElementById('componentes-tab');

    // Referencias para artículos de servicio - soporta tanto edit.blade.php como add.blade.php
    // Para edit.blade.php
    const nuevoArticuloSelect = document.getElementById('nuevo-articulo') || document.getElementById('articulo_id');
    const nuevaCantidadInput = document.getElementById('nuevo-cantidad') || document.getElementById('cantidad');
    const addArticuloBtn = document.getElementById('add-articulo-servicio') || document.getElementById('add-articulo');
    const servicioArticulosExistentesBody = document.getElementById('servicio-articulos-existentes-body');
    const servicioArticulosNuevosBody = document.getElementById('servicio-articulos-nuevos-body') || document.getElementById('servicio-articulos');
    const contadorExistentes = document.getElementById('contador-existentes') || document.getElementById('contador-articulos-agregados');
    const costoTotalElement = document.getElementById('costo-total');

    // Mapa para almacenar información de unidades de artículos seleccionados
    const unidadesPorArticulo = new Map();

    // Inicializar Select2 para mejores controles de selección
    if (typeof $ !== 'undefined') {
        // Para edit.blade.php
        if ($('#nuevo-articulo').length) {
            $('#nuevo-articulo').select2({
                placeholder: 'Buscar artículo por nombre o código',
                allowClear: true
            });
        }

        // Para add.blade.php
        if ($('#articulo_id').length) {
            $('#articulo_id').select2({
                placeholder: 'Buscar artículo por nombre o código',
                allowClear: true
            });
        }
    }

    // 1. CONFIGURAR VISIBILIDAD SEGÚN TIPO DE ARTÍCULO
    if (tipoSelect) {
        tipoSelect.addEventListener('change', function() {
            console.log('Tipo cambiado a:', this.value);

            if (this.value === 'servicio') {
                // Mostrar elementos para servicios
                if (seccionMecanico) seccionMecanico.style.display = 'block';
                if (seccionComponentes) seccionComponentes.style.display = 'block';

                console.log('Mostrando secciones para SERVICIO');

                // Hacer scroll automático hasta la sección de componentes
                setTimeout(() => {
                    if (seccionComponentes) {
                        seccionComponentes.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }, 300);

                // Si hay una pestaña de componentes, activarla
                if (componentesTab && typeof bootstrap !== 'undefined') {
                    try {
                        const componentesTabTrigger = new bootstrap.Tab(componentesTab);
                        componentesTabTrigger.show();
                    } catch(e) {
                        console.error('Error al activar pestaña:', e);
                    }
                }
            } else {
                // Ocultar elementos para artículos
                if (seccionMecanico) seccionMecanico.style.display = 'none';
                if (seccionComponentes) seccionComponentes.style.display = 'none';
                console.log('Ocultando secciones para ARTICULO');
            }

            // Recalcular margen después de cambiar el tipo
            calcularMargen();
        });

        // Disparar evento change para aplicar lógica inicial
        setTimeout(() => {
            tipoSelect.dispatchEvent(new Event('change'));
        }, 300);
    } else {
        console.error('No se encontró el elemento tipoSelect');
    }

    // 2. GESTIÓN DE UNIDADES Y TIPOS PARA ARTÍCULOS

    // Cargar información de unidades para todos los artículos disponibles
    document.querySelectorAll('#nuevo-articulo option').forEach(option => {
        const articuloId = option.value;
        if (articuloId) {
            const unidadAbr = option.getAttribute('data-unidad');
            // Consultar el tipo de unidad mediante una petición AJAX
            fetch(`/api/articulos/${articuloId}/unidad`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        unidadesPorArticulo.set(parseInt(articuloId), {
                            tipo: data.tipo,
                            abreviatura: unidadAbr
                        });
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    });

    // Validar tipo de unidad del artículo seleccionado para componentes de servicio
    if (nuevoArticuloSelect) {
        nuevoArticuloSelect.addEventListener('change', function() {
            const articuloId = parseInt(this.value);
            if (!articuloId) return;

            // Si no tenemos la información de la unidad, la solicitamos
            if (!unidadesPorArticulo.has(articuloId)) {
                fetch(`/api/articulos/${articuloId}/unidad`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const unidadInfo = {
                                tipo: data.tipo,
                                abreviatura: data.abreviatura
                            };
                            unidadesPorArticulo.set(articuloId, unidadInfo);
                            ajustarCampoCantidad(nuevaCantidadInput, unidadInfo.tipo);
                        }
                    })
                    .catch(error => console.error('Error:', error));
            } else {
                // Si ya tenemos la información, la usamos
                const unidadInfo = unidadesPorArticulo.get(articuloId);
                ajustarCampoCantidad(nuevaCantidadInput, unidadInfo.tipo);
            }
        });
    }

    // Añadir validación de tipo de unidad para campos de stock
    const unidadSelect = document.getElementById('unidad_id');
    const stockInput = document.getElementById('stock');
    const stockMinimoInput = document.getElementById('stock_minimo');

    if (unidadSelect) {
        unidadSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (!selectedOption) return;

            const tipoUnidad = selectedOption.getAttribute('data-tipo');
            console.log('Tipo de unidad seleccionada:', tipoUnidad);

            // Verificar si se encontraron los campos de stock
            if (!stockInput || !stockMinimoInput) {
                console.error('No se encontraron los campos de stock');
                return;
            }

            if (tipoUnidad === 'unidad') {
                // Para unidades enteras - configurar atributos del input
                stockInput.setAttribute('step', '1');
                stockInput.setAttribute('min', '0');
                stockInput.setAttribute('pattern', '[0-9]*');
                stockMinimoInput.setAttribute('step', '1');
                stockMinimoInput.setAttribute('min', '0');
                stockMinimoInput.setAttribute('pattern', '[0-9]*');

                // Forzar valores enteros
                stockInput.value = Math.floor(parseFloat(stockInput.value) || 0);
                stockMinimoInput.value = Math.floor(parseFloat(stockMinimoInput.value) || 0);

                // Remover los antiguos event listeners (para evitar duplicados)
                stockInput.removeEventListener('input', soloPermitirEnteros);
                stockMinimoInput.removeEventListener('input', soloPermitirEnteros);
                stockInput.removeEventListener('keydown', prevenirDecimales);
                stockMinimoInput.removeEventListener('keydown', prevenirDecimales);

                // Añadir los validadores de forma más robusta
                stockInput.addEventListener('input', soloPermitirEnteros);
                stockMinimoInput.addEventListener('input', soloPermitirEnteros);
                stockInput.addEventListener('keydown', prevenirDecimales);
                stockMinimoInput.addEventListener('keydown', prevenirDecimales);
                stockInput.addEventListener('blur', validarValorEntero);
                stockMinimoInput.addEventListener('blur', validarValorEntero);
            } else {
                // Para unidades decimales
                stockInput.setAttribute('step', '0.01');
                stockInput.setAttribute('min', '0');
                stockInput.removeAttribute('pattern');
                stockMinimoInput.setAttribute('step', '0.01');
                stockMinimoInput.setAttribute('min', '0');
                stockMinimoInput.removeAttribute('pattern');

                // Quitar validadores
                stockInput.removeEventListener('input', soloPermitirEnteros);
                stockMinimoInput.removeEventListener('input', soloPermitirEnteros);
                stockInput.removeEventListener('keydown', prevenirDecimales);
                stockMinimoInput.removeEventListener('keydown', prevenirDecimales);
                stockInput.removeEventListener('blur', validarValorEntero);
                stockMinimoInput.removeEventListener('blur', validarValorEntero);
            }

            console.log('Stock input configurado - step:', stockInput.getAttribute('step'));
        });

        // Aplicar inicialmente la validación si hay una unidad seleccionada
        if (unidadSelect.selectedIndex > 0) {
            setTimeout(() => {
                console.log('Aplicando validación inicial de unidad...');
                unidadSelect.dispatchEvent(new Event('change'));
            }, 100);
        }
    }

    // 3. EVENTOS PARA AGREGAR Y ELIMINAR COMPONENTES DE SERVICIOS

    // Agregar nuevo artículo al servicio
    if (addArticuloBtn) {
        addArticuloBtn.addEventListener('click', function() {
            console.log('Botón agregar artículo clickeado');

            if (!nuevoArticuloSelect || !nuevaCantidadInput) {
                console.error('No se encontraron los elementos para agregar artículo');
                return;
            }

            const articuloId = nuevoArticuloSelect.value;
            if (!articuloId) return alert('Debe seleccionar un artículo');

            const cantidad = parseFloat(nuevaCantidadInput.value);
            if (isNaN(cantidad) || cantidad <= 0) return alert('La cantidad debe ser mayor a 0');

            const option = nuevoArticuloSelect.options[nuevoArticuloSelect.selectedIndex];
            const articuloNombre = option.textContent;
            const precio = parseFloat(option.getAttribute('data-precio') || 0);
            const unidad = option.getAttribute('data-unidad') || '';

            // Determinar el tipo de unidad - diferentes atributos dependiendo de la vista
            let tipoUnidad = option.getAttribute('data-unidad-tipo') || 'decimal'; // Para add.blade.php
            if (!tipoUnidad || tipoUnidad === 'decimal') {
                // Intentar obtener del mapa de unidades para edit.blade.php
                const unidadInfo = unidadesPorArticulo.get(parseInt(articuloId));
                if (unidadInfo) {
                    tipoUnidad = unidadInfo.tipo;
                }
            }

            console.log('Agregando artículo:', {
                id: articuloId,
                nombre: articuloNombre,
                cantidad: cantidad,
                unidad: unidad,
                tipoUnidad: tipoUnidad
            });

            const costo = precio * cantidad;

            // Verificar si el tipo es unidad y la cantidad tiene decimales
            if (tipoUnidad === 'unidad' && !Number.isInteger(cantidad)) {
                return alert('Este artículo utiliza una unidad de tipo entero. Por favor, ingrese un número entero.');
            }

            // Verificar si ya existe el artículo
            // Para edit.blade.php
            let existeArticulo = document.querySelector(`input[name^="articulos_servicio_existentes"][value="${articuloId}"]`) ||
                               document.querySelector(`input[name^="articulos_servicio[new][id]"][value="${articuloId}"]`);

            // Para add.blade.php
            if (!existeArticulo) {
                existeArticulo = document.querySelector(`input[name="articulos_servicio[${articuloId}]"]`);
            }

            if (existeArticulo) {
                return alert('Este artículo ya ha sido agregado al servicio');
            }

            // Crear fila para el nuevo artículo
            const row = document.createElement('tr');
            row.setAttribute('data-precio', precio);
            row.setAttribute('data-tipo-unidad', tipoUnidad);
            row.setAttribute('data-articulo-id', articuloId);

            // Obtener el símbolo de moneda
            const simboloMoneda = document.getElementById('currency_simbol')?.value || '$';

            // En add.blade.php se usan campos hidden diferentes a edit.blade.php
            let hiddenInputHtml = '';
            const esAdd = nuevoArticuloSelect.id === 'articulo_id';

            if (esAdd) {
                // Para add.blade.php
                hiddenInputHtml = `<input type="hidden" name="articulos_servicio[${articuloId}]" value="${cantidad}">`;
                console.log('Usando formato hidden para add.blade.php');
            } else {
                // Para edit.blade.php
                hiddenInputHtml = `<input type="hidden" name="articulos_servicio[new][id][]" value="${articuloId}">`;
                console.log('Usando formato hidden para edit.blade.php');
            }

            row.innerHTML = `
                <td>
                    <div class="d-flex align-items-center">
                        <div>
                            <span class="d-block fw-bold">${articuloNombre}</span>
                        </div>
                    </div>
                    ${hiddenInputHtml}
                </td>
                <td>
                    <div class="input-group input-group-sm">
                        <input type="number" step="${tipoUnidad === 'unidad' ? '1' : '0.01'}"
                            class="form-control cantidad-articulo"
                            ${esAdd ? '' : 'name="articulos_servicio[new][cantidad][]"'}
                            value="${cantidad}"
                            min="${tipoUnidad === 'unidad' ? '1' : '0.01'}"
                            data-tipo-unidad="${tipoUnidad}"
                            ${esAdd ? 'onchange="actualizarCantidad(this, \'' + articuloId + '\')"' : ''}
                            required>
                        <span class="input-group-text">${unidad}</span>
                    </div>
                </td>
                <td>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">${simboloMoneda}.</span>
                        <input type="text" class="form-control costo-articulo" value="${costo.toFixed(2)}" readonly>
                    </div>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-articulo-servicio">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;

            if (servicioArticulosNuevosBody) {
                servicioArticulosNuevosBody.appendChild(row);
                console.log('Artículo agregado a la tabla');
            } else {
                console.error('No se encontró el elemento para agregar artículos');
                return;
            }

            // Limpiar campos
            nuevoArticuloSelect.value = '';
            nuevaCantidadInput.value = '1';
            if (typeof $ !== 'undefined') {
                // Para edit.blade.php usa Select2
                if (nuevoArticuloSelect.id === 'nuevo-articulo' && $('#nuevo-articulo').length) {
                    $('#nuevo-articulo').val(null).trigger('change');
                }
                // Para add.blade.php si usa Select2
                else if (nuevoArticuloSelect.id === 'articulo_id' && $('#articulo_id').length) {
                    $('#articulo_id').val(null).trigger('change');
                }
                // Fallback para cualquier caso
                else {
                    $(nuevoArticuloSelect).trigger('change');
                }
            }

            actualizarContadores();
            actualizarCostoTotal();
        });
    }

    // Eliminar artículo de la tabla (delegación de eventos)
    document.addEventListener('click', function(e) {
        if (e.target.matches('.remove-articulo-servicio') || e.target.closest('.remove-articulo-servicio')) {
            const boton = e.target.matches('.remove-articulo-servicio') ? e.target : e.target.closest('.remove-articulo-servicio');
            const fila = boton.closest('tr');

            // Obtener el ID del artículo para poder eliminar también el input hidden en add.blade.php
            const articuloId = fila.getAttribute('data-articulo-id');
            if (articuloId) {
                console.log('Eliminando artículo ID:', articuloId);
                // Verificar si existe un input hidden específico para add.blade.php
                const hiddenInput = document.querySelector(`input[name="articulos_servicio[${articuloId}]"]`);
                if (hiddenInput && hiddenInput !== fila.querySelector('input[type="hidden"]')) {
                    console.log('Eliminando input hidden adicional para artículo ID:', articuloId);
                    hiddenInput.remove();
                }
            }

            fila.remove();
            actualizarContadores();
            actualizarCostoTotal();
        }
    });

    // Actualizar costo al cambiar cantidad (delegación de eventos)
    document.addEventListener('input', function(e) {
        if (e.target.matches('.cantidad-articulo')) {
            const cantidadInput = e.target;
            const row = cantidadInput.closest('tr');
            const tipoUnidad = cantidadInput.getAttribute('data-tipo-unidad');
            let cantidad = parseFloat(cantidadInput.value) || 0;

            // Si es unidad, validamos que sea entero
            if (tipoUnidad === 'unidad') {
                cantidad = Math.floor(cantidad);
                cantidadInput.value = cantidad;
            }

            const costoInput = row.querySelector('.costo-articulo');
            const precio = parseFloat(row.getAttribute('data-precio'));
            const costo = precio * cantidad;
            costoInput.value = costo.toFixed(2);

            actualizarCostoTotal();
        }
    });

    // 4. VALIDACIÓN DEL FORMULARIO
    const formulario = document.querySelector('#form-articulo');
    if (formulario) {
        formulario.addEventListener('submit', function(e) {
            if (tipoSelect && tipoSelect.value === 'servicio') {
                // Verificar si existen los elementos antes de consultar
                if (!servicioArticulosExistentesBody && !servicioArticulosNuevosBody) {
                    console.error('No se encontraron los elementos para verificar artículos');
                    return;
                }

                const tieneArticulos =
                    (servicioArticulosExistentesBody ? servicioArticulosExistentesBody.querySelectorAll('tr').length > 0 : false) ||
                    (servicioArticulosNuevosBody ? servicioArticulosNuevosBody.querySelectorAll('tr').length > 0 : false);

                if (!tieneArticulos) {
                    e.preventDefault();
                    alert('Debe agregar al menos un artículo al servicio');

                    // Mostrar sección de componentes
                    if (seccionComponentes) {
                        seccionComponentes.style.display = 'block';
                        seccionComponentes.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }

                    // Mostrar pestaña de componentes si existe
                    if (componentesTab && typeof bootstrap !== 'undefined') {
                        try {
                            const componentesTabTrigger = new bootstrap.Tab(componentesTab);
                            componentesTabTrigger.show();
                        } catch (error) {
                            console.error('Error al mostrar la pestaña de componentes:', error);
                        }
                    }
                }
            }
        });
    }

    // 5. REGISTRAR LISTENERS PARA CÁLCULO DE MÁRGENES
    if (costoMecanicoInput) {
        costoMecanicoInput.addEventListener('input', calcularMargen);
    }

    if (comisionCarwashInput) {
        comisionCarwashInput.addEventListener('input', calcularMargen);
    }

    if (precioCompraInput) {
        precioCompraInput.addEventListener('input', calcularMargen);
        precioCompraInput.addEventListener('change', calcularMargen);
    }

    if (precioVentaInput) {
        precioVentaInput.addEventListener('input', calcularMargen);
        precioVentaInput.addEventListener('change', calcularMargen);
    }

    // 6. INICIALIZACIÓN FINAL

    // Si es servicio, inicializar el costo total
    if (tipoSelect && tipoSelect.value === 'servicio') {
        setTimeout(() => {
            actualizarCostoTotal();
        }, 500);
    }

    // Aplicar validaciones a los campos de cantidad existentes
    document.querySelectorAll('.cantidad-articulo').forEach(function(input) {
        const tipoUnidad = input.getAttribute('data-tipo-unidad') || 'decimal';
        if (tipoUnidad === 'unidad') {
            input.setAttribute('step', '1');
            input.setAttribute('min', '1');

            // Asegurar valor entero al cargar
            input.value = Math.floor(parseFloat(input.value) || 1);

            // Agregar validadores
            input.addEventListener('input', soloPermitirEnteros);
            input.addEventListener('keydown', prevenirDecimales);
        } else {
            input.setAttribute('step', '0.01');
            input.setAttribute('min', '0.01');
        }
    });

    // Calcular margen al cargar la página
    setTimeout(calcularMargen, 100);

    // Actualizar contadores al inicio
    actualizarContadores();
});

// FUNCIONES AUXILIARES (fuera del DOMContentLoaded)

// Función para calcular y mostrar margen de ganancia
function calcularMargen() {
    // Valores principales
    const precioCompra = parseFloat(document.getElementById('precio_compra')?.value) || 0;
    const precioVenta = parseFloat(document.getElementById('precio_venta')?.value) || 0;
    const impuestoPorcentaje = parseFloat(document.getElementById('impuesto_porcentaje')?.value) || 0;
    const simboloMoneda = document.getElementById('currency_simbol')?.value || '$';
    const tipoElement = document.getElementById('tipo');

    if (!tipoElement) {
        console.error('No se encontró el elemento tipo para calcular el margen');
        return;
    }

    // Costos adicionales - comisiones
    const costoMecanico = parseFloat(document.getElementById('costo_mecanico')?.value) || 0;
    const comisionCarwash = parseFloat(document.getElementById('comision_carwash')?.value) || 0;
    const tipoArticulo = tipoElement.value;

    // Solo aplicar costos de comisiones si es un servicio
    const costosComisiones = (tipoArticulo === 'servicio') ? (costoMecanico + comisionCarwash) : 0;

    // Calcular costos adicionales
    const impuestoValor = precioVenta * (impuestoPorcentaje / 100);

    // Calcular ganancia y margen incluyendo comisiones
    const ganancia = precioVenta - precioCompra;
    const gananciaReal = ganancia - impuestoValor - costosComisiones;

    // El margen se calcula sobre el costo total (precio de compra + comisiones)
    const costoTotal = precioCompra + costosComisiones;
    const margenReal = costoTotal > 0 ? (gananciaReal / costoTotal) * 100 : 0;

    // Actualizar etiquetas en la tabla
    const tdPrecioVenta = document.getElementById('td-precio-venta');
    const tdPrecioCompra = document.getElementById('td-precio-compra');
    const tdLabelImpuesto = document.getElementById('td-label-impuesto');
    const tdImpuesto = document.getElementById('td-impuesto');

    if (tdPrecioVenta) {
        tdPrecioVenta.textContent = `${simboloMoneda}.${formatNumber(precioVenta)}`;
    }

    if (tdPrecioCompra) {
        tdPrecioCompra.textContent = `- ${simboloMoneda}.${formatNumber(precioCompra)}`;
    }

    if (tdLabelImpuesto) {
        tdLabelImpuesto.textContent = `Impuesto (${formatNumber(impuestoPorcentaje)}%)`;
    }

    if (tdImpuesto) {
        tdImpuesto.textContent = `- ${simboloMoneda}.${formatNumber(impuestoValor)}`;
    }

    // Añadir filas para comisiones si es un servicio
    const tablaMargen = document.querySelector('#margen-detalle table tbody');
    if (tablaMargen) {
        // Buscar o crear filas para comisiones
        let filaComisiones = document.getElementById('fila-comisiones');
        if (tipoArticulo === 'servicio' && costosComisiones > 0) {
            if (!filaComisiones) {
                // Crear fila si no existe
                filaComisiones = document.createElement('tr');
                filaComisiones.id = 'fila-comisiones';

                const tdLabel = document.createElement('td');
                tdLabel.className = 'text-start';
                tdLabel.id = 'td-label-comisiones';
                tdLabel.textContent = 'Comisiones (Mecánico/CarWash)';

                const tdValor = document.createElement('td');
                tdValor.className = 'text-end text-danger';
                tdValor.id = 'td-comisiones';
                tdValor.textContent = `- ${simboloMoneda}.${formatNumber(costosComisiones)}`;

                filaComisiones.appendChild(tdLabel);
                filaComisiones.appendChild(tdValor);

                // Insertar antes de la fila de ganancia
                const filaGanancia = tablaMargen.querySelector('tr:last-child');
                if (filaGanancia) {
                    tablaMargen.insertBefore(filaComisiones, filaGanancia);
                } else {
                    tablaMargen.appendChild(filaComisiones);
                }
            } else {
                // Actualizar valor si ya existe la fila
                const tdComisiones = document.getElementById('td-comisiones');
                if (tdComisiones) {
                    tdComisiones.textContent = `- ${simboloMoneda}.${formatNumber(costosComisiones)}`;
                }
            }
        } else if (filaComisiones) {
            // Remover fila si no es servicio o no hay comisiones
            filaComisiones.remove();
        }
    }

    // Colorear ganancia real
    const tdGananciaReal = document.getElementById('td-ganancia-real');
    if (tdGananciaReal) {
        tdGananciaReal.textContent = `${simboloMoneda}.${formatNumber(gananciaReal)}`;
        tdGananciaReal.className = gananciaReal >= 0 ? 'text-end text-success' : 'text-end text-danger';
    }

    // Actualizar margen visual con el valor correcto
    const margenElement = document.getElementById('margen-valor');
    const margenBarra = document.getElementById('margen-barra');

    if (margenElement && margenBarra) {
        margenElement.textContent = `${formatNumber(margenReal)}%`;

        // Establecer color según el valor del margen
        let colorClass = 'bg-success';
        if (margenReal < 10) {
            colorClass = 'bg-danger';
        } else if (margenReal < 20) {
            colorClass = 'bg-warning';
        }

        // Remover clases de color anteriores
        margenBarra.className = 'progress-bar';
        margenElement.className = 'badge fs-6';

        // Agregar nueva clase de color
        margenBarra.classList.add(colorClass);
        margenElement.classList.add(colorClass);

        // Actualizar ancho de la barra de progreso (máximo 100%)
        const anchoBarraPorcentaje = Math.min(margenReal, 100);
        margenBarra.style.width = `${anchoBarraPorcentaje}%`;
    }
}

// Función auxiliar para formatear números con 2 decimales
function formatNumber(number) {
    return (Math.round(number * 100) / 100).toFixed(2);
}

// Función para actualizar contadores
function actualizarContadores() {
    console.log('Actualizando contadores...');

    // Intentar diferentes IDs para soportar ambas vistas
    const elementosContadores = [
        { tbody: 'servicio-articulos-existentes-body', contador: 'contador-existentes' },
        { tbody: 'servicio-articulos', contador: 'contador-articulos' },
        { tbody: 'servicio-articulos', contador: 'contador-articulos-agregados' },
        { tbody: 'servicio-articulos-nuevos-body', contador: 'contador-existentes' }
    ];

    for (const elem of elementosContadores) {
        const tbody = document.getElementById(elem.tbody);
        const contador = document.getElementById(elem.contador);

        if (tbody && contador) {
            const cantidad = tbody.querySelectorAll('tr').length;
            contador.textContent = cantidad.toString();
            console.log(`Actualizado contador ${elem.contador}: ${cantidad}`);
        }
    }

    // Para add.blade.php también podemos tener otros contadores
    const contadoresAdicionales = [
        { tbody: 'servicio-articulos', contador: 'contador-articulos-agregados' },
        { tbody: 'servicio-articulos-nuevos-body', contador: 'contador-articulos-agregados' }
    ];

    for (const elem of contadoresAdicionales) {
        const tbody = document.getElementById(elem.tbody);
        const contador = document.getElementById(elem.contador);

        if (tbody && contador && contador.textContent === '0') {
            const cantidad = tbody.querySelectorAll('tr').length;
            contador.textContent = cantidad.toString();
            console.log(`Actualizado contador adicional ${elem.contador}: ${cantidad}`);
        }
    }
}

// Función para actualizar costo total
function actualizarCostoTotal() {
    console.log('Actualizando costo total...');

    const costoTotalElement = document.getElementById('costo-total');
    const tipoSelect = document.getElementById('tipo');
    const precioCompraInput = document.getElementById('precio_compra');
    const precioVentaInput = document.getElementById('precio_venta');

    if (!costoTotalElement || !tipoSelect || !precioCompraInput || !precioVentaInput) {
        console.error('No se encontraron los elementos para actualizar costo total');
        return;
    }

    // Sumar costos de artículos existentes
    let costoTotal = 0;
    document.querySelectorAll('.costo-articulo').forEach(function(input) {
        let valor = input.value;
        // Si el valor tiene formato con coma como separador decimal, lo convertimos
        if (typeof valor === 'string') {
            valor = valor.replace(/,/g, '');
        }
        const costo = parseFloat(valor) || 0;
        costoTotal += costo;
        console.log('Sumando costo:', costo, 'Total acumulado:', costoTotal);
    });

    costoTotalElement.value = costoTotal.toFixed(2);
    console.log('Costo total actualizado:', costoTotal.toFixed(2));

    // Si es servicio, actualizar precio de compra y sugerir precio de venta basado en los costos
    if (tipoSelect.value === 'servicio') {
        // Actualizar precio de compra para que refleje el costo total de los componentes
        precioCompraInput.value = costoTotal.toFixed(2);
        console.log('Precio de compra actualizado:', costoTotal.toFixed(2));

        const sugerido = costoTotal * 1.3; // 30% de ganancia
        const precioVentaActual = parseFloat(precioVentaInput.value) || 0;

        // Sugerir precio de venta si es 0 o menor que el costo
        if (precioVentaActual === 0 || precioVentaActual < costoTotal) {
            precioVentaInput.value = sugerido.toFixed(2);
            console.log('Precio de venta sugerido:', sugerido.toFixed(2));
        }

        // Recalcular margen siempre que se actualice el costo total
        calcularMargen();
    }
}

// Función para ajustar el campo de cantidad según el tipo de unidad
function ajustarCampoCantidad(inputCantidad, tipoUnidad) {
    if (!inputCantidad) {
        console.error('No se encontró el elemento para ajustar cantidad');
        return;
    }

    console.log('Ajustando campo cantidad para tipo:', tipoUnidad);

    if (tipoUnidad === 'unidad') {
        // Para unidades enteras
        inputCantidad.setAttribute('step', '1');
        inputCantidad.setAttribute('min', '1');
        inputCantidad.value = Math.floor(parseFloat(inputCantidad.value) || 1);

        // Limpiar event listeners anteriores clonando el elemento
        const nuevoInput = inputCantidad.cloneNode(true);
        if (inputCantidad.parentNode) {
            inputCantidad.parentNode.replaceChild(nuevoInput, inputCantidad);
            inputCantidad = nuevoInput; // Actualizar referencia
        }

        // Asegurar valor entero
        inputCantidad.addEventListener('input', function() {
            if (this.value.includes('.') || this.value.includes(',') || !Number.isInteger(parseFloat(this.value))) {
                this.value = Math.floor(parseFloat(this.value) || 1);
            }
        });

        // Prevenir entrada de decimales
        inputCantidad.addEventListener('keydown', function(e) {
            if (e.key === '.' || e.key === ',') {
                e.preventDefault();
                return false;
            }
        });
    } else {
        // Para unidades decimales
        inputCantidad.setAttribute('step', '0.01');
        inputCantidad.setAttribute('min', '0.01');

        // Limpiar event listeners anteriores clonando el elemento
        const nuevoInput = inputCantidad.cloneNode(true);
        if (inputCantidad.parentNode) {
            inputCantidad.parentNode.replaceChild(nuevoInput, inputCantidad);
        }
    }
}

// Función para validar que solo se ingresen números enteros - Versión mejorada
function soloPermitirEnteros(e) {
    const input = e.target;
    const valor = input.value;

    // Si hay un punto decimal o el valor no es un entero
    if (valor.includes('.') || valor.includes(',') || !Number.isInteger(parseFloat(valor))) {
        input.value = Math.floor(parseFloat(valor) || 0);
    }
}

// Función para prevenir la entrada de puntos decimales
function prevenirDecimales(e) {
    // Prevenir punto decimal y coma
    if (e.key === '.' || e.key === ',') {
        e.preventDefault();
        return false;
    }
}

// Función para validar al perder el foco
function validarValorEntero(e) {
    const input = e.target;
    const valor = input.value;

    // Asegurar que sea un valor entero
    input.value = Math.floor(parseFloat(valor) || 0);
}

// Función para actualizar la cantidad en el input hidden
function actualizarCantidad(input, articuloId) {
    console.log('Actualizando cantidad para artículo ID:', articuloId);

    const tipoUnidad = input.getAttribute('data-tipo-unidad');
    let cantidad = parseFloat(input.value);

    if (isNaN(cantidad) || cantidad <= 0) {
        input.value = tipoUnidad === 'unidad' ? '1' : '0.01';
        cantidad = tipoUnidad === 'unidad' ? 1 : 0.01;
        alert('La cantidad debe ser mayor a 0');
    }

    // Si es unidad, aseguramos que sea entero
    if (tipoUnidad === 'unidad' && !Number.isInteger(cantidad)) {
        cantidad = Math.floor(cantidad);
        input.value = cantidad;
    }

    // Buscar el input hidden correspondiente
    const hiddenInput = document.querySelector(`input[name="articulos_servicio[${articuloId}]"]`);
    if (hiddenInput) {
        console.log('Input hidden encontrado, actualizando valor a:', cantidad);
        hiddenInput.value = cantidad;
    } else {
        console.error('No se encontró el input hidden para el artículo ID:', articuloId);
    }

    // Actualizar el costo
    const row = input.closest('tr');
    if (row) {
        const precio = parseFloat(row.getAttribute('data-precio')) || 0;
        const costo = precio * cantidad;
        const costoInput = row.querySelector('.costo-articulo');
        if (costoInput) {
            costoInput.value = costo.toFixed(2);
            console.log('Costo actualizado a:', costo.toFixed(2));
        }

        // Actualizar costo total
        actualizarCostoTotal();
    }
}
