<?php

namespace Quicktools\Console\Commands;

use Illuminate\Database\Console\Seeds\SeederMakeCommand;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;

class MakeDatabaseScene extends SeederMakeCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quicktools:make-db-scene {name : The name of the scene}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new database scene';



    /**
     * Create a new command instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @param  \Illuminate\Support\Composer  $composer
     * @return void
     */
    public function __construct(Filesystem $files, Composer $composer)
    {
        parent::__construct($files, $composer);
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        parent::handle();

        exec('pstorm database/seeds/scenes/'.$this->input->getArgument('name').'.php');
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/db-scene.stub';
    }

    protected function getPath($name)
    {
        return $this->laravel->basePath().'/database/seeds/scenes/'.$name.'.php';
    }

    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());

        return
            $this->replaceNamespace($stub, $name)
            ->replaceClass($stub, $name)
        ;

    }

    protected function replaceNamespace(&$stub,$name)
    {
        $namespace = 'database\seeds\scenes';

        $details = pathinfo($name);


        if($details['dirname'] !== "."){

            $namespace .= '\\'.str_replace('/','\\',$details['dirname']);
        }

        $stub = str_replace('DummyNamespace', $namespace, $stub);

        return $this;
    }

    public function replaceClass($stub, $name)
    {
        $details = pathinfo($name);
        $stub = str_replace('DummyModel', $details['filename'], $stub);

        return $stub;
    }
}
