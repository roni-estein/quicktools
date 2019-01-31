<?php

namespace Quicktools\Console\Commands;

use Illuminate\Foundation\Console\RequestMakeCommand;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;

class MakeFormRequestCommand extends RequestMakeCommand
{
   /**
    * The name and signature of the console command.
    *
    * @var string
    */
   protected $signature = 'quicktools:make-form-request {name : The name of the form request}';

   /**
    * The console command description.
    *
    * @var string
    */
   protected $description = 'Create a new request';


   /**
    * Create a new command instance.
    *
    * @param  \Illuminate\Filesystem\Filesystem $files
    * @param  \Illuminate\Support\Composer $composer
    * @return void
    */
   public function __construct(Filesystem $files, Composer $composer)
   {
      parent::__construct($files, $composer);
   }

   /**
    * Get the destination class path.
    *
    * @param  string $name
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

      exec('pstorm app/Http/Requests/Forms/' . $this->input->getArgument('name') . 'Form.php');
   }

   /**
    * Get the stub file for the generator.
    *
    * @return string
    */
   protected function getStub()
   {
      return __DIR__ . '/stubs/form-request.stub';
   }

   protected function getNameInput()
   {
      return trim('Forms/' . $this->argument('name') . 'Form');
   }

   protected function buildClass($name)
   {
      $name = $name . 'Form';
      $stub = $this->files->get($this->getStub());

      return
         $this->replaceNamespace($stub, $name)
            ->replaceClass($stub, $name);

   }

   protected function replaceNamespace(&$stub, $name)
   {
      $namespace = 'App\Http\Requests\Forms';

      $details = pathinfo($name);


      if ($details['dirname'] !== ".") {

         $namespace .= '\\' . str_replace('/', '\\', $details['dirname']);
      }

      $stub = str_replace('DummyNamespace', $namespace, $stub);

      return $this;
   }

   public function replaceClass($stub, $name)
   {
      $name = explode('/', $this->argument('name'));
      $updateName = end($name) . 'Form';

      $stub = str_replace('DummyClass', $updateName, $stub);

      return $stub;
   }
}
