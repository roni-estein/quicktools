<?php

namespace Quicktools\Console\Commands;

use Illuminate\Foundation\Console\TestMakeCommand as ParentCommand;

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

}
