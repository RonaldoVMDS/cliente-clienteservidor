<!DOCTYPE html>
<html lang="en">


    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Erro {{ $statusCode }}</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    </head>


    <body>
        <div class="d-flex align-items-center justify-content-center vh-100">
            <div class="text-center">
                <h1 class="display-1 fw-bold">{{ $statusCode }}</h1>
                <p class="fs-3"> <span class="text-danger">Opps!</span> {{ $errorMessage }}.</p>
                <p class="lead">
                {{ $content }}
                  </p>
                  <a href="/" class="btn btn-primary">Entrar</a>
            </div>
        </div>
    </body>


</html>