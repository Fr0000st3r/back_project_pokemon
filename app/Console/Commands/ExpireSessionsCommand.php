<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Sanctum\PersonalAccessToken;



class ExpireSessionsCommand extends Command
{
    protected $signature = 'sessions:expire';
    protected $description = 'Elimina tokens de Sanctum que hayan expirado';

    public function handle()
    {
        $deleted = PersonalAccessToken::where('expires_at', '<', now())->delete();

        $this->info("Tokens expirados eliminados: {$deleted}");
    }
}
