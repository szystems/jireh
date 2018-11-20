<?php

namespace jireh;

use Illuminate\Database\Eloquent\Model;

class Vistavehiculo extends Model
{
    protected $table='vehiculo';

    protected $primaryKey='idvehiculo';

    public $timestamps=false;

    protected $fillable =[ 
        'contacto',
        'tel_contacto',
        'email_contacto',
        'nombre',
    	'marca',
    	'modelo',
    	'linea',
    	'tipo',
    	'origen',
    	'precio',
    	'puertas',
    	'motor',
    	'cilindros',
    	'combustible',
    	'millas',
    	'descripcion',
    	'ac',
    	'full_equipo',
    	'estado',
    	'autorizado',
    	'fecha',
    	'fecha_actualizacion'
    ];

    protected $guarded =[

    ];
}
