<?php

namespace Quicktools\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Foundation\Console\TestMakeCommand as ParentCommand;
use Illuminate\Support\Str;

class TestMakeCommand extends ParentCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'quicktools:make-test {name : The name of the class}
                    {--unit : Create a unit test}
                    {--presentation : Create a presentation test on a view or get request}
                    {--storage: (default) Create a test on a put patch or post request}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new test class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Test';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        if ($this->option('unit')) {
            return __DIR__.'/stubs/unit-test.stub';
        }

        if ($this->option('presentation')) {
            return __DIR__.'/stubs/presentation-test.stub';
        }


        return __DIR__.'/stubs/storage-test.stub';
    }
    
    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());
    
        return $this->replaceNamespace($stub, $name)
            ->replaceDummyVariable($stub, $name)
            ->replaceExampleVariable($stub, $name)
            ->replaceClass($stub, $name);
    }
    
    protected function replaceDummyVariable(&$stub, $name)
    {
        $class = Str::snake(str_replace($this->getNamespace($name).'\\', '', $name));
        $class = str_replace('_test','',$class);
        $stub =  str_replace('dummy', $class, $stub);
        
        return $this;
    }
    
    protected function replaceExampleVariable(&$stub, $name)
    {
        $class = collect(explode('\\',$this->getNamespace($name)))->last();
        $snake_class = Str::snake($class);
        $stub =  str_replace('Example', $class, $stub);
        $stub =  str_replace('example', $snake_class, $stub);
        
        return $this;
    }
}
