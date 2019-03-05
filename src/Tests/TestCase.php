<?php

namespace Quicktools\Tests;

use Quicktools\Model;
use PHPUnit\Framework\Assert;
use Illuminate\Support\Collection as BaseCollection;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use \Tests\CreatesApplication;

    // Determine in a view has been loaded or if a view is missing
    // This will even drill down to view partials
    use Viewable;

    public function setUp()
    {
        parent::setUp();
    
        TestResponse::macro('assertCreated', function () {
            Assert::assertTrue(
                201 === $this->getStatusCode(),
                'Response status code ['.$this->getStatusCode().'] does not match expected 201 status code.'
            );
        
            return $this;
        });
        
        TestResponse::macro('data', function ($key = null) {
            if (!is_null($key)) {
                return $this->original->getData()[$key];
            }
            return is_null($this->original->getData()) ? null : collect($this->original->getData());

        });
    
        TestResponse::macro('assertViewContains', function ($key, $value) {
            $this->data($key)->assertContains($value);
            
            return $this;
        });
    
        TestResponse::macro('assertViewNotContains', function ($key, $value) {
            $this->data($key)->assertNotContains($value);
        
            return $this;
        });
    
    
        TestResponse::macro('assertViewData', function ($callback) {
            Assert::assertTrue($callback((object) $this->oiginal->getData()));
        
            return $this;
        });
    
        TestResponse::macro('assertPageComponentContains', function(...$options){
            if(count($options) === 1 ){
                collect($this->data('data'))->assertContains($options[0]);
            
                return $this;
            }elseif (count($options) === 2) {
            
                $this->data('data')[$options[0]]->assertEquals($options[1]);
            }
        });
    
        TestResponse::macro('assertPageComponentNotContains', function($key, $value){
            $this->data('data')[$key]->assertNotContains($value);
        
            return $this;
        });
    
    
        TestResponse::macro('assertViewData', function ($callback) {
            Assert::assertTrue($callback((object) $this->oiginal->getData()));
        
            return $this;
        });
    
        BaseCollection::macro('equals', function($items){
            if($items->count() !== $this->count())
                return false;
        
            $this->zip($items)->each(function ($itemPair) {
                if(gettype($itemPair[0]) !== gettype($itemPair[1])){
                    return false;
                }
                return (($itemPair[0] <=> $itemPair[1]) == 0);
            });
        
        });
    
        BaseCollection::macro('assertEquals', function ($items) {
            Assert::assertCount($items->count(), $this);
        
            $this->zip($items)->each(function ($itemPair) {
                if(gettype($itemPair[0]) !== gettype($itemPair[1])){
                    Assert::fail(
                        $itemPair[0] . ' is a '. gettype($itemPair[0]) . ' and ' .
                        $itemPair[1] . ' is a '. gettype($itemPair[1])
                    );
                }
                
                Assert::assertTrue(
                    ($itemPair[0] <=> $itemPair[1]) == 0 || //primitives
                    $itemPair[0]->is($itemPair[1])      //laravel models and objects that inherit "is"
            
                );
            });
        });
    
        BaseCollection::macro('assertContains', function ($item) {
            Assert::assertTrue($this->contains($item) || $this->equals($item));
        });
        
        
        BaseCollection::macro('assertDoesNotContain', function ($item) {
            Assert::assertFalse($this->contains($item));
        });


        Model::macro('tableHeaders', function(){
            return array_keys($this->attributes);
        });

        $this->withoutExceptionHandling();
    }

    protected function signIn($user = null)
    {
        throw new \Exception('Use method basicSignIn(...parameters)');
    }

    protected function errorsArray($errorBag = 'default')
    {
        ddf(session()->get('errors')->getBag($errorBag));
    }


    protected function logoutUser($guard = null)
    {
        return $this->post(route('logout'));
    }
}
