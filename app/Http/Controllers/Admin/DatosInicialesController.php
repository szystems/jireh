<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TipoTrabajador;
use App\Models\Trabajador;
use App\Models\Comision;
use App\Models\User;
use App\Models\Categoria;
use App\Models\Articulo;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Cliente;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DatosInicialesController extends Controller
{
    /**
     * Crea datos de prueba para el sistema de comisiones
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function crearDatosPrueba()
    {
        // Usamos una transacción para poder revertir en caso de error
        return DB::transaction(function() {
            try {
                // 1. Crear tipos de trabajador si no existen
                $this->crearTiposTrabajador();

                // 2. Crear trabajadores para pruebas
                $trabajadores = $this->crearTrabajadores();

                // 3. Crear categorías y artículos
                $articulos = $this->crearArticulos();

                // 4. Crear clientes para pruebas
                $clientes = $this->crearClientes();

                // 5. Crear ventas con distintos escenarios de comisión
                $ventas = $this->crearVentas($clientes, $articulos, $trabajadores);

                // 6. Crear comisiones manualmente
                $this->crearComisiones($ventas, $trabajadores);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Datos de prueba creados correctamente',
                    'data' => [
                        'trabajadores' => $trabajadores->pluck('nombre_completo'),
                        'articulos' => $articulos->pluck('nombre'),
                        'ventas' => $ventas->pluck('num_comprobante'),
                        'comisiones' => Comision::count()
                    ]
                ]);
            } catch (\Exception $e) {
                // En caso de error revertimos la transacción y devolvemos el mensaje
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error al crear datos de prueba: ' . $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ], 500);
            }
        });
    }

    /**
     * Crea los tipos de trabajador necesarios
     */
    private function crearTiposTrabajador()
    {
        // Tipo de trabajador: Mecánico
        if (!TipoTrabajador::where('nombre', 'Mecánico')->exists()) {
            TipoTrabajador::create([
                'nombre' => 'Mecánico',
                'descripcion' => 'Trabaja en reparación de vehículos',
                'aplica_comision' => true,
                'requiere_asignacion' => true,
                'tipo_comision' => 'porcentaje_venta',
                'porcentaje_comision' => 10.00,
                'permite_multiples_trabajadores' => false,
                'estado' => 'activo'
            ]);
        }

        // Tipo de trabajador: Car Wash
        if (!TipoTrabajador::where('nombre', 'Car Wash')->exists()) {
            TipoTrabajador::create([
                'nombre' => 'Car Wash',
                'descripcion' => 'Realiza lavado de vehículos',
                'aplica_comision' => true,
                'requiere_asignacion' => false,
                'tipo_comision' => 'porcentaje_venta',
                'porcentaje_comision' => 15.00,
                'permite_multiples_trabajadores' => true,
                'estado' => 'activo'
            ]);
        }
    }

    /**
     * Crea trabajadores para pruebas
     */
    private function crearTrabajadores()
    {
        $mecanico = TipoTrabajador::where('nombre', 'Mecánico')->first();
        $carwash = TipoTrabajador::where('nombre', 'Car Wash')->first();

        $trabajadores = collect();

        // Crear 2 mecánicos
        for ($i = 1; $i <= 2; $i++) {
            $trabajador = Trabajador::firstOrCreate(
                ['email' => "mecanico{$i}@test.com"],
                [
                    'nombre' => "Mecánico",
                    'apellido' => "Prueba {$i}",
                    'telefono' => "123456{$i}",
                    'direccion' => "Dirección de prueba {$i}",
                    'nit' => "NIT12345{$i}",
                    'dpi' => "DPI123456{$i}",
                    'tipo_trabajador_id' => $mecanico->id,
                    'estado' => true
                ]
            );
            $trabajadores->push($trabajador);
        }

        // Crear 2 trabajadores de car wash
        for ($i = 1; $i <= 2; $i++) {
            $trabajador = Trabajador::firstOrCreate(
                ['email' => "carwash{$i}@test.com"],
                [
                    'nombre' => "CarWash",
                    'apellido' => "Prueba {$i}",
                    'telefono' => "654321{$i}",
                    'direccion' => "Dirección de prueba {$i}",
                    'nit' => "NIT54321{$i}",
                    'dpi' => "DPI654321{$i}",
                    'tipo_trabajador_id' => $carwash->id,
                    'estado' => true
                ]
            );
            $trabajadores->push($trabajador);
        }

        return $trabajadores;
    }

    /**
     * Crea categorías y artículos para pruebas
     */
    private function crearArticulos()
    {
        // Crear categoría para servicios mecánicos si no existe
        $catMecanica = Categoria::firstOrCreate(
            ['nombre' => 'Servicios Mecánicos'],
            [
                'descripcion' => 'Servicios de reparación y mantenimiento',
                'estado' => true
            ]
        );

        // Crear categoría para servicios de lavado si no existe
        $catLavado = Categoria::firstOrCreate(
            ['nombre' => 'Servicios de Lavado'],
            [
                'descripcion' => 'Servicios de limpieza y lavado',
                'estado' => true
            ]
        );

        $articulos = collect();

        // Crear artículos de servicios mecánicos
        $servMecanicos = [
            [
                'nombre' => 'Cambio de aceite',
                'precio_venta' => 350.00,
                'stock' => 100
            ],
            [
                'nombre' => 'Alineación y balanceo',
                'precio_venta' => 500.00,
                'stock' => 100
            ],
            [
                'nombre' => 'Reparación de frenos',
                'precio_venta' => 750.00,
                'stock' => 100
            ]
        ];

        foreach ($servMecanicos as $serv) {
            $articulo = Articulo::firstOrCreate(
                ['nombre' => $serv['nombre']],
                [
                    'categoria_id' => $catMecanica->id,
                    'codigo' => 'SRV' . strtoupper(substr(md5($serv['nombre']), 0, 6)),
                    'precio_venta' => $serv['precio_venta'],
                    'stock' => $serv['stock'],
                    'es_servicio' => true,
                    'aplica_comision' => true,
                    'estado' => true
                ]
            );
            $articulos->push($articulo);
        }

        // Crear artículos de servicios de lavado
        $servLavado = [
            [
                'nombre' => 'Lavado básico',
                'precio_venta' => 100.00,
                'stock' => 100
            ],
            [
                'nombre' => 'Lavado premium',
                'precio_venta' => 200.00,
                'stock' => 100
            ],
            [
                'nombre' => 'Lavado y encerado',
                'precio_venta' => 300.00,
                'stock' => 100
            ]
        ];

        foreach ($servLavado as $serv) {
            $articulo = Articulo::firstOrCreate(
                ['nombre' => $serv['nombre']],
                [
                    'categoria_id' => $catLavado->id,
                    'codigo' => 'LAV' . strtoupper(substr(md5($serv['nombre']), 0, 6)),
                    'precio_venta' => $serv['precio_venta'],
                    'stock' => $serv['stock'],
                    'es_servicio' => true,
                    'aplica_comision' => true,
                    'estado' => true
                ]
            );
            $articulos->push($articulo);
        }

        return $articulos;
    }

    /**
     * Crea clientes para pruebas
     */
    private function crearClientes()
    {
        $clientes = collect();

        for ($i = 1; $i <= 3; $i++) {
            $cliente = Cliente::firstOrCreate(
                ['email' => "cliente{$i}@test.com"],
                [
                    'nombre' => "Cliente Prueba {$i}",
                    'apellido' => "Apellido {$i}",
                    'telefono' => "9876{$i}",
                    'direccion' => "Dirección Cliente {$i}",
                    'nit' => "C{$i}12345",
                    'estado' => true
                ]
            );

            $clientes->push($cliente);
        }

        return $clientes;
    }

    /**
     * Crea ventas con diferentes escenarios
     */
    private function crearVentas($clientes, $articulos, $trabajadores)
    {
        $ventas = collect();
        $fechaActual = Carbon::now();

        // Obtener mecánicos y trabajadores de carwash
        $mecanicos = $trabajadores->filter(function($t) {
            return $t->esMecanico();
        });

        $carwashers = $trabajadores->filter(function($t) {
            return $t->esCarwash();
        });

        // Agrupar artículos por categoría
        $servMecanicos = $articulos->filter(function($a) {
            return $a->categoria->nombre === 'Servicios Mecánicos';
        });

        $servLavado = $articulos->filter(function($a) {
            return $a->categoria->nombre === 'Servicios de Lavado';
        });

        // Usuario vendedor (primer usuario administrador)
        $vendedor = User::where('rol', 'admin')->first();

        // 1. Venta solo con servicios mecánicos
        $venta1 = $this->crearVenta(
            $clientes->first(),
            $vendedor,
            $fechaActual->copy()->subDays(5),
            $servMecanicos->take(2),
            $mecanicos->first()
        );
        $ventas->push($venta1);

        // 2. Venta solo con servicios de lavado
        $venta2 = $this->crearVenta(
            $clientes->get(1),
            $vendedor,
            $fechaActual->copy()->subDays(3),
            $servLavado->take(2),
            null,
            $carwashers
        );
        $ventas->push($venta2);

        // 3. Venta mixta (mecánica y lavado)
        $articulosMixtos = collect([$servMecanicos->first(), $servLavado->first()]);
        $venta3 = $this->crearVenta(
            $clientes->get(2),
            $vendedor,
            $fechaActual->copy()->subDays(1),
            $articulosMixtos,
            $mecanicos->get(1),
            $carwashers->take(1)
        );
        $ventas->push($venta3);

        return $ventas;
    }

    /**
     * Crea una venta individual
     */
    private function crearVenta($cliente, $vendedor, $fecha, $articulos, $mecanico = null, $carwashers = null)
    {
        // Crear venta principal
        $venta = new Venta();
        $venta->cliente_id = $cliente->id;
        $venta->user_id = $vendedor->id;
        $venta->fecha = $fecha;
        $venta->tipo_comprobante = 'Factura';
        $venta->serie_comprobante = 'F001';
        $venta->num_comprobante = 'TEST-' . strtoupper(substr(md5(rand()), 0, 6));
        $venta->impuesto = 12;
        $venta->total = 0;
        $venta->estado = 'Completado';
        $venta->save();

        $totalVenta = 0;

        // Crear detalle de venta para cada artículo
        foreach ($articulos as $articulo) {
            $detalle = new DetalleVenta();
            $detalle->venta_id = $venta->id;
            $detalle->articulo_id = $articulo->id;
            $detalle->cantidad = 1;
            $detalle->precio = $articulo->precio_venta;
            $detalle->descuento = 0;

            // Si es un servicio mecánico y hay mecánico asignado
            if ($articulo->categoria->nombre === 'Servicios Mecánicos' && $mecanico) {
                $detalle->trabajador_id = $mecanico->id;
            }

            $detalle->save();

            $totalVenta += $detalle->precio;

            // Si es un servicio de lavado y hay carwashers, asignarlos
            if ($articulo->categoria->nombre === 'Servicios de Lavado' && $carwashers && $carwashers->count() > 0) {
                foreach ($carwashers as $carwasher) {
                    $detalle->trabajadoresCarwash()->attach($carwasher->id, [
                        'monto_comision' => $articulo->precio_venta * 0.15 / $carwashers->count()
                    ]);
                }
            }
        }

        // Actualizar total de la venta
        $venta->total = $totalVenta;
        $venta->save();

        return $venta;
    }

    /**
     * Crea comisiones para los diferentes escenarios
     */
    private function crearComisiones($ventas, $trabajadores)
    {
        foreach ($ventas as $venta) {
            foreach ($venta->detalleVenta as $detalle) {
                // 1. Comisiones para mecánicos
                if ($detalle->trabajador_id) {
                    $trabajador = Trabajador::find($detalle->trabajador_id);
                    if ($trabajador && $trabajador->puedeRecibirComisiones()) {
                        $montoComision = $detalle->precio * 0.10; // 10% comisión mecánico

                        Comision::create([
                            'commissionable_id' => $trabajador->id,
                            'commissionable_type' => get_class($trabajador),
                            'tipo_comision' => 'mecanico',
                            'monto' => $montoComision,
                            'porcentaje' => 10,
                            'detalle_venta_id' => $detalle->id,
                            'venta_id' => $venta->id,
                            'articulo_id' => $detalle->articulo_id,
                            'fecha_calculo' => $venta->fecha,
                            'estado' => 'pendiente',
                        ]);
                    }
                }

                // 2. Comisiones para carwash (ya están en la tabla pivote)
                if ($detalle->trabajadoresCarwash && $detalle->trabajadoresCarwash->count() > 0) {
                    foreach ($detalle->trabajadoresCarwash as $carwasher) {
                        $montoComision = $detalle->pivot->monto_comision ?? ($detalle->precio * 0.15 / $detalle->trabajadoresCarwash->count());

                        Comision::create([
                            'commissionable_id' => $carwasher->id,
                            'commissionable_type' => get_class($carwasher),
                            'tipo_comision' => 'carwash',
                            'monto' => $montoComision,
                            'porcentaje' => 15,
                            'detalle_venta_id' => $detalle->id,
                            'venta_id' => $venta->id,
                            'articulo_id' => $detalle->articulo_id,
                            'fecha_calculo' => $venta->fecha,
                            'estado' => 'pendiente',
                        ]);
                    }
                }
            }

            // 3. Comisión para el vendedor (usuario)
            // Si la venta supera 1000, el vendedor recibe 5% de comisión
            if ($venta->total >= 1000) {
                $vendedor = User::find($venta->user_id);
                $montoComision = $venta->total * 0.05;

                Comision::create([
                    'commissionable_id' => $vendedor->id,
                    'commissionable_type' => get_class($vendedor),
                    'tipo_comision' => 'meta_venta',
                    'monto' => $montoComision,
                    'porcentaje' => 5,
                    'venta_id' => $venta->id,
                    'fecha_calculo' => $venta->fecha,
                    'estado' => 'pendiente',
                ]);
            }
        }
    }
}
