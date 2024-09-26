<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFirstExcelDataTable extends Migration
{
    public function up()
    {
        Schema::create('first_excel_data', function (Blueprint $table) {
            $table->id();
            $table->string('regional_origen')->nullable();
            $table->string('codigo_ciudad_origen')->nullable();
            $table->string('ciudad_origen')->nullable();
            $table->string('regional_destino')->nullable();
            $table->string('codigo_ciudad_destino')->nullable();
            $table->string('ciudad_destino')->nullable();
            $table->string('numero_guia')->unique();
            $table->dateTime('fecha_venta')->nullable();
            $table->dateTime('fecha_edicion')->nullable();
            $table->string('tipo_cliente')->nullable();
            $table->string('desc_tipo_entrega')->nullable();
            $table->string('unidad_negocio')->nullable();
            $table->string('id_centro_costos_origen')->nullable();
            $table->string('id_centro_servicios_origen')->nullable();
            $table->string('nombre_centro_servicio_origen')->nullable();
            $table->string('id_centro_costos_destino')->nullable();
            $table->string('id_centro_servicios_destino')->nullable();
            $table->string('nombre_centro_servicio_destino')->nullable();
            $table->string('numero_identificacion_origen')->nullable();
            $table->string('id_convenio')->nullable();
            $table->string('nit_convenio')->nullable();
            $table->string('id_sucursal')->nullable();
            $table->string('id_contrato')->nullable();
            $table->string('razon_social')->nullable();
            $table->string('nombre_sucursal')->nullable();
            $table->string('nombre_contrato')->nullable();
            $table->integer('piezas')->nullable();
            $table->decimal('peso', 10, 2)->nullable();
            $table->decimal('valor_comercial', 12, 2)->nullable();
            $table->decimal('valor_transporte', 12, 2)->nullable();
            $table->decimal('valor_seguro', 12, 2)->nullable();
            $table->decimal('valor_adicionales', 12, 2)->nullable();
            $table->decimal('valor_descuento', 12, 2)->nullable();
            $table->decimal('valor_contrapago', 12, 2)->nullable();
            $table->decimal('valor_total', 12, 2)->nullable();
            $table->string('tipo_servicio')->nullable();
            $table->string('tipo_envio')->nullable();
            $table->string('tipo_identificacion_remitente')->nullable();
            $table->string('identificacion_remitente')->nullable();
            $table->string('nombre_remitente')->nullable();
            $table->string('telefono_remitente_origen')->nullable();
            $table->string('direccion_remitente')->nullable();
            $table->string('tipo_identificacion_destinatario')->nullable();
            $table->string('identificacion_destinatario')->nullable();
            $table->string('nombre_destinatario')->nullable();
            $table->string('telefono_destinatario')->nullable();
            $table->string('direccion_destinatario')->nullable();
            $table->text('contenido')->nullable();
            $table->string('forma_pago')->nullable();
            $table->boolean('es_alcobro')->nullable();
            $table->string('estado_envio')->nullable();
            $table->integer('tiempo_gestion')->nullable();
            $table->dateTime('fecha_estimada_entrega')->nullable();
            $table->string('ultimo_estado')->nullable();
            $table->dateTime('fecha_ultimo_estado')->nullable();
            $table->string('nom_ciudad_ultimo_estado')->nullable();
            $table->string('nom_racol_ultimo_estado')->nullable();
            $table->decimal('largo', 8, 3)->nullable();
            $table->decimal('ancho', 8, 3)->nullable();
            $table->decimal('alto', 8, 3)->nullable();
            $table->string('contrapago')->nullable();
            $table->string('planilla_asignacion_envio')->nullable();
            $table->dateTime('fecha_asignacion')->nullable();
            $table->string('cedula_mensajero')->nullable();
            $table->string('nombre_mensajero')->nullable();
            $table->dateTime('fecha_descargue')->nullable();
            $table->dateTime('fecha_digitalizacion')->nullable();
            $table->string('digitalizacion_prueba')->nullable();
            $table->dateTime('fecha_recibido')->nullable();
            $table->dateTime('fecha_archivo')->nullable();
            $table->string('archivo_prueba')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('first_excel_data');
    }
}
