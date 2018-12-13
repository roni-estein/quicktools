<?php

namespace Quicktools\Console\Commands;

use Illuminate\Database\Console\Seeds\SeederMakeCommand;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;

class MakeTestSeeder extends SeederMakeCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quicktools:make-test-seeder {name : The name of the seeder}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new seeder in the tests namespace for use in Unit Testing Only';



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

        exec('pstorm tests/Utilities/Seeders/'.$this->input->getArgument('name').'Seeder.php');
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/test-seeder.stub';
    }

    protected function getPath($name)
    {
        return $this->laravel->basePath().'/tests/Utilities/Seeders/'.$name.'Seeder.php';
    }

    protected function buildClass($name)
    {
        $name = $name.'Seeder';
        $stub = $this->files->get($this->getStub());

        return
            $this->replaceNamespace($stub, $name)
            ->replaceClass($stub, $name)
        ;

    }

    protected function replaceNamespace(&$stub,$name)
    {
        $namespace = 'Tests\Utilities\Seeders';

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
