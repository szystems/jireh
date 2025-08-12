<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{User, Trabajador, TipoTrabajador, Cliente, Categoria, Unidad, Articulo, Venta, DetalleVenta, Comision, MetaVenta, TrabajadorDetalleVenta};
use Carbon\Carbon;

class ComisionesTestSeeder extends Seeder
{
    protected $command;

    /**
     * Set the command instance for output
     */
    public function setCommand($command)
    {
        $this->command = $command;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $output = $this->command ?? $this;
        $output->info('🚀 Creando datos de prueba para el sistema de comisiones...');

        // 1. Crear tipos de trabajadores si no existen
        $this->crearTiposTrabajadores();

        // 2. Crear usuarios vendedores
        $vendedores = $this->crearVendedores();
        $output->info('✅ Vendedores creados: ' . count($vendedores));

        // 3. Crear trabajadores (mecánicos y car wash)
        $trabajadores = $this->crearTrabajadores();
        $output->info('✅ Trabajadores creados: ' . count($trabajadores));

        // 4. Crear categorías y unidades
        $categorias = Categoria::factory(8)->create();
        $unidades = Unidad::factory(5)->create();
        $output->info('✅ Categorías y unidades creadas');

        // 5. Crear artículos específicos
        $articulos = $this->crearArticulos();
        $output->info('✅ Artículos creados: ' . count($articulos));

        // 6. Crear clientes
        $clientes = Cliente::factory(15)->create();
        $output->info('✅ Clientes creados: ' . count($clientes));

        // 7. Crear metas de ventas
        $this->crearMetasVentas();
        $output->info('✅ Metas de ventas creadas');

        // 8. Crear ventas con detalles
        $ventas = $this->crearVentas($vendedores, $clientes->toArray(), $articulos);
        $output->info('✅ Ventas creadas: ' . count($ventas));

        // 9. Crear comisiones
        $comisiones = $this->crearComisiones($vendedores, $trabajadores, $ventas);
        $output->info('✅ Comisiones creadas: ' . count($comisiones));

        $output->info('🎉 ¡Datos de prueba creados exitosamente!');
        $output->info('📊 Resumen:');
        $output->info("   - Vendedores: " . count($vendedores));
        $output->info("   - Trabajadores: " . count($trabajadores));
        $output->info("   - Artículos: " . count($articulos));
        $output->info("   - Ventas: " . count($ventas));
        $output->info("   - Comisiones: " . count($comisiones));
    }

    private function crearTiposTrabajadores()
    {
        $tipos = [
            ['id' => 1, 'nombre' => 'Mecánico', 'descripcion' => 'Trabajador especializado en reparación y mantenimiento'],
            ['id' => 2, 'nombre' => 'Car Wash', 'descripcion' => 'Trabajador especializado en lavado y detallado'],
            ['id' => 3, 'nombre' => 'Administrativo', 'descripcion' => 'Personal administrativo']
        ];

        foreach ($tipos as $tipo) {
            TipoTrabajador::updateOrCreate(['id' => $tipo['id']], $tipo);
        }
    }

    private function crearVendedores()
    {
        $vendedores = [];
        $vendedoresData = [
            ['nombre' => 'Carlos Mendez', 'email' => 'carlos.mendez@jireh.com'],
            ['nombre' => 'Ana Garcia', 'email' => 'ana.garcia@jireh.com'],
            ['nombre' => 'Luis Rodriguez', 'email' => 'luis.rodriguez@jireh.com'],
            ['nombre' => 'Maria Lopez', 'email' => 'maria.lopez@jireh.com'],
            ['nombre' => 'Jose Hernandez', 'email' => 'jose.hernandez@jireh.com']
        ];

        foreach ($vendedoresData as $data) {
            $vendedor = User::updateOrCreate(
                ['email' => $data['email']], // Buscar por email
                [
                    'name' => $data['nombre'],
                    'email' => $data['email'],
                    'role_as' => 1, // Vendedor
                    'estado' => 1,
                    'password' => bcrypt('123456'), // Agregar password por defecto
                ]
            );
            $vendedores[] = $vendedor;
        }

        return $vendedores;
    }

    private function crearTrabajadores()
    {
        $trabajadores = [];

        // Mecánicos
        $nombresMecanicos = ['Roberto Morales', 'Diego Castillo', 'Fernando Vasquez'];
        foreach ($nombresMecanicos as $nombre) {
            $partes = explode(' ', $nombre);
            $trabajador = Trabajador::factory()->mecanico()->create([
                'nombre' => $partes[0],
                'apellido' => $partes[1] ?? '',
            ]);
            $trabajadores[] = $trabajador;
        }

        // Car Wash
        $nombresCarWash = ['Pablo Jimenez', 'Ricardo Moreno', 'Andres Ruiz', 'Daniel Vargas'];
        foreach ($nombresCarWash as $nombre) {
            $partes = explode(' ', $nombre);
            $trabajador = Trabajador::factory()->carwash()->create([
                'nombre' => $partes[0],
                'apellido' => $partes[1] ?? '',
            ]);
            $trabajadores[] = $trabajador;
        }

        return $trabajadores;
    }

    private function crearArticulos()
    {
        $articulos = [];

        // Servicios Car Wash
        $serviciosCarWash = [
            ['nombre' => 'Lavado Básico', 'precio' => 25.00, 'comision' => 8.00],
            ['nombre' => 'Lavado Completo', 'precio' => 45.00, 'comision' => 15.00],
            ['nombre' => 'Encerado Premium', 'precio' => 75.00, 'comision' => 25.00],
            ['nombre' => 'Detallado Interior', 'precio' => 35.00, 'comision' => 12.00],
        ];

        foreach ($serviciosCarWash as $servicio) {
            $articulo = Articulo::factory()->carwash()->create([
                'nombre' => $servicio['nombre'],
                'precio_venta' => $servicio['precio'],
                'comision_carwash' => $servicio['comision'],
                'estado' => 1
            ]);
            $articulos[] = $articulo;
        }

        // Servicios Mecánicos
        $serviciosMecanicos = [
            ['nombre' => 'Cambio de Aceite', 'precio' => 35.00, 'comision' => 15.00],
            ['nombre' => 'Alineación y Balanceo', 'precio' => 85.00, 'comision' => 35.00],
            ['nombre' => 'Revisión de Frenos', 'precio' => 65.00, 'comision' => 25.00],
            ['nombre' => 'Cambio de Filtros', 'precio' => 45.00, 'comision' => 18.00],
        ];

        $mecanicos = Trabajador::whereHas('tipoTrabajador', function($q) {
            $q->where('nombre', 'like', '%Mecánico%');
        })->get();

        foreach ($serviciosMecanicos as $index => $servicio) {
            $mecanico = $mecanicos[$index % count($mecanicos)];
            $articulo = Articulo::factory()->mecanico()->create([
                'nombre' => $servicio['nombre'],
                'precio_venta' => $servicio['precio'],
                'costo_mecanico' => $servicio['comision'],
                'mecanico_id' => $mecanico->id,
                'estado' => 1
            ]);
            $articulos[] = $articulo;
        }

        // Productos físicos
        $productos = [
            ['nombre' => 'Aceite de Motor 5W30', 'precio_compra' => 45.00, 'precio_venta' => 65.00],
            ['nombre' => 'Filtro de Aire', 'precio_compra' => 15.00, 'precio_venta' => 25.00],
            ['nombre' => 'Pastillas de Freno', 'precio_compra' => 85.00, 'precio_venta' => 120.00],
            ['nombre' => 'Shampoo para Carros', 'precio_compra' => 12.00, 'precio_venta' => 18.00],
        ];

        foreach ($productos as $producto) {
            $articulo = Articulo::factory()->producto()->create([
                'nombre' => $producto['nombre'],
                'precio_compra' => $producto['precio_compra'],
                'precio_venta' => $producto['precio_venta'],
                'stock' => rand(20, 100),
                'estado' => 1
            ]);
            $articulos[] = $articulo;
        }

        return $articulos;
    }

    private function crearMetasVentas()
    {
        $metas = [
            // Metas mensuales
            ['nombre' => 'Bronce Mensual', 'monto_minimo' => 1000, 'monto_maximo' => 2499, 'porcentaje_comision' => 3.0, 'periodo' => 'mensual'],
            ['nombre' => 'Plata Mensual', 'monto_minimo' => 2500, 'monto_maximo' => 4999, 'porcentaje_comision' => 5.0, 'periodo' => 'mensual'],
            ['nombre' => 'Oro Mensual', 'monto_minimo' => 5000, 'monto_maximo' => 999999, 'porcentaje_comision' => 8.0, 'periodo' => 'mensual'],
            
            // Metas trimestrales
            ['nombre' => 'Bronce Trimestral', 'monto_minimo' => 3000, 'monto_maximo' => 7499, 'porcentaje_comision' => 3.5, 'periodo' => 'trimestral'],
            ['nombre' => 'Plata Trimestral', 'monto_minimo' => 7500, 'monto_maximo' => 14999, 'porcentaje_comision' => 5.5, 'periodo' => 'trimestral'],
            ['nombre' => 'Oro Trimestral', 'monto_minimo' => 15000, 'monto_maximo' => 999999, 'porcentaje_comision' => 8.5, 'periodo' => 'trimestral'],
        ];

        foreach ($metas as $meta) {
            MetaVenta::updateOrCreate(
                ['nombre' => $meta['nombre']],
                array_merge($meta, [
                    'descripcion' => "Meta {$meta['nombre']} - {$meta['porcentaje_comision']}% de comisión",
                    'estado' => true // Corregido: usar 'estado' en lugar de 'activo'
                ])
            );
        }
    }

    private function crearVentas($vendedores, $clientes, $articulos)
    {
        $ventas = [];
        $fechas = [
            Carbon::now()->subMonths(2),
            Carbon::now()->subMonths(1),
            Carbon::now()->subWeeks(2),
            Carbon::now()->subWeek(),
            Carbon::now()->subDays(3),
            Carbon::now()->subDay(),
            Carbon::now()
        ];

        foreach ($fechas as $fecha) {
            // Crear 3-5 ventas por fecha
            for ($i = 0; $i < rand(3, 5); $i++) {
                $tipoVenta = rand(0, 1) ? 'Car Wash' : 'CDS';
                $venta = Venta::factory()->create([
                    'fecha' => $fecha,
                    'cliente_id' => $clientes[array_rand($clientes)]['id'],
                    'usuario_id' => $vendedores[array_rand($vendedores)]->id,
                    'tipo_venta' => $tipoVenta,
                    'created_at' => $fecha,
                    'updated_at' => $fecha,
                ]);

                // Crear detalles de venta
                $this->crearDetallesVenta($venta, $articulos, $tipoVenta);
                
                $ventas[] = $venta;
            }
        }

        return $ventas;
    }

    private function crearDetallesVenta($venta, $articulos, $tipoVenta)
    {
        $cantidadDetalles = rand(1, 4);

        for ($i = 0; $i < $cantidadDetalles; $i++) {
            if ($tipoVenta === 'Car Wash') {
                // Filtrar servicios car wash
                $articulosCarWash = array_filter($articulos, function($art) {
                    return $art->tipo === 'servicio' && $art->comision_carwash > 0;
                });
                $articulo = $articulosCarWash[array_rand($articulosCarWash)];
            } else {
                // Mezcla de servicios mecánicos y productos
                $articulo = $articulos[array_rand($articulos)];
            }

            $cantidad = $articulo->tipo === 'servicio' ? 1 : rand(1, 3);
            $precioUnitario = $articulo->precio_venta;
            $subTotalDetalle = $cantidad * $precioUnitario;

            $detalle = DetalleVenta::create([
                'venta_id' => $venta->id,
                'articulo_id' => $articulo->id,
                'cantidad' => $cantidad,
                'precio_venta' => $precioUnitario,
                'precio_costo' => $articulo->precio_compra ?? 0,
                'sub_total' => $subTotalDetalle,
                'usuario_id' => $venta->usuario_id,
            ]);

            // Si es car wash, crear asignación de trabajadores
            if ($tipoVenta === 'Car Wash' && $articulo->comision_carwash > 0) {
                $trabajadoresCarWash = Trabajador::whereHas('tipoTrabajador', function($q) {
                    $q->where('nombre', 'like', '%Car Wash%');
                })->get();

                // Asignar 1-2 trabajadores al azar
                $trabajadoresAsignados = $trabajadoresCarWash->random(rand(1, min(2, count($trabajadoresCarWash))));
                foreach ($trabajadoresAsignados as $trabajador) {
                    TrabajadorDetalleVenta::create([
                        'trabajador_id' => $trabajador->id,
                        'detalle_venta_id' => $detalle->id,
                        'monto_comision' => $articulo->comision_carwash / count($trabajadoresAsignados),
                    ]);
                }
            }
        }
    }

    private function crearComisiones($vendedores, $trabajadores, $ventas)
    {
        $comisiones = [];

        // Crear comisiones de vendedores
        foreach ($vendedores as $vendedor) {
            $ventasVendedor = array_filter($ventas, function($v) use ($vendedor) {
                return $v->usuario_id === $vendedor->id;
            });

            if (count($ventasVendedor) > 0) {
                // Calcular total basado en detalles de venta
                $totalVentas = 0;
                foreach ($ventasVendedor as $venta) {
                    $totalVenta = $venta->detalleVentas->sum(function($detalle) {
                        return $detalle->sub_total;
                    });
                    $totalVentas += $totalVenta;
                }
                
                // Determinar meta alcanzada
                $meta = MetaVenta::where('periodo', 'mensual')
                                ->where('monto_minimo', '<=', $totalVentas)
                                ->where('monto_maximo', '>=', $totalVentas)
                                ->first();

                if ($meta) {
                    $primerVenta = array_values($ventasVendedor)[0]; // Convertir a array indexado
                    $comision = Comision::create([
                        'commissionable_id' => $vendedor->id,
                        'commissionable_type' => 'App\Models\User',
                        'tipo_comision' => 'venta_meta',
                        'monto' => $totalVentas * ($meta->porcentaje_comision / 100),
                        'porcentaje' => $meta->porcentaje_comision,
                        'venta_id' => $primerVenta->id,
                        'estado' => rand(0, 1) ? 'pendiente' : 'pagado',
                        'fecha_calculo' => Carbon::now()->subDays(rand(1, 30)),
                    ]);
                    $comisiones[] = $comision;
                }
            }
        }

        // Crear comisiones de trabajadores (mecánicos y car wash)
        foreach ($ventas as $venta) {
            foreach ($venta->detalleVentas as $detalle) {
                $articulo = $detalle->articulo;

                // Comisión mecánico
                if ($articulo && $articulo->mecanico_id && $articulo->costo_mecanico > 0) {
                    $comision = Comision::create([
                        'commissionable_id' => $articulo->mecanico_id,
                        'commissionable_type' => 'App\Models\Trabajador',
                        'tipo_comision' => 'mecanico',
                        'monto' => $articulo->costo_mecanico * $detalle->cantidad,
                        'detalle_venta_id' => $detalle->id,
                        'venta_id' => $venta->id,
                        'articulo_id' => $articulo->id,
                        'estado' => rand(0, 1) ? 'pendiente' : 'pagado',
                        'fecha_calculo' => $venta->fecha,
                    ]);
                    $comisiones[] = $comision;
                }

                // Comisión car wash
                $trabajadoresCarWash = TrabajadorDetalleVenta::where('detalle_venta_id', $detalle->id)->get();
                foreach ($trabajadoresCarWash as $trabajadorDetalle) {
                    $comision = Comision::create([
                        'commissionable_id' => $trabajadorDetalle->trabajador_id,
                        'commissionable_type' => 'App\Models\Trabajador',
                        'tipo_comision' => 'carwash',
                        'monto' => $trabajadorDetalle->monto_comision,
                        'detalle_venta_id' => $detalle->id,
                        'venta_id' => $venta->id,
                        'articulo_id' => $articulo->id,
                        'estado' => rand(0, 1) ? 'pendiente' : 'pagado',
                        'fecha_calculo' => $venta->fecha,
                    ]);
                    $comisiones[] = $comision;
                }
            }
        }

        return $comisiones;
    }
}
