<?php

namespace Quicktools\Console\Commands;

use Illuminate\Foundation\Console\ModelMakeCommand as ParentCommand;

class ModelMakeCommand extends ParentCommand
{
	/**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'quicktools:make-model';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Eloquent model class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Model';

	/**
     * Execute the console command.
     *
     * @return void
     */
	public function handle(){
		$path = $this->argument('name');

        //make the model
        parent::handle();
        //open the model file in phpstorm
        exec("pstorm app/".$path.".php");
	}

	/**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        if ($this->option('pivot')) {
            return __DIR__.'/stubs/pivot.model.stub';
        }

        return __DIR__.'/stubs/model.stub';
    }


    protected function getNewNamespace($details)
    {
        if ( $details['dirname'] == ".") return $details['filename'];

        return $details['dirname'].'\\'.$details['filename'];
    }

    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());

        return $this->replaceNamespace($stub, $name)
            ->replaceTableName($stub)
            ->replaceClass($stub, $name);
    }

    /**
     * Replace the namespace for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return $this
     */
    protected function replaceTableName(&$stub)
    {
        $stub = str_replace('DummyDirectory', 
            'dummy_table', $this->getTableName(), $stub
        );
        return $this;
    }

    protected function getTableName()
    {

        $path = pathinfo($this->argument('name'));
        //prepare the migration
        $preparedClass = snake_case(str_plural($path['filename']));

        if ( $path['dirname'] == ".") return $preparedClass;

        $preparedPath = str_replace('__','_',snake_case(str_replace('/','_',$path['dirname'])));
        return  $preparedPath.'_'.$preparedClass ;
    }
}