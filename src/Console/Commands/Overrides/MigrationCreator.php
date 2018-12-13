<?php

namespace Quicktools\Console\Commands\Overrides;


class MigrationCreator extends \Illuminate\Database\Migrations\MigrationCreator
{

    public function stubPath()
    {
        return $this->removeLastDirectory(__DIR__).'/stubs/migration';
    }

    protected function removeLastDirectory($path, $delimiter = '/')
    {
        $pathArray = explode($delimiter,$path);
        array_pop($pathArray);
        return implode($delimiter, $pathArray);
    }

}