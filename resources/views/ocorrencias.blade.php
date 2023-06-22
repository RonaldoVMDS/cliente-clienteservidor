<!DOCTYPE html>
<html>

<head>
    <title>Ocorrências</title>
    <!-- Adicione os links para os arquivos CSS do Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Adicione o link para o arquivo CSS do Toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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
            $('#deleteEmployeeModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget); // Botão que acionou o modal
                var ocorrenciaId = button.data('id'); // Extrai o ID da ocorrência do atributo data-id

                // Atualiza o valor do campo oculto com o ID da ocorrência
                $('#deleteEmployeeModal input[name="ocorrencia_id"]').val(ocorrenciaId);
            });
            $('#editEmployeeModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget); // Botão que acionou o modal
                var ocorrenciaId = button.data('id'); // Extrai o ID da ocorrência do atributo data-id

                // Atualiza o valor do campo oculto com o ID da ocorrência
                $('#editEmployeeModal input[name="ocorrencia_id"]').val(ocorrenciaId);
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
                                <form method="POST" action="occurrences/users/{{ $userData['id'] }}">
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
                                    <a href="#editEmployeeModal" class="edit" data-toggle="modal" data-id="{{ $ocorrencia['id'] }}"><i class="material-icons" data-toggle="tooltip" title="Editar">&#xE254;</i></a>
                                    <a href="#deleteEmployeeModal" class="delete" data-toggle="modal" data-id="{{ $ocorrencia['id'] }}"><i class="material-icons" data-toggle="tooltip" title="Excluir">&#xE872;</i></a>
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
                    <form method="POST" action="/occurrences/">
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
                    <form method="post" action="{{ route('ocorrencias.update') }}">
                        @csrf
                        <input type="hidden" name="ocorrencia_id">
                        <input type="hidden" name="user_id" value="{{ $userData['id'] }}">
                        <input type="hidden" name="token" value="{{ $userData['token'] }}">
                        <input type="hidden" name="name" value="{{ $userData['name'] }}">
                        <input type="hidden" name="email" value="{{ $userData['email'] }}">
                        <div class="modal-header">
                            <h4 class="modal-title">Editar Ocorrência</h4>
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
                            <input type="submit" class="btn btn-info" value="Atualizar">
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Delete Modal HTML -->
        <div id="deleteEmployeeModal" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="post" action="{{ route('ocorrencias.delete') }}">
                        @csrf
                        <input type="hidden" name="ocorrencia_id">
                        <input type="hidden" name="user_id" value="{{ $userData['id'] }}">
                        <input type="hidden" name="token" value="{{ $userData['token'] }}">
                        <input type="hidden" name="name" value="{{ $userData['name'] }}">
                        <input type="hidden" name="email" value="{{ $userData['email'] }}">
                        <div class="modal-header">
                            <h4 class="modal-title">Excluir ocorrência</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        </div>
                        <div class="modal-body">
                            <p>Tem certeza que deseja apagar este registro?</p>
                            <p class="text-warning"><small>Essa ação não pode ser desfeita.</small></p>
                        </div>
                        <div class="modal-footer">
                            <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancelar">
                            <input type="submit" class="btn btn-danger" value="Excluir">
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