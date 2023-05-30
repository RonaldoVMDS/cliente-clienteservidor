<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class LoginController extends Controller
{
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
                return view('ocorrencias', ['userData' => $userData]);
            } else {
                return view('erro', ['mensagem' => 'Não foi possível cadastrar o usuário.']);
            }
        } catch (RequestException $e) {
            return view('erro', ['mensagem' => 'Erro na requisição para a API de login.']);
        }
    }

    public function logout(Request $request)
    {
        $id = $request->input('id');
        $token = $request->input('token');

        // Montar os dados da requisição para a API
        $requestData = [
            'id' => $id,
        ];

        // Enviar a requisição para a API usando o GuzzleHttp
        $apiServer = env('API_SERVER');
        $client = new Client(['base_uri' => $apiServer]);
        
        try {
            $response = $client->get("/logout", [
                'json' => $requestData,
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                ],
            ]);
    
            // Verificar a resposta da API
            $statusCode = $response->getStatusCode();
            $content = $response->getBody()->getContents();
    
            if ($statusCode == 200) {
                return view('login', ['mensagem' => 'Usuário deslogado com sucesso']);
            } else {
                return view('login', ['mensagem' => 'O status recebido não foi 200']);
            }
        } catch (RequestException $e) {
            echo $e->getMessage();
            // return view('erro', ['mensagem' => "Erro na requisição para a API de logout"]);
        }
    }
}
