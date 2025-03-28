<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ClienteVehiculoController extends Controller
{
    /**
     * Obtiene los vehículos asociados a un cliente específico.
     *
     * @param  int  $cliente_id
     * @return \Illuminate\Http\Response
     */
    public function getVehiculos($cliente_id)
    {
        try {
            Log::info('Buscando vehículos para el cliente ID: ' . $cliente_id);
            $vehiculos = Vehiculo::where('cliente_id', $cliente_id)->get();
            Log::info('Vehículos encontrados: ' . $vehiculos->count());
            return response()->json($vehiculos);
        } catch (\Exception $e) {
            Log::error('Error al obtener vehículos: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener vehículos: ' . $e->getMessage()], 500);
        }
    }
}
