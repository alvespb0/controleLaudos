<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

use Google_Client;
use Google_Service_Calendar;

class GoogleController extends Controller
{
    public function loginOAuth2(){
        $client = new Google_Client();
        $client->setAuthConfig(storage_path('app/google-calendar/credentials.json'));
        $client->setRedirectUri(route('callback.google'));
        $client->addScope(Google_Service_Calendar::CALENDAR);
        $client->setAccessType('offline'); // permite gerar refresh_token
        $client->setPrompt('consent');    // garante refresh_token na primeira conexão

        return redirect()->away($client->createAuthUrl());
    }

    public function callbackGoogle(){
        $client = new Google_Client();
        $client->setAuthConfig(storage_path('app/google-calendar/credentials.json'));
        $client->setRedirectUri(route('callback.google'));

        if (request()->has('code')) {
            $token = $client->fetchAccessTokenWithAuthCode(request('code'));

            if (!isset($token['error'])) {
                // Salva os tokens vinculados ao usuário logado
                $user = auth()->user();
                $user->update([
                    'google_access_token' => $token['access_token'],
                    'google_refresh_token' => $token['refresh_token'] ?? $user->google_refresh_token,
                    'google_token_expires' => now()->addSeconds($token['expires_in']),
                ]);

                return redirect()->route('dashboard.show')->with('mensagem', 'Google Calendar conectado com sucesso!');
            }
        }

        return redirect()->route('dashboard.show')->with('error', 'Erro ao conectar ao Google Calendar');
    }

    public function listEvents(){
        $user = auth()->user();
        if (!$user->google_access_token || !$user->google_refresh_token) {
            return redirect()->route('dashboard.show')
                ->with('error', 'Você ainda não conectou sua conta Google Calendar. Clique no botão para conectar.');
        }
        $client = new Google_Client();
        $client->setAuthConfig(storage_path('app/google-calendar/credentials.json'));
        $client->setAccessToken([
            'access_token' => $user->google_access_token,
            'refresh_token' => $user->google_refresh_token,
            'expires_in' => now()->diffInSeconds($user->google_token_expires),
        ]);

        // Verifica se o token expirou
        if ($client->isAccessTokenExpired()) {
            $client->fetchAccessTokenWithRefreshToken($user->google_refresh_token);
            $newToken = $client->getAccessToken();
            $user->update([
                'google_access_token' => $newToken['access_token'],
                'google_token_expires' => now()->addSeconds($newToken['expires_in']),
            ]);
        }

        $service = new Google_Service_Calendar($client);
        $calendarId = 'primary'; // agenda principal do usuário

        // Buscar próximos 10 eventos
        $events = $service->events->listEvents($calendarId, [
            'maxResults' => 10,
            'orderBy' => 'startTime',
            'singleEvents' => true,
            'timeMin' => date('c'), // apenas eventos futuros
        ]);

        return view('/Google/Calendar', compact('events'));
    }

}
