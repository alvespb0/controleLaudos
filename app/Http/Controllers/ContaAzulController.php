<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

use App\Models\CA_Tokens;

class ContaAzulController extends Controller
{
    public function getAuthorizationToken(){
        $urlRedirect = env('CONTA_AZUL_REDIRECT_URI');
        $client_id = env('CLIENT_ID_CA');
        try{
            $url = "https://auth.contaazul.com/oauth2/authorize?" .
                    "response_type=code" .
                    "&client_id={$client_id}" .
                    "&redirect_uri={$urlRedirect}" .
                    "&scope=openid+profile+aws.cognito.signin.user.admin" .
                    "&state=xyz123";
            return redirect($url);
        }catch(\Exception $e){
            session()->flash('error', 'Erro ao conseguir o token de autorização do CA');
            \Log::error('Erro ao conseguir o Auth Token do Conta Azul:', [
                'error' => $e->getMessage(),
            ]);
            return redirect()->route('dashboard.show');
        }
    }
    public function getAccessToken(){
        $data = [
            'client_id' => env('CLIENT_ID_CA'),
            'client_secret' => env('SECRET_ID_CA'),
            'grant_type' => 'authorization_code',
            'code' => env('AUTH_TOKEN_CA'),
            'redirect_uri' => env('CONTA_AZUL_REDIRECT_URI')
        ];
        try{
            $response = Http::asForm()
                ->withHeaders([
                    'Authorization' => 'Basic ' . base64_encode($data['client_id'] . ':' . $data['client_secret']),
                ])
                ->post('https://auth.contaazul.com/oauth2/token', $data);

            if($response->ok()){
                return $response->json();
            } else {
                \Log::error('Erro ao obter access token', ['status' => $response->status(), 'body' => $response->body()]);
                return $response->status();
            }
        }catch(\Exception $e){
            session()->flash('error', 'Erro ao conseguir o token de acesso e refresh do CA');
            \Log::error('Erro ao conseguir o access Token do Conta Azul:', [
                'error' => $e->getMessage(),
            ]);
            return redirect()->route('dashboard.show');
        }
    }

    public function saveOrRefreshToken(){
        $ca_tokens = CA_Tokens::all();

        if($ca_tokens->isEmpty()){ //verifica se já há algum token cadastrado, só pode haver uma linha no banco
            $data = $this->getAccessToken();
            CA_Tokens::create([
                'access_token' => $data['access_token'],
                'refresh_token' => $data['refresh_token'],
                'expires_at' => now()->addSeconds($data['expires_in'])
            ]);
        }else{
            $token = $ca_tokens->first(); //all retorna uma collection

            $data = [
                'client_id' => env('CLIENT_ID_CA'),
                'client_secret' => env('SECRET_ID_CA'),
                'grant_type' => 'refresh_token',
                'refresh_token' => $token->refresh_token
            ];
            $response = Http::asForm()
                ->withHeaders([
                    'Authorization' => 'Basic ' . base64_encode($data['client_id'] . ':' . $data['client_secret']),
                ])
                ->post('https://auth.contaazul.com/oauth2/token', $data);

            if($response->ok()){
                $dataNova = $response->json();
                $token->update([
                    'access_token' => $dataNova['access_token'],
                    'refresh_token' => $dataNova['refresh_token'] ?? $token->refresh_token,
                    'expires_at' => now()->addSeconds($dataNova['expires_in'])
                ]);
            }else {
                \Log::error('Erro ao conectar com o conta azul', ['status' => $response->status(), 'body' => $response->body()]);
            }
        }

    }
}
