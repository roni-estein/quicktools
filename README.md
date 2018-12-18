## Introduction

Quicktools's only job is to help you rapidly write and deploy tests to your [Laravel Application](https://laravel.com) while using PHPStorm. It's main purpose is to makw it easier start up a project without worrying about too many advanced configurations or Testing Add-ons. Also It adds some speed conveniences buy opening up each file you generate so you don't have to manually sync folders.

//TODO:ADD AN IMAGE

## Installation

Wink runs on any Laravel application, it uses a separate database connection and authentication system so that you don't have to modify any of your project code.

To install Wink, require it via Composer:

```
composer require roniestein/quicktools
```

Once Composer is done, run the following command NOTE: THIS IS A DESTRUCTIVE COMMAND!!:

```
php artisan preset quicktools
```

This will,
 - add a .gitignore
 - clean your package.json and add a number of useful packages
 - replace your composer.json with some handy packages
 - setup phpunit.xml for rapid testing, and also update your tests to use the quictools classes
 - add a new example unit test
 - add a series of common rapid development commands
 - initialize tailwind
 - remove old public css and js
 - add new app.js, bootstrap.js and app.css
 - use postcss-preset-env app.css example
 - run composer install
 - run composer update
 - run yarn install
 - run yarn run development to test your system is configured
 - add new blade files with tailwind
 - scaffold authentication
 - open the site

## List of files overwritten or added

 - package.json 
 - composer.json 
 - composer.lock 
 - webpack.mix.js 
 - resources/sass/** 
 - resources/css/tailwind-config.js 
 - resources/css/app.css
 - resources/js/app.js
 - resources/views/welcome.blade.php
 - resources/views/home.blade.php
 - resources/views/auth/login.blade.php
 - resources/views/auth/register.blade.php
 - resources/views/auth/verify.blade.php
 - resources/views/auth/passwords/email.blade.php
 - resources/views/auth/passwords/reset.blade.php
 - resources/views/auth/passwords/reset.blade.php
 - resources/views/layouts/partials/_nav.blade.php
 - resources/views/layouts/app.blade.php
 - resources/views/layouts/app-with-nav.blade.php
 - resources/views/layouts/app-with-nav-single-screen.blade.php
 - resources/views/layouts/app-without-nav.blade.php
 - tests/TestCase.php 
 - tests/DomainTestCase.php 
 - tests/Assistant/ObjectFactory.php 
 - app/Http/Controllers/Controller.php

  
 
## PHPStorm Shell Commands for MacOS Mojave

On MacOS, Add the following to your aliases file, to give you a quick leg up on running commands

```
#HELP 
alias list='listFunction(){

	c
	echo "Laravel Helpers List"
	echo "--------------------"
	echo "NOTE: use directory/class for namespaced files"
	echo ""
	echo "TESTING"
	echo "ft  =>  Feature Test"
	echo "ut  =>  Unit Test"
	echo ""
	echo "SEEDING"
	echo "ts  =>  Test Seeder (not the same as a regular db seeder)"
	echo "rs  =>  Regular App Seeder"
	echo ""
	echo "TDD"
	echo "fmm =>  Make a (namespaced: optional) Model with a migration and a factory"
	echo "pc  =>  Plain Controller"
	echo "rc  =>  Resourcefull Controller"
	echo "pm  =>  Plain Model (Model with migration comming for now mn)"
	echo "fr  =>  Form Request"
	echo "mf  =>  Model Factory"
	echo ""
	echo "REGULAR DEVELOPMENT"
	echo "nc  =>  New Console Command"
	echo "pe  =>  Event"
	echo "pl  =>  Listener"
	echo "px  =>  Exception"	
	echo "mw  =>  Middleware"
	echo "sp  =>  Service Provider"
	echo "pr  =>  Rule"
	echo ""
	echo "MIGRATIONS"
	echo "mn  =>  New Migration, jsut the name of the table create_<posts>_table"
	echo "mp  =>  Plain Migration, mp add_something_to_posts posts"
	echo ""
	echo "OBSCURE DEVELOPMENT"
	echo "nv  =>  New View File {path/to/name or path.name or name.blade.php}"
	echo "nn  =>  New Notification"
	echo "nm  =>  New Mailable"
	# echo "nmm =>  New Markdown Mailable"
	echo ""
	echo "VUE"
	echo "--------------------"
	echo "vc  =>  Vue Component In components Directory (make into proper stub later)"
	echo ""

unset -f listFunction	
}; listFunction'

#VUE

#LARAVEL-VUE
alias vc='{ f=$(cat -); touch resources/assets/js/components/${f}.vue; pstorm resources/assets/js/components/${f}.vue;}<<<'

#LARAVEL


alias ft='{ f=$(cat -); php artisan quicktools:make-db-seeder ${f}Test; pstorm tests/Feature/${f}Test.php;}<<<'
alias ut='{ f=$(cat -); php artisan quicktools:make-db-seeder ${f}Test --unit; pstorm tests/Unit/${f}Test.php;}<<<'
alias pc='{ f=$(cat -); php artisan make:controller ${f}Controller; pstorm app/Http/Controllers/${f}Controller.php;}<<<'
alias rc='{ f=$(cat -); php artisan make:controller ${f}Controller -r; app/Http/Controllers/${f}Controller.php;}<<<'
alias pm='{ f=$(cat -); php artisan quicktools:make-model ${f};}<<<'
alias pe='{ f=$(cat -); php artisan make:event ${f}; pstorm app/Events/${f}.php;}<<<'
alias pl='{ f=$(cat -); php artisan make:listener ${f}; pstorm app/Listeners/${f}.php;}<<<'
alias nc='{ f=$(cat -); php artisan make:command ${f}; pstorm app/Console/Commands/${f}.php;}<<<'
alias px='{ f=$(cat -); php artisan make:exception ${f}; pstorm app/Exceptions/${f}.php;}<<<'
alias pr='{ f=$(cat -); php artisan make:request ${f}Request; pstorm app/Http/Requests/${f}Request.php;}<<<'
alias fr='{ f=$(cat -); php artisan make:request Forms/${f}Form; pstorm app/Http/Requests/Forms/${f}Form.php;}<<<'
alias mw='{ f=$(cat -); php artisan make:middleware ${f}; pstorm app/Http/Middleware/${f}.php;}<<<'
alias sp='{ f=$(cat -); php artisan make:provider ${f}Provider; pstorm app/Providers/${f}Provider.php;}<<<'
alias mf='{ f=$(cat -); php artisan make:factory ${f}Factory; pstorm database/factories/${f}Factory.php;}<<<'
alias pr='{ f=$(cat -); php artisan make:rule ${f}; pstorm app/Rules/${f}.php;}<<<'
alias nn='{ f=$(cat -); php artisan make:notification ${f}; pstorm app/Notifications/${f}.php;}<<<'
alias nm='{ f=$(cat -); php artisan make:mail ${f}; pstorm app/Mail/${f}.php;}<<<'
# alias nmm='{ f=$(cat -); php artisan make:mail ${f}; pstorm app/Mail/${f}.php;}<<<'
alias rs='{ f=$(cat -); php artisan quicktools:make-db-seeder ${f};}<<<'
alias nv='{ f=$(cat -); php artisan quicktools:make-view ${f};}<<<'
alias ts='{ f=$(cat -); php artisan quicktools:make-test-seeder  ${f};}<<<'
alias fmm='{ f=$(cat -); php artisan quicktools:make-test-resource ${f};}<<<'

alias mn='function __migrate-new-make(){
	tab=$(php artisan make:migration create_$*_table --create=$* --table=$*); 
	pstorm database/migrations/${tab:19}.php; 
	unset -f __migrate-new-make; 
}; __migrate-new-make'

alias mp='function __migrate-make(){
	tab=$(php artisan make:migration $1 --table=$2); 
	pstorm database/migrations/${tab:19}.php; 
	unset -f __migrate-make; 
}; __migrate-make'
```

## Updates to Routing

After Quicktools\Setup is published, your App\Http\Controllers\Controller.php will have additional functionality that will allow you to click through on your routes files.

You can configure your routes in any way you want:

```php
Route::get('/', 'FooController@index');
// Can also be 

use App\Http\Controllers\FooController;

Route::get('/', FooController::at('index'));



Route::resource('post','PostController');

// Can also be 

use App\Http\Controllers\PostController;

Route::resource('post', FooController::res());


```

## Road map

Quicktools will continue to be developed for the macos, valet, local testing environment. Feel free to fork it or send me questions or issues. I decided to ship it in this early stage so you can help me make it better, however I'm already using it to run multiple websites including my personal blog.

Here's the plan for what's coming:

- [ ] Add tests
- [ ] Add some congiration for other OS or editor platforms
- [ ] Add nice aliases that people can opt into
- [ ] Make Better Docs
## Contributing

Check our [contribution guide](CONTRIBUTING.md).

## License

Quicktools is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
