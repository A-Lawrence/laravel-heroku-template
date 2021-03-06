<?php

namespace App\Console\Deployment;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PDOException;

class HerokuPostDeploy extends Command
{
    protected $signature = 'postdeploy:heroku';
    protected $description = 'Run post-deploy on Heroku';

    public function handle()
    {
        $this->runMigrationsFor(app()->environment());
        $this->clearResponseCache();
    }

    public function runMigrationsFor($environment)
    {
        if (!$this->checkDatabaseConnection()) {
            return false;
        }

        switch ($environment) {
            case "production":
                $this->call('migrate', ['--force' => true]);
                break;
            case "staging":
                $this->call('migrate');
                break;
            case "development":
                $this->call('migrate:fresh');
                break;
        }
    }

    private function checkDatabaseConnection()
    {
        try {
            DB::connection()->getDatabaseName();
            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    public function clearResponseCache()
    {
        if (!class_exists("\Spatie\ResponseCache\ResponseCacheServiceProvider")) {
            return false;
        }

        $this->call("responsecache:clear");
    }
}