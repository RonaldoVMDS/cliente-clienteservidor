<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use DateTime;
use DateTimeZone;

class OccurrenceController extends Controller
{
    public function showErrorPage($statusCode, $content, $errorMessage)
    {
        $content = substr($content, 0, 500);
        return view('erro', compact('statusCode', 'content', 'errorMessage'));
    }
    public function index(Request $request)
    {
        // Obter os dados do usuário da sessão
        $userData = $request->session()->get('userData');

        // Verificar se o usuário está logado
        if (!$userData) {
            // return redirect()->route('login')->with('error', 'Você precisa estar logado para acessar as ocorrências.');
        }

        // Enviar a requisição para a API para obter as ocorrências
        $apiServer = env('API_SERVER');
        $client = new Client(['base_uri' => $apiServer]);

        try {
            $response = $client->get('/occurrences', []);

            $statusCode = $response->getStatusCode();
            $content = $response->getBody()->getContents();

            if ($statusCode == 200) {
                $ocorrencias = json_decode($content, true);

                // Renderizar a view de ocorrências com os dados
                return view('ocorrencias', ['userData' => $userData, 'ocorrencias' => $ocorrencias]);
            } else {
                return $this->showErrorPage($statusCode, $content, 'Falha ao obter as ocorrências');
            }
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $statusCode = $response->getStatusCode();
                $content = $response->getBody()->getContents();
                $errorData = json_decode($content, true);
                $errorMessage = $errorData;
            } else {
                $statusCode = 500;
                $errorMessage = 'Erro na requisição para a API de ocorrências';
            }

            return $this->showErrorPage($statusCode, $errorMessage, 'Erro na requisição para a API de ocorrências');
        }
    }
    public function getUserOccurrences(Request $request, $idRequest)
    {
        try {
            $id = $idRequest;

            // Verificar se o usuário está autenticado
            // if (!$request->user()) {
            //     return redirect('/login');
            // }

            // Obter o token do usuário autenticado
            $id = intval($id);
            $token = $request->input('token');
            $name = $request->input('name');
            $email = $request->input('email');
            $userData = [
                'id' => $id,
                'name' => $name,
                'email' => $email,
                'token' => $token,
            ];

            // Fazer a requisição para a rota do servidor de ocorrências do usuário
            $apiServer = env('API_SERVER');
            $client = new Client(['base_uri' => $apiServer]);

            $response = $client->get("/occurrences/users/$id", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ],
            ]);

            // Verificar a resposta da API
            $statusCode = $response->getStatusCode();
            $content = $response->getBody()->getContents();

            if ($statusCode == 200) {
                // Extrair as ocorrências do conteúdo da resposta
                $ocorrencias = json_decode($content, true);
                // Retornar a view com as ocorrências do usuário
                return view('ocorrencias', ['userData' => $userData, 'ocorrencias' => $ocorrencias]);
            } else {
                return $this->showErrorPage($statusCode, $content, 'Erro ao obter ocorrências do usuário');
            }
        } catch (\Exception $e) {
            return $this->showErrorPage(500, 'Erro no servidor: ' . $e->getMessage(), 'Erro no servidor');
        }
    }
    public function store(Request $request)
    {
        $registered_at = $request->input('registered_at');
        $registered_at = DateTime::createFromFormat('Y-m-d\TH:i', $registered_at);

        // Adicione os segundos e milissegundos à hora
        $registered_at->setTime($registered_at->format('H'), $registered_at->format('i'), 0);

        // Converta para o fuso horário UTC
        $registered_at->setTimezone(new DateTimeZone('UTC'));

        // Agora você tem o valor no formato desejado
        $registered_at = $registered_at->format('Y-m-d\TH:i:s.v\Z');


        $local = $request->input('local');
        $km = $request->input('km');
        $occurrence_type = $request->input('occurrence_type');
        $user_id = $request->input('user_id');
        $id = intval($user_id);
        $token = $request->input('token');
        $name = $request->input('name');
        $email = $request->input('email');
        $userData = [
            'id' => $id,
            'name' => $name,
            'email' => $email,
            'token' => $token,
        ];
        $data = [
            'registered_at' => $registered_at,
            'local' => $local,
            'km' => intval($km),
            'occurrence_type' => intval($occurrence_type),
            'user_id' => intval($user_id)
        ];

        // Enviar a requisição para a API para obter as ocorrências
        $apiServer = env('API_SERVER');
        $client = new Client(['base_uri' => $apiServer]);
        $token = $request->input('token');

        try {
            $response = $client->post("/occurrences", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ],
                'json' => $data
            ]);

            $statusCode = $response->getStatusCode();
            $content = $response->getBody()->getContents();

            if ($statusCode == 201) {
                $ocorrencias = json_decode($content, true);

                // Renderizar a view de ocorrências com os dados
                return redirect()->route('occurrences')->with('userData', $userData);
            } else {
                return $this->showErrorPage($statusCode, $content, 'Falha ao obter as ocorrências');
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
                $errorMessage = 'Erro na requisição para a API de ocorrências';
            }

            return $this->showErrorPage($statusCode, $errorMessage, 'Erro na requisição para a API de ocorrências');
        }
    }
    public function delete(Request $request)
    {
        $token = $request->input('token');
        $ocrId = $request->input('ocorrencia_id');
        $data = [
            'occurrenceId' => $ocrId,
        ];
        // Enviar a requisição para a API para obter as ocorrências
        $apiServer = env('API_SERVER');
        $client = new Client(['base_uri' => $apiServer]);
        $token = $request->input('token');
        $user_id = $request->input('user_id');
        $id = intval($user_id);
        $token = $request->input('token');
        $name = $request->input('name');
        $email = $request->input('email');
        $userData = [
            'id' => $id,
            'name' => $name,
            'email' => $email,
            'token' => $token,
        ];

        try {
            $response = $client->delete("/occurrences/$ocrId", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ],
                'json' => $data
            ]);

            $statusCode = $response->getStatusCode();
            $content = $response->getBody()->getContents();

            if ($statusCode == 200) {
                $ocorrencias = json_decode($content, true);

                // Renderizar a view de ocorrências com os dados
                return redirect()->route('occurrences')->with([
                    'userData' => $userData,
                    'ocorrencias' => $ocorrencias
                ]);
            } else {
                return $this->showErrorPage($statusCode, $content, 'Falha ao obter as ocorrências');
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
                $errorMessage = 'Erro na requisição para a API de ocorrências';
            }

            return $this->showErrorPage($statusCode, $errorMessage, 'Erro na requisição para a API de ocorrências');
        }
    }
    public function update(Request $request)
    {
        $registered_at = $request->input('registered_at');
        $registered_at = DateTime::createFromFormat('Y-m-d\TH:i', $registered_at);

        // Adicione os segundos e milissegundos à hora
        $registered_at->setTime($registered_at->format('H'), $registered_at->format('i'), 0);

        // Converta para o fuso horário UTC
        $registered_at->setTimezone(new DateTimeZone('UTC'));

        // Agora você tem o valor no formato desejado
        $registered_at = $registered_at->format('Y-m-d\TH:i:s.v\Z');


        $local = $request->input('local');
        $km = $request->input('km');
        $occurrence_type = $request->input('occurrence_type');
        $user_id = $request->input('user_id');
        $id = intval($user_id);
        $token = $request->input('token');
        $name = $request->input('name');
        $email = $request->input('email');
        $ocrId = $request->input('ocorrencia_id');
        $userData = [
            'id' => $id,
            'name' => $name,
            'email' => $email,
            'token' => $token,
        ];
        $data = [
            'registered_at' => $registered_at,
            'local' => $local,
            'km' => intval($km),
            'occurrence_type' => intval($occurrence_type),
            'user_id' => intval($user_id)
        ];

        // Enviar a requisição para a API para obter as ocorrências
        $apiServer = env('API_SERVER');
        $client = new Client(['base_uri' => $apiServer]);
        $token = $request->input('token');

        try {
            $response = $client->put("/occurrences/$ocrId", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ],
                'json' => $data
            ]);

            $statusCode = $response->getStatusCode();
            $content = $response->getBody()->getContents();

            if ($statusCode == 200) {
                $ocorrencias = json_decode($content, true);

                // Renderizar a view de ocorrências com os dados
                return redirect()->route('occurrences')->with('userData', $userData);
            } else {
                return $this->showErrorPage($statusCode, $content, 'Falha ao obter as ocorrências');
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
                $errorMessage = 'Erro na requisição para a API de ocorrências';
            }

            return $this->showErrorPage($statusCode, $errorMessage, 'Erro na requisição para a API de ocorrências');
        }
    }
}
