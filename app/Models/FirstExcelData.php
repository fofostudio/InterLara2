<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FirstExcelData extends Model
{
    use HasFactory;

    protected $fillable = [
        'regional_origen',
        'codigo_ciudad_origen',
        'ciudad_origen',
        'regional_destino',
        'codigo_ciudad_destino',
        'ciudad_destino',
        'numero_guia',
        'fecha_venta',
        'fecha_edicion',
        'tipo_cliente',
        'desc_tipo_entrega',
        'unidad_negocio',
        'id_centro_costos_origen',
        'id_centro_servicios_origen',
        'nombre_centro_servicio_origen',
        'id_centro_costos_destino',
        'id_centro_servicios_destino',
        'nombre_centro_servicio_destino',
        'numero_identificacion_origen',
        'id_convenio',
        'nit_convenio',
        'id_sucursal',
        'id_contrato',
        'razon_social',
        'nombre_sucursal',
        'nombre_contrato',
        'piezas',
        'peso',
        'valor_comercial',
        'valor_transporte',
        'valor_seguro',
        'valor_adicionales',
        'valor_descuento',
        'valor_contrapago',
        'valor_total',
        'tipo_servicio',
        'tipo_envio',
        'tipo_identificacion_remitente',
        'identificacion_remitente',
        'nombre_remitente',
        'telefono_remitente_origen',
        'direccion_remitente',
        'tipo_identificacion_destinatario',
        'identificacion_destinatario',
        'nombre_destinatario',
        'telefono_destinatario',
        'direccion_destinatario',
        'contenido',
        'forma_pago',
        'es_alcobro',
        'estado_envio',
        'tiempo_gestion',
        'fecha_estimada_entrega',
        'ultimo_estado',
        'fecha_ultimo_estado',
        'nom_ciudad_ultimo_estado',
        'nom_racol_ultimo_estado',
        'largo',
        'ancho',
        'alto',
        'contrapago',
        'planilla_asignacion_envio',
        'fecha_asignacion',
        'cedula_mensajero',
        'nombre_mensajero',
        'fecha_descargue',
        'fecha_digitalizacion',
        'digitalizacion_prueba',
        'fecha_recibido',
        'fecha_archivo',
        'archivo_prueba',
    ];

    protected $casts = [
        'fecha_venta' => 'datetime',
        'fecha_edicion' => 'datetime',
        'es_alcobro' => 'boolean',
        'fecha_estimada_entrega' => 'datetime',
        'fecha_ultimo_estado' => 'datetime',
        'fecha_asignacion' => 'datetime',
        'fecha_descargue' => 'datetime',
        'fecha_digitalizacion' => 'datetime',
        'fecha_recibido' => 'datetime',
        'fecha_archivo' => 'datetime',
    ];
    public function point()
    {
        return $this->belongsTo(Point::class);
    }

    public function secondExcelData()
    {
        return $this->hasOne(SecondExcelData::class, 'ADM_NumeroGuia', 'numero_guia');
    }
}
