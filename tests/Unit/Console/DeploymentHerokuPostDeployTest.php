<?php

namespace Tests\Unit\Console;

use Illuminate\Foundation\Application;
use Mockery as m;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Console\Deployment\HerokuPostDeploy;

class DeploymentHerokuPostDeployTest extends TestCase
{
    use RefreshDatabase;

    public function tearDown()
    {
        m::close();
    }

    /**
     * @test
     *
     * @group Console
     */
    public function it_runs_migrate_fresh_with_seed_data_when_in_development_environment()
    {
        $app = new ApplicationModifiedEnvironmentStub();
        $app->setCustomEnv("development");

        $command = m::mock("\Volanti\Console\Deployment\HerokuPostDeploy[call,clearResponseCache]")->shouldIgnoreMissing();

        $command->setLaravel($app);

        $command->shouldReceive("call")->with("migrate:fresh", ["-seed", true]);

        $this->runCommand($command);
    }

    /**
     * @test
     *
     * @group Console
     */
    public function it_runs_migrate_with_step_option_when_in_staging_environment()
    {
        $app = new ApplicationModifiedEnvironmentStub();
        $app->setCustomEnv("staging");

        $command = m::mock("\Volanti\Console\Deployment\HerokuPostDeploy[call,clearResponseCache]")->shouldIgnoreMissing();

        $command->setLaravel($app);

        $command->shouldReceive("call")->with("migrate", ['--step' => true,]);

        $this->runCommand($command);
    }

    /**
     * @test
     *
     * @group Console
     */
    public function it_runs_migrate_with_force_option_when_in_production_environment()
    {
        $app = new ApplicationModifiedEnvironmentStub();
        $app->setCustomEnv("production");

        $command = m::mock("\Volanti\Console\Deployment\HerokuPostDeploy[call,clearResponseCache]")->shouldIgnoreMissing();

        $command->setLaravel($app);

        $command->shouldReceive("call")->with("migrate", ['--force' => true, '--step' => true,]);

        $this->runCommand($command);
    }

    /**
     * @test
     *
     * @group Console
     */
    public function it_clears_the_response_cache_when_called()
    {
        $command = m::mock("\Volanti\Console\Deployment\HerokuPostDeploy[call]");

        $command->setLaravel(new ApplicationModifiedEnvironmentStub());

        $command->shouldReceive("call")->with("responsecache:clear");

        $this->runCommand($command);
    }

    /**
     * Ref: https://github.com/laravel/framework/blob/5.6/tests/Session/SessionTableCommandTest.php
     */
    protected function runCommand($command, $input = [])
    {
        return $command->run(new \Symfony\Component\Console\Input\ArrayInput($input), new \Symfony\Component\Console\Output\NullOutput);
    }
}

class ApplicationModifiedEnvironmentStub extends Application
{
    public $custom_environment = null;

    public function setCustomEnv($env){
        $this->custom_environment = $env;
    }

    public function environment()
    {
        return $this->custom_environment;
    }
}