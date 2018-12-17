<?php

namespace Quicktools;

use Quicktools\BaseProject;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Foundation\Console\PresetCommand;
use Illuminate\Foundation\Console\Presets\Preset as LaravelPreset;

class Preset extends LaravelPreset{

    public static $command;
    public static function install(PresetCommand $command)
    {
        static::$command = $command;

        static::issueWarning();

        static::addGitIgnore();
        static::addResourceAssets();
        static::addBaseHelpers();
        static::addComposerJson();
        static::addTestHelpers();
        static::addTestSetup();
        static::updatePackages($devDependancies = true);
        static::updateLaravelMix();
        static::updateBaseController();

//        static::cleanSassDirectory();
        static::removePublicFiles();
        static::runYarnInstall();
        static::initializeTailwind();


        static::runComposerUpdate();
        static::runYarnDev();
        static::runValetLink();

    }

    ///////////////////////////////  PROMPTS ///////////////////////////////
    public static function issueWarning()
    {
        $i = 1;
        static::$command->info('');
        static::$command->info('******************************************************');
        static::$command->info('');
        static::$command->warn('This is a descructive call, and will overwrite some files in your application');
        static::$command->warn('and therefore should only be run when you are setting up a new application,');
        static::$command->warn('or if you have staged and cleaned your project and git repository and are');
        static::$command->warn('able to restore destroyed files at their current point. Use this when');
        static::$command->warn('you are trying to solve a configuration issue in one of these files');
        static::$command->info('');
        static::$command->info('******************************************************');
        static::$command->info('');
        static::$command->warn('Here is a list of files that will be overwritten:');
        static::$command->info('');
        static::$command->info($i++.'. /package.json'); // done
        static::$command->info($i++.'. /webpack.mix.js'); // done
        static::$command->info($i++.'. /resources/sass/**'); // done
        static::$command->info($i++.'. /resources/css/yailwind-config.js'); // done
        static::$command->info($i++.'. /tests/DomainTestCase.php'); // done
        static::$command->info($i++.'. /tests/Assistant/ObjectFactory.php'); // done
        static::$command->info($i++.'. /app/Http/Controllers/Controller.php'); //done
        static::$command->info($i++.'. /resources/css/app.css');
        static::$command->info($i++.'. /resources/js/app.js');


        static::$command->info('');
        static::$command->info('These Files will be deleted');
        static::$command->warn($i++.'. /tests/TestCase.php');
    }

    ///////////////////////////////  ADDITIVE COMMANDS ///////////////////////////////

    public static function addGitIgnore()
    {
        static::$command->info('');
        static::$command->info('Add .gitignore file ...');
        copy( __DIR__.'/stubs/.gitignore.stub',base_path('.gitignore'));
    }

    public static function addResourceAssets()
    {
        copy( __DIR__.'/stubs/app.css.stub',resource_path('css/app.css'));
        copy( __DIR__.'/stubs/app.js.stub',resource_path('js/app.js'));
        copy( __DIR__.'/stubs/bootstrap.js.stub',resource_path('js/bootstrap.js'));
    }

    public static function addBaseHelpers()
    {
        if( ! File::isDirectory(app_path('Helpers'))){
            File::makeDirectory(app_path('Helpers'));
        }
        copy( __DIR__.'/stubs/BaseHelpers.php.stub',app_path('Helpers/BaseHelpers.php'));
    }

    public static function addTestHelpers()
    {
        copy( __DIR__.'/stubs/test-helpers.php.stub',BaseProject::testPath('test-helpers.php'));

    }

    public static function addComposerJson()
    {
        copy( __DIR__.'/stubs/composer.json.stub',base_path('composer.json'));
    }

    public static function addTestSetup()
    {
        copy( __DIR__.'/stubs/phpunit.xml.stub',base_path('phpunit.xml'));
        copy( __DIR__.'/stubs/DomainTestCase.stub',BaseProject::testPath('DomainTestCase.php'));
        if( ! File::isDirectory(BaseProject::testPath('Assistant'))){
            File::makeDirectory(BaseProject::testPath('Assistant'));
        }
        copy( __DIR__.'/stubs/ObjectFactory.stub',BaseProject::testPath('Assistant/ObjectFactory.php'));
    }

    public static function updatePackageArray($packages)
    {
        return array_merge([
            'tailwindcss' => '>=0.7.2',
            'postcss-color-mod-function' => '^3.0.3',
            'postcss-custom-selectors' => '^5.1.2',
            'postcss-import' => '^12.0.1',
            'postcss-nesting' => '^7.0.0',
            'postcss-preset-env' => '^6.4.0',
            'laravel-mix ' =>  '^4.0.3',
            'laravel-mix-purgecss ' =>  '^3.0.0',
            'browser-sync '  =>  '^2.26.3',
            'browser-sync-webpack-plugin '  =>  '2.2.2',
        ],
            Arr::except($packages, [
                'popper.js',
                'jquery',
                'lodash',
                'bootstrap',
                'bootstrap-sass',
                'laravel-mix',

            ]));
    }

    public static function updateLaravelMix()
    {
        $directory = pathinfo(base_path())['basename'];
        $file = File::get(__DIR__.'/stubs/webpack.mix.js.stub');
        $file = str_replace('DummyDirectory', $directory,$file);
        File::put(base_path('webpack.mix.js'), $file);

    }

    public static function updateBaseController()
    {
        copy( __DIR__.'/stubs/BaseController.stub',app_path('/Http/Controllers/Controller.php'));
    }

    ///////////////////////////////  CLEANING COMMANDS ///////////////////////////////
    public static function cleanSassDirectory()
    {
        File::cleanDirectory(resource_path('sass'));
    }

    public static function removePublicFiles()
    {
        File::cleanDirectory(public_path('css'));
        File::cleanDirectory(public_path('js'));
    }

    ///////////////////////////////  INSTALL COMMANDS ///////////////////////////////


    public static function runYarnInstall()
    {
        static::$command->info('');
        static::$command->info('Running yarn install, this can take a minute ...');
        static::$command->info(exec('yarn install'));

    }


    public static function initializeTailwind()
    {
        static::$command->info('');
        static::$command->info('Initializing Tailwind ...');
        exec('./node_modules/.bin/tailwind init resources/css/tailwind-config.js');
    }





    public static function runComposerUpdate()
    {
        static::$command->info('');
        static::$command->info('Removing composer.lock ...');
        File::delete(base_path('composer.lock'));
        static::$command->info('');
        static::$command->info('Running composer install this may take a few minutes ...');
        exec('composer install');
        static::$command->info('');
        static::$command->info('Running composer update this may take a few minutes ...');
        exec('composer update');
    }


    public static function runYarnDev()
    {
        static::$command->info('');
        static::$command->info('Running command yarn run development to test for errors...');
        exec('yarn run development');
    }










    






    public static function runValetLink()
    {
        static::$command->info('');
        static::$command->info('Running valet link ...');
        exec('valet link');
    }
}