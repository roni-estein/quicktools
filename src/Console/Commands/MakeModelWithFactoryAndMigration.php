<?php

namespace Quicktools\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;

class MakeModelWithFactoryAndMigration extends GeneratorCommand
{
    /**
     *
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quicktools:make-test-resource 
                        {name : The model name including the namespace like Product/SpecialProduct}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make a PHPUnit Test Resource, a Model, wth a Factory a Migration and a unit test';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    protected function getStub()
    {
        return __DIR__.'/stubs/model.stub';
    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $path = $this->argument('name');

        //make the model
        parent::handle();
        //open the model file in phpstorm
        exec("pstorm app/".$path.".php");
        $this->createNamespaceFactory($path);

        $this->createMigration();

        exec('newMigration=$(ls -dt database/migrations/* | head -1);pstorm $newMigration');


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
        $stub = str_replace(
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

    protected function createMigration()
    {

        $table = $this->getTableName();

        $this->call('quicktools:make-migration', [
            'name' => "create_{$table}_table",
            '--create' => $table,
        ]);

    }

    protected function createDirectories($path)
    {

        if (! is_dir($directory = ($path))) {
            mkdir($directory, 0755, true);
        }
    }

    protected function createNamespaceFactory($path)
    {

        $details = pathinfo($path);

        $this->createDirectories('database/factories/'.$details['dirname']);



        if (file_exists($file = ('database/factories/'.$path.'Factory.php')) && ! $this->option('force')) {
            if (! $this->confirm("The [database/factories/".$path.".php] factory already exists. Do you want to replace it?")) {
                $this->error('database/factories/'.$path.'.php not created!');
                die();
            }else{
                exec('rm database/factories/'.$path.'Factory.php');
//                unlink($view);
            }

        }

        touch($file);
        file_put_contents($file,str_replace('DummyNamespace','App\\'.str_replace('/','\\',$this->getNewNamespace($details)),str_replace('DummyModel',$details['filename'],file_get_contents(__DIR__.'/stubs/factory.stub'))));
        exec("pstorm {$file}");
    }

    protected function getNewNamespace($details)
    {
        if ( $details['dirname'] == ".") return $details['filename'];

        return $details['dirname'].'\\'.$details['filename'];
    }

}
