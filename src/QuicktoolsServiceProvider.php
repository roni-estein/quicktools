<?php

namespace Quicktools;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Console\PresetCommand;

class QuicktoolsServiceProvider extends ServiceProvider {

	public function boot(){

        if ($this->app->runningInConsole()) {
            $this->commands([
                \Quicktools\Console\Commands\MakeDatabaseSeeder::class,
                \Quicktools\Console\Commands\MakeMigrationAndOpen::class,
                \Quicktools\Console\Commands\MakeModelWithFactoryAndMigration::class,
                \Quicktools\Console\Commands\MakeTestSeeder::class,
                \Quicktools\Console\Commands\MakeViewCommand::class,
                \Quicktools\Console\Commands\ModelMakeCommand::class,
                \Quicktools\Console\Commands\TestMakeCommand::class,
            ]);

        }

        PresetCommand::macro('quicktools', function($command){
            Preset::install($command);
        });
        
        Builder::macro('first', function($columns=['*']){
            return $this->get($columns)->limit(1)->take(1)->firstOrFail();
        });

        // databases
        // $this->publishes([
        //     __DIR__.'/../database/migrations/' => database_path('migrations')
        // ], 'migrations');

        // configurations
        // $this->publishes([
        //     __DIR__.'/../config/package.php' => config_path('Quicktools.php')
        // ], 'config');

        // Routes
		// $this->loadRoutesFrom(__DIR__.'/routes/web.php');
	}

	/**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        

    }

	
}