<!DOCTYPE html>
<html>
<head>
    <title>Ocorrências</title>
    <!-- Adicione os links para os arquivos CSS do Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Adicione o link para o arquivo CSS do Toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <style>
        .token {
            font-size: 16px;
            color: #777;
            word-wrap: break-word;
            white-space: pre-line;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <!-- <p class="token">{{ $userData['token'] }}</p> -->

        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Dados do Usuário</h4>
                <p class="card-text"><strong>ID:</strong> {{ $userData['id'] }}</p>
                <p class="card-text"><strong>Nome:</strong> {{ $userData['name'] }}</p>
                <p class="card-text"><strong>E-mail:</strong> {{ $userData['email'] }}</p>
            </div>
        </div>

        <form id="logoutForm" action="/logout" method="POST">
            @csrf
            <input type="hidden" name="id" value="{{ $userData['id'] }}">
            <input type="hidden" name="token" value="{{ $userData['token'] }}">
            <button type="submit" class="btn btn-primary mt-2">Logout</button>
        </form>

    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
