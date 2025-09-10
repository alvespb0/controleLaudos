<?php

namespace App\Jobs;

use App\Models\Laudo;
use App\Notifications\ValidadeContratoNotification;
use Illuminate\Support\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotificarVendedorValidadeLaudo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $hoje = Carbon::today();

        $laudos = Laudo::whereDate('data_fim_contrato', $hoje->copy()->addDays(30))
               ->orWhereDate('data_fim_contrato', $hoje->copy()->addDays(15))
               ->orWhereDate('data_fim_contrato', $hoje->copy()->addDays(7))
               ->with(['comercial.user'])
               ->get();
               
        foreach($laudos as $laudo){
            if($laudo->comercial && $laudo->comercial->user){
                $laudo->comercial->user->notify(new ValidadeContratoNotification($laudo));
            }
        }
    }
}
