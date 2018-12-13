<?php

namespace Quicktools\Console\Commands;

use Quicktools\Console\Commands\Overrides\MigrationCreator;
use Illuminate\Database\Console\Migrations\MigrateMakeCommand;
use Illuminate\Support\Composer;


class MakeMigrationAndOpen extends MigrateMakeCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quicktools:make-migration {name : The name of the migration}
        {--create= : The table to be created}
        {--table= : The table to migrate}
        {--path= : The location where the migration file should be created}
        {--realpath : Indicate any provided migration file paths are pre-resolved absolute paths}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new migration file and open it';

    public function __construct(MigrationCreator $creator, Composer $composer)
    {
        parent::__construct($creator, $composer);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        parent::handle();
    }


    protected function writeMigration($name, $table, $create)
    {
        $file = pathinfo($this->creator->create(
            $name, $this->getMigrationPath(), $table, $create
        ));


        $this->line("<info>Created Migration:</info> {$file['filename']}");

        exec("pstorm {$file['dirname']}/{$file['basename']}");

    }
}
