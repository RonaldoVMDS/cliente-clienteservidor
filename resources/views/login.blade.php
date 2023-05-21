<!DOCTYPE html>
<html>
<head>
	<title>Entrar</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
	<style>
		body {
			background-color: #f7f7f7;
			font-family: Arial, sans-serif;
		}
	</style>
</head>
<body>
    <div class="container mt-4 w-25">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4 text-center">Entrar</h4>      
                <form action="/login" method="POST">
                    @csrf
                    <!-- Email input -->
                    <div class="form-outline mb-4">
                        <input type="email" name="email" id="email" class="form-control" />
                        <label class="form-label" for="email">Email</label>
                    </div>

                    <!-- Password input -->
                    <div class="form-outline mb-4">
                        <input type="password" name="password" id="password" class="form-control" />
                        <label class="form-label" for="password">Senha</label>
                    </div>

                    <!-- Submit button -->
                    <button type="submit" class="btn btn-primary btn-block mb-4">Logar</button>

                    <!-- Register buttons -->
                    <div class="text-center">
                        <p>Não tem uma conta? <a href="/cadastro" >Registrar</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>