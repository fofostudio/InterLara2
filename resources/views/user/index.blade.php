@extends('layouts.app', [
    'class' => '',
    'elementActive' => 'user'
])

@section('content')
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Gestión de Usuarios</h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-primary btn-round" data-toggle="modal" data-target="#userModal" onclick="resetUserForm()">
                                    <i class="nc-icon nc-simple-add"></i> Crear Usuario
                                </button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="usersTable" class="table">
                                <thead class="text-primary">
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Email</th>
                                        <th>Rol</th>
                                        <th>Fecha de Ingreso</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                        <tr>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->role->description }}</td>
                                            <td>{{ $user->created_at->format('d/m/Y') }}</td>
                                            <td>
                                                <button type="button" class="btn btn-warning btn-icon btn-sm" onclick="editUser({{ $user->id }})">
                                                    <i class="nc-icon nc-ruler-pencil"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger btn-icon btn-sm" onclick="confirmDelete({{ $user->id }})">
                                                    <i class="nc-icon nc-simple-remove"></i>
                                                </button>
                                            </td>
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

    <!-- Modal para crear/editar usuario -->
    <div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userModalLabel">Crear/Editar Usuario</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="userForm" action="{{ route('user.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_method" value="POST">
                    <input type="hidden" name="user_id" id="user_id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="role_id">Rol</label>
                            <select class="form-control" id="role_id" name="role_id" required onchange="handleRoleChange()">
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->description }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="name">Nombre</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password">
                            <small class="form-text text-muted">Dejar en blanco para mantener la contraseña actual al editar.</small>
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation">Confirmar Contraseña</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        var table = $('#usersTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
            }
        });

        $('#userForm').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var url = form.attr('action');
            var method = form.find('input[name="_method"]').val();

            $.ajax({
                url: url,
                method: method,
                data: form.serialize(),
                success: function(response) {
                    $('#userModal').modal('hide');
                    Swal.fire('¡Éxito!', response.message, 'success');
                    table.ajax.reload();
                },
                error: function(xhr) {
                    var errors = xhr.responseJSON.errors;
                    var errorMessage = '';
                    $.each(errors, function(key, value) {
                        errorMessage += value[0] + '<br>';
                    });
                    Swal.fire('Error', errorMessage, 'error');
                }
            });
        });

        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: '{{ session('success') }}',
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: '¡Error!',
                text: '{{ session('error') }}',
            });
        @endif
    });

    function resetUserForm() {
        $('#userForm')[0].reset();
        $('#userForm').attr('action', '{{ route('user.store') }}');
        $('input[name="_method"]').val('POST');
        $('#userModalLabel').text('Crear Usuario');
        $('#user_id').val('');
        handleRoleChange();
    }

    function editUser(userId) {
        $.get('/user/' + userId + '/edit', function(data) {
            $('#userForm').attr('action', '/user/' + userId);
            $('input[name="_method"]').val('PUT');
            $('#userModalLabel').text('Editar Usuario');
            $('#user_id').val(userId);
            $('#name').val(data.name);
            $('#email').val(data.email);
            $('#role_id').val(data.role_id);
            $('#password').val('');
            $('#password_confirmation').val('');
            handleRoleChange();
            $('#userModal').modal('show');
        });
    }

    function handleRoleChange() {
        var roleId = $('#role_id').val();
        var operatorRoleId = {{ $roles->where('name', 'operator')->first()->id ?? 'null' }};

        if (roleId == operatorRoleId) {
            $('#password').val('InterLara');
            $('#password_confirmation').val('InterLara');
            $('#password, #password_confirmation').prop('disabled', true);
        } else {
            $('#password, #password_confirmation').prop('disabled', false);
            $('#password').val('');
            $('#password_confirmation').val('');
        }
    }

    function confirmDelete(userId) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "No podrás revertir esta acción.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                deleteUser(userId);
            }
        });
    }

    function deleteUser(userId) {
        $.ajax({
            url: '/user/' + userId,
            type: 'DELETE',
            data: {
                "_token": "{{ csrf_token() }}",
            },
            success: function(result) {
                Swal.fire(
                    '¡Eliminado!',
                    'El usuario ha sido eliminado.',
                    'success'
                ).then(() => {
                    $('#usersTable').DataTable().ajax.reload();
                });
            },
            error: function(xhr) {
                Swal.fire(
                    '¡Error!',
                    'Hubo un problema al eliminar el usuario.',
                    'error'
                );
            }
        });
    }
</script>
@endpush
