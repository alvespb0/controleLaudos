<?php

namespace App\Jobs;

use App\Models\Lead;
use App\Notifications\ProximoContatoNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class NotificarVendedoresProximoContato implements ShouldQueue
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
        $amanha = Carbon::tomorrow()->toDateString();

        $leads = Lead::whereDate('proximo_contato', $amanha)
                     ->with(['vendedor.user', 'cliente']) 
                     ->get();

        foreach ($leads as $lead) {
            if ($lead->vendedor && $lead->vendedor->user) {
                $lead->vendedor->user->notify(new ProximoContatoNotification($lead));
            }
        }
    }
}
