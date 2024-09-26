@extends('layouts.app', [
    'class' => '',
    'elementActive' => 'excel_upload'
])

@section('content')
<div class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Subir archivos Excel</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('excel.upload.process') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="first_excel">Primer archivo Excel</label>
                            <input type="file" class="form-control-file" id="first_excel" name="first_excel" required>
                        </div>
                        <div class="form-group">
                            <label for="second_excel">Segundo archivo Excel</label>
                            <input type="file" class="form-control-file" id="second_excel" name="second_excel" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Procesar archivos</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
