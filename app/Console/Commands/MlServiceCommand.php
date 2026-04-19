<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class MlServiceCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'ml:service {action=start : start, stop, status, or restart}';

    /**
     * The console command description.
     */
    protected $description = 'Manage ML Service (Python Flask)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');

        switch ($action) {
            case 'start':
                $this->startService();
                break;
            case 'stop':
                $this->stopService();
                break;
            case 'status':
                $this->checkStatus();
                break;
            case 'restart':
                $this->stopService();
                sleep(2);
                $this->startService();
                break;
            default:
                $this->error("Unknown action: {$action}");
                $this->info("Available actions: start, stop, status, restart");
        }
    }

    protected function startService()
    {
        // Check if already running
        if ($this->isRunning()) {
            $this->warn('ML Service is already running on port 5000');
            return;
        }

        $this->info('Starting ML Service...');

        $mlPath = base_path('ml-service');
        $pythonPath = base_path('.venv/Scripts/python.exe');

        if (!file_exists($pythonPath)) {
            $pythonPath = 'python'; // Fallback to system python
        }

        // Start in background (Windows)
        if (PHP_OS_FAMILY === 'Windows') {
            $command = "start /B powershell -Command \"cd '{$mlPath}'; {$pythonPath} app.py > ml-service.log 2>&1\"";
            pclose(popen($command, 'r'));
        } else {
            // Linux/Mac
            $command = "cd {$mlPath} && nohup {$pythonPath} app.py > ml-service.log 2>&1 &";
            exec($command);
        }

        sleep(3); // Wait for startup

        if ($this->isRunning()) {
            $this->info('✓ ML Service started successfully on http://localhost:5000');
            $this->info('  Check logs at: ml-service/ml-service.log');
        } else {
            $this->error('✗ Failed to start ML Service');
            $this->info('  Try running manually: python ml-service/app.py');
        }
    }

    protected function stopService()
    {
        $this->info('Stopping ML Service...');

        if (PHP_OS_FAMILY === 'Windows') {
            // Find and kill Python process on port 5000
            exec('netstat -ano | findstr :5000', $output);
            
            if (!empty($output)) {
                foreach ($output as $line) {
                    if (strpos($line, 'LISTENING') !== false) {
                        preg_match('/\s+(\d+)\s*$/', $line, $matches);
                        if (isset($matches[1])) {
                            $pid = $matches[1];
                            exec("taskkill /F /PID {$pid} 2>&1", $killOutput);
                            $this->info("✓ Stopped process (PID: {$pid})");
                        }
                    }
                }
            } else {
                $this->warn('ML Service is not running');
            }
        } else {
            // Linux/Mac
            exec("lsof -ti:5000 | xargs kill -9 2>&1");
            $this->info('✓ ML Service stopped');
        }
    }

    protected function checkStatus()
    {
        $this->info('Checking ML Service status...');
        $this->newLine();

        $running = $this->isRunning();

        if ($running) {
            $this->info('✓ ML Service is RUNNING');
            $this->info('  URL: http://localhost:5000');
            $this->info('  Health: http://localhost:5000/api/health');
            
            // Try to get version info
            try {
                $response = @file_get_contents('http://localhost:5000/api/health', false, stream_context_create([
                    'http' => ['timeout' => 2]
                ]));
                
                if ($response) {
                    $data = json_decode($response, true);
                    $this->info('  Status: ' . ($data['status'] ?? 'unknown'));
                }
            } catch (\Exception $e) {
                // Silent fail
            }
        } else {
            $this->warn('✗ ML Service is NOT RUNNING');
            $this->info('  Start with: php artisan ml:service start');
        }

        $this->newLine();
    }

    protected function isRunning(): bool
    {
        try {
            $context = stream_context_create([
                'http' => [
                    'timeout' => 2,
                    'ignore_errors' => true
                ]
            ]);
            
            $response = @file_get_contents('http://localhost:5000/api/health', false, $context);
            return $response !== false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
