<!DOCTYPE html>
<html>

<head>
    <title>Ocorrências</title>
    <!-- Adicione os links para os arquivos CSS do Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Adicione o link para o arquivo CSS do Toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <!-- Outras tags do cabeçalho aqui -->
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Inclua o arquivo CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <!-- Inclua o arquivo JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        $(document).ready(function() {
            // Activate tooltip
            $('[data-toggle="tooltip"]').tooltip();

            // Select/Deselect checkboxes
            var checkbox = $('table tbody input[type="checkbox"]');
            $("#selectAll").click(function() {
                if (this.checked) {
                    checkbox.each(function() {
                        this.checked = true;
                    });
                } else {
                    checkbox.each(function() {
                        this.checked = false;
                    });
                }
            });
            checkbox.click(function() {
                if (!this.checked) {
                    $("#selectAll").prop("checked", false);
                }
            });
        });
    </script>
</head>

<body>
    @php
    $ocorrenciaDescricao = [
    1 => 'Atropelamento',
    2 => 'Deslizamento',
    3 => 'Colisão frontal',
    4 => 'Capotagem',
    5 => 'Saída de pista',
    6 => 'Batida em objeto fixo',
    7 => 'Veículo avariado',
    8 => 'Colisão com motocicletas',
    9 => 'Colisão no mesmo sentido ou transversal',
    10 => 'Construção',
    ];
    @endphp
    <div class="container mt-4">
        @if (isset($userData))
        <div class="card">
            <div class="card-body">
                <div class='row'>
                    <div class='col-6'>

                        <h4 class="card-title">Dados do Usuário</h4>
                        <p class="card-text"><strong>ID:</strong> {{ $userData['id'] }}</p>
                        <p class="card-text"><strong>Nome:</strong> {{ $userData['name'] }}</p>
                        <p class="card-text"><strong>E-mail:</strong> {{ $userData['email'] }}</p>
                    </div>
                    <div class='col-6'>
                        <button type="button" class="btn btn-success mt-2" data-toggle="modal" data-target="#editUserModal">
                            Editar meus dados
                        </button>
                        <form method="POST" action="/logout">
                            @csrf
                            <input type="hidden" name="id" value="{{ $userData['id'] }}">
                            <input type="hidden" name="token" value="{{ $userData['token'] }}">
                            <button type="submit" class="btn btn-primary mt-2">Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="container-xl">
            <div class="table-responsive">
                <div class="table-wrapper">
                    <div class="table-title">
                        <div class="row">
                            <div class="col-sm-6">
                                <h2><b>SAOITR</b></h2>
                            </div>
                            <div class="col-sm-6">
                                @if (isset($userData))
                                <a href="#addOccurrenceModal" class="btn btn-success" data-toggle="modal"><i class="material-icons">&#xE147;</i> <span>Adicionar ocorrência</span></a>
                                <form method="GET" action="occurrences/{{ $userData['id'] }}">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $userData['id'] }}">
                                    <input type="hidden" name="token" value="{{ $userData['token'] }}">
                                    <input type="hidden" name="name" value="{{ $userData['name'] }}">
                                    <input type="hidden" name="email" value="{{ $userData['email'] }}">
                                    <button type="submit" class="btn btn-info">Ver minhas ocorrências</a>
                                </form>
                                @else
                                <a href="/login" class="btn btn-success"><i class="material-icons">person</i>Entrar na minha conta</span></a>
                                @endif
                            </div>
                        </div>
                    </div>
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Data de Registro</th>
                                <th>Local</th>
                                <th>Tipo de ocorrência</th>
                                <th>KM</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (isset($ocorrencias) && count($ocorrencias) > 0)
                            @foreach ($ocorrencias as $ocorrencia)
                            <tr>
                                <td>{{ date('d/m/Y H:i:s', strtotime($ocorrencia['registered_at'])) }}</td>
                                <td>{{ $ocorrencia['local'] }}</td>
                                <td>
                                    @isset($ocorrenciaDescricao[$ocorrencia['occurrence_type']])
                                    {{ $ocorrenciaDescricao[$ocorrencia['occurrence_type']] }}
                                    @else
                                    Desconhecido
                                    @endisset
                                </td>
                                <td>{{ $ocorrencia['km'] }}</td>
                                <td>
                                    @if (isset($userData) && $ocorrencia['user_id'] == $userData['id'] )
                                    <a href="#editEmployeeModal" class="edit" data-toggle="modal"><i class="material-icons" data-toggle="tooltip" title="Edit">&#xE254;</i></a>
                                    <a href="#deleteEmployeeModal" class="delete" data-toggle="modal"><i class="material-icons" data-toggle="tooltip" title="Delete">&#xE872;</i></a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td colspan="4">Não há ocorrências disponíveis.</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
        <!-- Edit Modal HTML -->
        @if (isset($userData))
        <!-- Modal de edição de dados do usuário -->
        <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editUserModalLabel">Editar Dados do Usuário</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Formulário de edição de dados do usuário -->
                        <form action="/user/{{ $userData['id'] }}" method="POST">
                            @csrf
                            <input type="hidden" name="id" value="{{ $userData['id'] }}">
                            <input type="hidden" name="token" value="{{ $userData['token'] }}">
                            <div class="form-group">
                                <label for="name">Nome:</label>
                                <input type="text" class="form-control" id="name" name="name" value="" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email:</label>
                                <input type="email" class="form-control" id="email" name="email" value="" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Nova Senha:</label>
                                <input type="password" class="form-control" id="password" name="password">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Add Occurrence Modal HTML -->
        <div id="addOccurrenceModal" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="{{ route('ocorrencias.store') }}">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ $userData['id'] }}">
                        <input type="hidden" name="token" value="{{ $userData['token'] }}">
                        <input type="hidden" name="name" value="{{ $userData['name'] }}">
                        <input type="hidden" name="email" value="{{ $userData['email'] }}">
                        <div class="modal-header">
                            <h4 class="modal-title">Adicionar Ocorrência</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Data de Registro</label>
                                <input type="datetime-local" name='registered_at' class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Local</label>
                                <input type="text" name="local" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Tipo de Ocorrência</label>
                                <select class="form-control" name="occurrence_type" required>
                                    <option value="1">Atropelamento</option>
                                    <option value="2">Deslizamento</option>
                                    <option value="3">Colisão frontal</option>
                                    <option value="4">Capotagem</option>
                                    <option value="5">Saída de pista</option>
                                    <option value="6">Batida em objeto fixo</option>
                                    <option value="7">Veículo avariado</option>
                                    <option value="8">Colisão com motocicletas</option>
                                    <option value="9">Colisão no mesmo sentido ou transversal</option>
                                    <option value="10">Construção</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>KM</label>
                                <input type="text" name="km" class="form-control" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancelar">
                            <input type="submit" class="btn btn-success" value="Adicionar">
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Edit Modal HTML -->
        <div id="editEmployeeModal" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form>
                        <div class="modal-header">
                            <h4 class="modal-title">Edit Employee</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Address</label>
                                <textarea class="form-control" required></textarea>
                            </div>
                            <div class="form-group">
                                <label>Phone</label>
                                <input type="text" class="form-control" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
                            <input type="submit" class="btn btn-info" value="Save">
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Delete Modal HTML -->
        <div id="deleteEmployeeModal" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form>
                        <div class="modal-header">
                            <h4 class="modal-title">Delete Employee</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to delete these Records?</p>
                            <p class="text-warning"><small>This action cannot be undone.</small></p>
                        </div>
                        <div class="modal-footer">
                            <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
                            <input type="submit" class="btn btn-danger" value="Delete">
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif

    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>

</html>