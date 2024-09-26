@extends('layouts.app', [
    'class' => '',
    'elementActive' => 'excel_results'
])

@section('content')
<div class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Resultados del cruce de datos</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="text-primary">
                                <tr>
                                    <th>Número de Guía</th>
                                    <th>Ciudad Origen</th>
                                    <th>Ciudad Destino</th>
                                    <th>Fecha de Venta</th>
                                    <th>Valor Total</th>
                                    <th>Creado Por</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($crossedData as $data)
                                <tr>
                                    <td>{{ $data->numero_guia }}</td>
                                    <td>{{ $data->ciudad_origen }}</td>
                                    <td>{{ $data->ciudad_destino }}</td>
                                    <td>{{ $data->fecha_venta }}</td>
                                    <td>{{ $data->valor_total }}</td>
                                    <td>{{ $data->ADM_CreadoPor }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
