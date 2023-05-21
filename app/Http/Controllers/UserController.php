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

        try{
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
        }
        catch (RequestException $e) {
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
                return view('ocorrencias', ['userData' => $userData, 'message' => 'Usuário entrou com sucesso.']);
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

    

}
