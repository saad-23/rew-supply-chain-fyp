<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DevServerCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'dev:serve';

    /**
     * The console command description.
     */
    protected $description = 'Start all development services (Laravel + ML Service)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('╔════════════════════════════════════════════════╗');
        $this->info('║  Starting REW Development Environment         ║');
        $this->info('╚════════════════════════════════════════════════╝');
        $this->newLine();

        // Start ML Service first
        $this->info('→ Starting ML Service...');
        $this->call('ml:service', ['action' => 'start']);
        
        $this->newLine();
        $this->info('→ Starting Laravel Server...');
        $this->newLine();

        $this->info('╔════════════════════════════════════════════════╗');
        $this->info('║  All Services Running                          ║');
        $this->info('╠════════════════════════════════════════════════╣');
        $this->info('║  • Laravel:    http://localhost:8000          ║');
        $this->info('║  • ML Service: http://localhost:5000          ║');
        $this->info('╠════════════════════════════════════════════════╣');
        $this->info('║  Press Ctrl+C to stop all services            ║');
        $this->info('╚════════════════════════════════════════════════╝');
        $this->newLine();

        // Start Laravel server (this will block)
        $this->call('serve');
    }
}
