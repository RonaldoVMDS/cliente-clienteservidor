<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class UserController extends Controller
{
    public function showErrorPage($statusCode, $content, $errorMessage)
    {
        $content = substr($content, 0, 500);
        return view('erro', compact('statusCode', 'content', 'errorMessage'));
    }
    public function cadastrar(Request $request)
    {
        $nome = $request->input('name');
        $email = $request->input('email');
        $senha = $request->input('password');



        // Montar os dados da requisição para a API
        $data = [
            'name' => $nome,
            'email' => $email,
            'password' => md5($senha)
            // 'password' => $senha
        ];

        // Enviar a requisição para a API usando o GuzzleHttp
        $apiServer = env('API_SERVER');
        $client = new Client(['base_uri' => $apiServer]);

        try {
            $response = $client->request('POST', '/users', [
                'headers' => ['Content-Type' => 'application/json'],
                'json' => $data
            ]);

            // Verificar a resposta da API
            $statusCode = $response->getStatusCode();
            $content = $response->getBody()->getContents();

            // Se a API retornar um código 201, significa que o usuário foi criado com sucesso
            if ($statusCode == 201) {
                return view('login', ['message' => 'Usuário cadastrado com sucesso.']);
            } else {
                return $this->showErrorPage($statusCode, $content, 'O Cadastro falhou');
            }
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $statusCode = $response->getStatusCode();
                $content = $response->getBody()->getContents();
                $errorData = json_decode($content, true);
                $errorMessage = $errorData['message'];
            } else {
                $statusCode = 500;
                $errorMessage = 'O cadastro falhou';
            }

            return $this->showErrorPage($statusCode, $errorMessage, 'Erro na requisição para a API de login');
        }
    }
    public function login(Request $request)
    {
        $email = $request->input('email');
        $senha = $request->input('password');

        // Montar os dados da requisição para a API
        $data = [
            'email' => $email,
            'password' => md5($senha)
        ];

        // Enviar a requisição para a API usando o GuzzleHttp
        $apiServer = env('API_SERVER');
        $client = new Client(['base_uri' => $apiServer]);

        try {
            $response = $client->post('/login', [
                'headers' => ['Content-Type' => 'application/json'],
                'json' => $data
            ]);

            // Verificar a resposta da API
            $statusCode = $response->getStatusCode();
            $content = $response->getBody()->getContents();

            if ($statusCode == 200) {
                // Extrair os dados do usuário do conteúdo da resposta
                $userData = json_decode($content, true);
                // Passar os dados do usuário para a view "ocorrencias"
                return redirect()->route('occurrences')->with(['userData' => $userData, 'message' => 'Usuário entrou com sucesso.']);
            } else {
                return $this->showErrorPage($statusCode, $content, 'O Login falhou');
            }
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $statusCode = $response->getStatusCode();
                $content = $response->getBody()->getContents();
                $errorData = json_decode($content, true);
                $errorMessage = $errorData['message'];
            } else {
                $statusCode = 500;
                $errorMessage = 'Erro na requisição para a API de login';
            }

            return $this->showErrorPage($statusCode, $errorMessage, 'Erro na requisição para a API de login');
        }
    }

    public function logout(Request $request)
    {
        $id = $request->input('id');
        $id = intval($id);
        $token = $request->input('token');

        // Montar os dados da requisição para a API
        $requestData = [
            'id' => $id,
        ];

        // Enviar a requisição para a API usando o GuzzleHttp
        $apiServer = env('API_SERVER');
        $client = new Client(['base_uri' => $apiServer]);

        try {
            $response = $client->post("/logout", [
                'json' => $requestData,
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                ],
            ]);

            // Verificar a resposta da API
            $statusCode = $response->getStatusCode();
            $content = $response->getBody()->getContents();

            if ($statusCode == 200) {
                return view('login', ['message' => 'Usuário deslogado com sucesso']);
            } else {
                return $this->showErrorPage($statusCode, $content, 'O Logout falhou');
            }
        } catch (RequestException $e) {
            $this->showErrorPage(500, $e, 'Erro ao tentar sair da sessão');
        }
    }

    public function update(Request $request, string $idRequest)
    {
        $token = $request->input('token');
        $senha = $request->input('password');
        $email = $request->input('email');
        $name = $request->input('name');
        $id = intval($idRequest);
        if ($senha == ''){
            $senha = null;
        }else{
            $senha = md5($senha);
        }
        $data = [
            'email' => $email,
            'name' => $name,
            'password' => $senha,
        ];
        // Configurações do cliente HTTP
        $apiServer = env('API_SERVER');
        $client = new Client(['base_uri' => $apiServer]);

        try {
            $response = $client->put("users/$id", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ],
                'json' => $data
            ]);

            $statusCode = $response->getStatusCode();
            $content = $response->getBody()->getContents();

            if ($statusCode == 200) {
                // Dados do usuário atualizados com sucesso
                // Redirecionar para a página desejada ou retornar uma resposta adequada
                return redirect('/login')->with('message', 'Dados do usuário atualizados com sucesso.');
            } else {
                return $this->showErrorPage($statusCode, $content, 'Falha ao atualizar dados do usuário');
            }
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $statusCode = $response->getStatusCode();
                $content = $response->getBody()->getContents();
                $errorData = json_decode($content, true);
                $errorMessage = $errorData['message'];
            } else {
                $statusCode = 500;
                $errorMessage = 'Erro na requisição para a API de atualização do usuário';
            }

            return $this->showErrorPage($statusCode, $errorMessage, 'Erro na requisição para a API de atualização do usuário');
        }
    }
}
