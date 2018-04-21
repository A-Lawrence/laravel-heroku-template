<?php

namespace App\Console\Deployment;

use Illuminate\Console\Command;

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

    public function clearResponseCache()
    {
        if(!class_exists("\Spatie\ResponseCache\ResponseCacheServiceProvider")){
            return false;
        }

        $this->call("responsecache:clear");
    }
}