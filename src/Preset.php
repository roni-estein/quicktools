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

      if(optional($command->options()['option'])[0] == 'force'){
         static::buildAuthentication();
      }else{
         static::scaffoldAuthentication();
      }


      static::addGitIgnore();
      static::addGitKeepToStorageFiles();
      static::addResourceAssets();
      static::addBaseHelpers();
      static::addComposerJson();
      static::addTestHelpers();
      static::addTestSetup();
      static::updatePackages($devDependancies = true);
      static::updateLaravelMix();
      static::updateBaseController();


      static::cleanSassDirectory();
      static::removePublicFiles();
      static::runYarnInstall();
      static::initializeTailwind();


      static::runComposerUpdate();
      static::createFirstCommit();
      static::runYarnDev();

      static::runValetLink();
      static::openSite();


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
      static::$command->info($i++.'. /package.json');
      static::$command->info($i++.'. /composer.json');
      static::$command->info($i++.'. /composer.lock');
      static::$command->info($i++.'. /webpack.mix.js');
      static::$command->info($i++.'. /resources/sass/**');
      static::$command->info($i++.'. /resources/css/tailwind-config.js');
      static::$command->info($i++.'. /resources/css/app.css');
      static::$command->info($i++.'. /resources/js/app.js');
      static::$command->info($i++.'. /resources/views/welcome.blade.php');
      static::$command->info($i++.'. /resources/views/home.blade.php');
      static::$command->info($i++.'. /resources/views/auth/login.blade.php');
      static::$command->info($i++.'. /resources/views/auth/register.blade.php');
      static::$command->info($i++.'. /resources/views/auth/verify.blade.php');
      static::$command->info($i++.'. /resources/views/auth/passwords/email.blade.php');
      static::$command->info($i++.'. /resources/views/auth/passwords/reset.blade.php');
      static::$command->info($i++.'. /resources/views/auth/passwords/reset.blade.php');
      static::$command->info($i++.'. /resources/views/layouts/partials/_nav.blade.php');
      static::$command->info($i++.'. /resources/views/layouts/app.blade.php');
      static::$command->info($i++.'. /resources/views/layouts/app-with-nav.blade.php');
      static::$command->info($i++.'. /resources/views/layouts/app-with-nav-single-screen.blade.php');
      static::$command->info($i++.'. /resources/views/layouts/app-without-nav.blade.php');
      static::$command->info($i++.'. /tests/TestCase.php');
      static::$command->info($i++.'. /tests/DomainTestCase.php');
      static::$command->info($i++.'. /tests/Assistant/ObjectFactory.php');
      static::$command->info($i++.'. /app/Http/Controllers/Controller.php');


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

   public static function addGitKeepToStorageDirectories()
   {
      static::$command->info('');
      static::$command->info('Add .gitkeep to storage directories ...');
      $keepFiles = [
         'app/public/.gitkeep',
         'framework/cache/data/.gitkeep',
         'framework/sessions/.gitkeep',
         'framework/testing/.gitkeep',
         'framework/views/.gitkeep',
      ];
      foreach($keepFiles as $file){
         File::put(storage_path($file, ''));
         exec('git add -f '. $file);
      }
      static::$command->info('Add the following files ...');
      static::$command->info($keepFiles);
   }


   public static function addResourceAssets()
   {
      if( ! File::isDirectory(resource_path('css'))){
         File::makeDirectory(resource_path('css'));
      }
      if( ! File::isDirectory(resource_path('css/forms'))){
         File::makeDirectory(resource_path('css/forms'));
      }
      copy( __DIR__.'/stubs/app.css.stub',resource_path('css/app.css'));
      copy( __DIR__.'/stubs/login.css.stub',resource_path('css/forms/login.css'));
      copy( __DIR__.'/stubs/app.js.stub',resource_path('js/app.js'));
      copy( __DIR__.'/stubs/bootstrap.js.stub',resource_path('js/bootstrap.js'));
   }

   public static function scaffoldAuthentication()
   {
      static::$command->info('Do you want to scaffold authentication now?');
      if(static::$command->choice('This will destroy existing authentication views', ['y'=>'yes', 'n'=>'no'],'y')) {
         static::buildAuthentication();
      }
   }

   public static function buildAuthentication(){
      static::$command->info('');
      static::$command->info('deleting old authentication files ...');
      // remove current authentication to stop hanging when calling auth routes a second time.
      // since all answers will be yes at this point
      File::delete(resource_path('views/auth/login.blade.php'));
      File::delete(resource_path('views/auth/register.blade.php'));
      File::delete(resource_path('views/auth/verify.blade.php'));
      File::delete(resource_path('views/auth/passwords/email.blade.php'));
      File::delete(resource_path('views/auth/passwords/reset.blade.php'));
      File::delete(resource_path('views/layouts/app.blade.php'));
      File::delete(resource_path('views/home.blade.php'));

      static::$command->info('Scaffolding new authentication ...');
      exec ('php artisan make:auth');
      static::prepFilesForAuth();
      static::$command->info('Scaffolding complete');
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
      //exec('./node_modules/.bin/tailwind init resources/css/tailwind-config.js');
      copy( __DIR__.'/stubs/tailwind-config.js.stub',resource_path('css/tailwind-config.js'));
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

   public static function prepFilesForAuth()
   {
      // add partials directory
      if( ! File::isDirectory(resource_path('views/layouts/partials'))){
         File::makeDirectory(resource_path('views/layouts/partials'));
      }

      // add nambar base partial to partials directory
      copy( __DIR__.'/stubs/_nav.blade.php.stub',resource_path('views/layouts/partials/_nav.blade.php'));

      // update layouts.app
      copy( __DIR__.'/stubs/app.blade.php.stub',resource_path('views/layouts/app.blade.php'));

      // add new layouts
      copy( __DIR__.'/stubs/app-with-nav.blade.php.stub',resource_path('views/layouts/app-with-nav.blade.php'));
      copy( __DIR__.'/stubs/app-without-nav.blade.php.stub',resource_path('views/layouts/app-without-nav.blade.php'));
      copy( __DIR__.'/stubs/app-with-nav-single-screen.blade.php.stub',resource_path('views/layouts/app-with-nav-single-screen.blade.php'));


      // add new welcome.blade.php
      copy( __DIR__.'/stubs/welcome.blade.php.stub',resource_path('views/welcome.blade.php'));

      // add new auth passwords email
      copy( __DIR__.'/stubs/auth-passwords-email.blade.php.stub',resource_path('views/auth/passwords/email.blade.php'));
      // add new auth passwords reset
      copy( __DIR__.'/stubs/auth-passwords-reset.blade.php.stub',resource_path('views/auth/passwords/reset.blade.php'));
      // add new auth login
      copy( __DIR__.'/stubs/auth-login.blade.php.stub',resource_path('views/auth/login.blade.php'));
      // add new auth register
      copy( __DIR__.'/stubs/auth-register.blade.php.stub',resource_path('views/auth/register.blade.php'));
      // add new auth verify
      copy( __DIR__.'/stubs/auth-verify.blade.php.stub',resource_path('views/auth/verify.blade.php'));

      // add new home.blade.php
      copy( __DIR__.'/stubs/home.blade.php.stub',resource_path('views/home.blade.php'));

   }

   public static function openSite(){
      static::$command->info('');
      $directory = static::directoryName();

      static::$command->info('Opening Site http://'.$directory.'.test...');

      exec('open http://'.$directory.'.test');
   }

   public static function createFirstCommit()
   {
      static::$command->info('');
      static::$command->info('--------------------------------');
      static::$command->info('Adding Readme.md');
      exec('echo "# '.strtoupper(static::directoryName()).'" >> README.md');
      static::$command->info('');
      static::$command->info('Initializing Git ...');
      exec('git init');
      exec('git add README.md');
      exec('git commit -m "first commit"');

      static::$command->info('');
      static::$command->info('Creating Private Github Repo roni-estein/'.static::directoryName());

      exec('hub create -p');
      exec('git remote add origin https://github.com/roni-estein/'.static::directoryName().'.git');
      exec('git push origin master');

      exec('git add .');
      exec('git commit -m "Installed Laravel with roniestein/quicktools preset"');
      exec('git push origin master');

   }


   protected static function directoryName()
   {
      return pathinfo(base_path())['basename'];
   }
}