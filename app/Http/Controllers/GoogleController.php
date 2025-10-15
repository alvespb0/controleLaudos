<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Controller para integração com Google Calendar
 * 
 * Gerencia a autenticação OAuth2, listagem de eventos e criação de novos eventos
 * no Google Calendar do usuário. Utiliza a API do Google Calendar v3.
 * 
 * @package App\Http\Controllers
 * @author Sistema de Controle de Laudos
 * @version 1.0
 */
class GoogleController extends Controller
{
    /**
     * Inicia o processo de autenticação OAuth2 com o Google Calendar
     * 
     * Configura o cliente Google com as credenciais necessárias e redireciona
     * o usuário para a página de autorização do Google.
     * 
     * @return \Illuminate\Http\RedirectResponse Redirecionamento para URL de autorização do Google
     */
    public function loginOAuth2(){
        $client = new Google_Client();
        $client->setAuthConfig(storage_path('app/google-calendar/credentials.json'));
        $client->setRedirectUri(route('callback.google'));
        $client->addScope(Google_Service_Calendar::CALENDAR);
        $client->setAccessType('offline');
        $client->setPrompt('consent');

        return redirect()->away($client->createAuthUrl());
    }

    /**
     * Processa o callback de autenticação OAuth2 do Google
     * 
     * Recebe o código de autorização do Google, troca por tokens de acesso
     * e salva os tokens no banco de dados vinculados ao usuário logado.
     * 
     * @return \Illuminate\Http\RedirectResponse Redirecionamento para dashboard com mensagem de status
     */
    public function callbackGoogle(){
        $client = new Google_Client();
        $client->setAuthConfig(storage_path('app/google-calendar/credentials.json'));
        $client->setRedirectUri(route('callback.google'));

        if (request()->has('code')) {
            $token = $client->fetchAccessTokenWithAuthCode(request('code'));

            if (!isset($token['error'])) {
                $user = auth()->user();
                $user->update([
                    'google_access_token' => $token['access_token'],
                    'google_refresh_token' => $token['refresh_token'] ?? $user->google_refresh_token,
                    'google_token_expires' => now()->addSeconds($token['expires_in']),
                ]);

                session()->flash('mensagem', 'Conta google conectada com sucesso!');

                return redirect()->route('dashboard.show');
            }
        }

        session()->flash('error','Erro ao conectar a conta google');
            
        return redirect()->route('dashboard.show');
    }

    /**
     * Lista os próximos eventos do Google Calendar do usuário
     * 
     * Verifica se o usuário possui tokens válidos do Google, renova automaticamente
     * se necessário e busca os próximos 10 eventos futuros da agenda principal.
     * Os eventos são ordenados por data de início e incluem apenas eventos únicos.
     * 
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     *         Retorna a view 'Google/Calendar' com os eventos ou redireciona para
     *         dashboard com mensagem de erro se não houver tokens válidos
     * 
     * @throws \Exception Se houver erro na comunicação com a API do Google
     */
    public function listEvents(){
        try{
            $user = auth()->user();
            if (!$user->google_access_token || !$user->google_refresh_token) {
                session()->flash('error','Vincule a conta google antes de continuar');
                
                return redirect()->route('dashboard.show');
            }
            $client = new Google_Client();
            $client->setAuthConfig(storage_path('app/google-calendar/credentials.json'));
            $client->setAccessToken([
                'access_token' => $user->google_access_token,
                'refresh_token' => $user->google_refresh_token,
                'expires_in' => now()->diffInSeconds($user->google_token_expires),
            ]);

            if ($client->isAccessTokenExpired()) {
                $client->fetchAccessTokenWithRefreshToken($user->google_refresh_token);
                $newToken = $client->getAccessToken();
                $user->update([
                    'google_access_token' => $newToken['access_token'],
                    'google_token_expires' => now()->addSeconds($newToken['expires_in']),
                ]);
            }

            $service = new Google_Service_Calendar($client);
            $calendarId = 'primary';

            $events = $service->events->listEvents($calendarId, [
                'maxResults' => 10,
                'orderBy' => 'startTime',
                'singleEvents' => true,
                'timeMin' => date('c'),
            ]);

            return view('/Google/Calendar', ['events'=>$events]);
        }catch(\Exception $e){
            session()->flash('error', 'Erro ao vincular sua conta google, verifique com o responsável do sistema');
            \Log::error('Erro vincular sua conta google:', [
                'error' => $e->getMessage(),
            ]);
            return redirect()->route('dashboard.show');
        }
    }

    /**
     * Cria um novo evento no Google Calendar do usuário
     * 
     * Recebe os dados do evento via request e cria o evento na agenda principal
     * do Google Calendar do usuário. Utiliza o método auxiliar createEvent()
     * para realizar a criação efetiva.
     * 
     * @param \Illuminate\Http\Request $request Dados do evento contendo:
     *                                          - summary (string): Título do evento
     *                                          - start (string): Data/hora de início em formato ISO
     *                                          - end (string): Data/hora de fim em formato ISO
     * 
     * @return \Illuminate\Http\RedirectResponse Redirecionamento para calendar.index com
     *         mensagem de sucesso ou para dashboard.show com mensagem de erro
     * 
     * @throws \Exception Se houver erro na criação do evento
     */
    public function criarEvento(Request $request){
        $evento = $this->createEvent([
            'summary' => $request->summary,
            'start' => $request->start,
            'end' => $request->end,
        ]);

        if(!$evento){
            session()->flash('error','Erro ao criar o evento');
            return redirect()->route('dashboard.show');
        }
        session()->flash('mensagem','Evento criado com sucesso');
        
        return redirect()->route('calendar.index');
    }

    
    /**
     * Método auxiliar para criar eventos no Google Calendar
     * 
     * Configura o cliente Google, verifica e renova tokens de acesso se necessário,
     * e cria o evento na agenda principal do usuário. Este método é responsável
     * por toda a comunicação com a API do Google Calendar.
     * 
     * @param array $data Dados do evento contendo:
     *                    - summary (string): Título do evento
     *                    - start (string): Data/hora de início em formato ISO 8601
     *                    - end (string): Data/hora de fim em formato ISO 8601
     *                    - description (string, opcional): Descrição do evento
     *                    - location (string, opcional): Localização do evento
     *                    - attendees (array, opcional): Lista de participantes com email
     *                    - reminders (array, opcional): Configurações de lembretes
     * 
     * @return \Google_Service_Calendar_Event|false Retorna o objeto do evento criado
     *         ou false se houver erro na criação ou se o usuário não tiver tokens válidos
     * 
     * @throws \Google_Service_Exception Se houver erro na API do Google
     * @throws \Exception Se houver erro de configuração do cliente
     */
    public static function createEvent(array $data){
        try{
            $user = auth()->user();

            if (!$user->google_access_token || !$user->google_refresh_token) {
                session()->flash('error','Você precisa vincular sua conta google antes');
                    
                return redirect()->route('dashboard.show');
            }

            $client = new Google_Client();
            $client->setAuthConfig(storage_path('app/google-calendar/credentials.json'));
            $client->setAccessToken([
                'access_token' => $user->google_access_token,
                'refresh_token' => $user->google_refresh_token,
                'expires_in' => now()->diffInSeconds($user->google_token_expires),
            ]);

            if ($client->isAccessTokenExpired()) {
                $client->fetchAccessTokenWithRefreshToken($user->google_refresh_token);
                $newToken = $client->getAccessToken();
                $user->update([
                    'google_access_token' => $newToken['access_token'],
                    'google_token_expires' => now()->addSeconds($newToken['expires_in']),
                ]);
            }

            $service = new Google_Service_Calendar($client);

            $event = new Google_Service_Calendar_Event([
                'summary' => $data['summary'] ?? 'Sem título',
                'description' => $data['description'] ?? null,
                'location' => $data['location'] ?? null,
                'start' => [
                    'dateTime' => $data['start'], 
                    'timeZone' => 'America/Sao_Paulo',
                ],
                'end' => [
                    'dateTime' => $data['end'],
                    'timeZone' => 'America/Sao_Paulo',
                ],
                'attendees' => $data['attendees'] ?? [],
                'reminders' => $data['reminders'] ?? ['useDefault' => true],
            ]);

            $createdEvent = $service->events->insert('primary', $event);

            return $createdEvent; 
        }catch(\Exception $e){
            session()->flash('error', 'Erro ao registrar o evento, verifique com o responsável do sistema');
            \Log::error('Erro ao registrar sua conta google:', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

}
