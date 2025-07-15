<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Log;

class VentaEditFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'numero_factura' => 'nullable|string|max:255',
            'fecha' => 'required|date',
            'cliente_id' => 'required|exists:clientes,id',
            'vehiculo_id' => 'required|exists:vehiculos,id',
            'tipo_venta' => 'required|in:Car Wash,CDS',
            'estado_pago' => 'required|in:pagado,pendiente',

            // Simplificamos las validaciones de detalles_a_mantener
            'detalles_a_mantener' => 'sometimes|array',
            'detalles_a_mantener.*.articulo_id' => 'sometimes|exists:articulos,id',
            'detalles_a_mantener.*.cantidad' => 'sometimes|numeric|min:0.01',
            'detalles_a_mantener.*.descuento_id' => 'nullable|exists:descuentos,id',
            'detalles_a_mantener.*.usuario_id' => 'sometimes|exists:users,id',
            'detalles_a_mantener.*.sub_total' => 'sometimes|numeric|min:0',

            // Validaciones para detalles a eliminar
            'detalles_a_eliminar' => 'nullable|array',
            'detalles_a_eliminar.*' => 'sometimes|numeric',

            // Validaciones para nuevos detalles
            'nuevos_detalles' => 'nullable|array',
            'nuevos_detalles.*.articulo_id' => 'sometimes|exists:articulos,id',
            'nuevos_detalles.*.cantidad' => 'sometimes|numeric|min:0.01',
            'nuevos_detalles.*.descuento_id' => 'nullable|exists:descuentos,id',
            'nuevos_detalles.*.usuario_id' => 'sometimes|exists:users,id',
            'nuevos_detalles.*.sub_total' => 'sometimes|numeric|min:0'
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     */
    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $this->validateDetalles($validator);
        });
    }

    /**
     * Validate that there is at least one detail for the sale.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @return void
     */
    protected function validateDetalles($validator)
    {
        // DEBUG: Log para detectar problemas en la validación
        Log::info('=== INICIO VALIDACIÓN PERSONALIZADA DE DETALLES ===');
        Log::info('Datos recibidos en request:', $this->all());
        
        // Verificar si existen detalles no eliminados
        $detalles = $this->input('detalles', []);
        Log::info('Detalles recibidos:', $detalles);
        
        $detallesNoEliminados = collect($detalles)->filter(function ($detalle, $key) {
            // Si hay un campo 'eliminar' y está en 1, el detalle se considera eliminado
            $esEliminado = isset($detalle['eliminar']) && $detalle['eliminar'] == '1';
            Log::info("Detalle $key - Eliminado: " . ($esEliminado ? 'SÍ' : 'NO'), $detalle);
            return !$esEliminado;
        });

        // Contar elementos no eliminados y nuevos
        $tieneDetallesAMantener = count($detallesNoEliminados) > 0;
        $tieneNuevosDetalles = $this->has('nuevos_detalles') && count($this->input('nuevos_detalles', [])) > 0;

        Log::info('Análisis de detalles:', [
            'detalles_a_mantener' => $tieneDetallesAMantener,
            'nuevos_detalles' => $tieneNuevosDetalles,
            'count_detalles_no_eliminados' => count($detallesNoEliminados),
            'nuevos_detalles_data' => $this->input('nuevos_detalles', [])
        ]);

        // Si hay detalles a eliminar, consideramos que hay cambios en el formulario
        $hayDetallesAEliminar = $this->has('detalles_a_eliminar') && !empty($this->input('detalles_a_eliminar', []));

        // Verificar si al menos hay un detalle (existente no eliminado o nuevo) en caso de cambios
        $hayCambios = $hayDetallesAEliminar || $tieneNuevosDetalles || $this->has('_method');

        Log::info('Estado de cambios:', [
            'hay_detalles_a_eliminar' => $hayDetallesAEliminar,
            'hay_cambios' => $hayCambios,
            'tiene_method_field' => $this->has('_method')
        ]);

        if ($hayCambios && !$tieneDetallesAMantener && !$tieneNuevosDetalles) {
            Log::warning('VALIDACIÓN FALLIDA: No hay detalles suficientes');
            $validator->errors()->add('detalles', 'Debe haber al menos un artículo en los detalles.');
        } else {
            Log::info('VALIDACIÓN EXITOSA: Detalles válidos');
        }
        
        Log::info('=== FIN VALIDACIÓN PERSONALIZADA DE DETALLES ===');
    }

    /**
     * Personalizar los mensajes de error.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'cliente_id.required' => 'Debe seleccionar un cliente.',
            'vehiculo_id.required' => 'Debe seleccionar un vehículo.',
            'detalles_a_mantener.required' => 'Debe haber al menos un detalle en la venta.',
            'detalles_a_mantener.*.articulo_id.exists' => 'Uno de los artículos seleccionados no existe.',
            'detalles_a_mantener.*.cantidad.numeric' => 'La cantidad debe ser un número.',
            'detalles_a_mantener.*.cantidad.min' => 'La cantidad debe ser mayor a cero.',
            'nuevos_detalles.*.articulo_id.exists' => 'Uno de los artículos seleccionados no existe.',
            'nuevos_detalles.*.cantidad.numeric' => 'La cantidad debe ser un número.',
            'nuevos_detalles.*.cantidad.min' => 'La cantidad debe ser mayor a cero.',
        ];
    }
}
