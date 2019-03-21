<?php

namespace Quicktools\Tests;

use mysql_xdevapi\Exception;
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
    
    public function setUp(): void
    {
        parent::setUp();
        
        //HELPER
        
        
        //COLLECTIONS
        
        BaseCollection::macro('assertHasNoneOf', function ($items) {
            if ($items == null) {
                throw new Exception('Trying to assert Collection does not contains a root object null');
            }
            if (is_scalar($items)) {
                return Assert::assertFalse($this->contains($items));
            } elseif (is_array($items)) {
                return $this->assertDoesNotContain(collect($items));
            } else {
                return $this->assertDoesNotContain($items);
            }
        });
        
        BaseCollection::macro('assertContains', function ($item, $orderedKey = null) {
            Assert::assertTrue($this->contains($item) || $this->equals($item, $orderedKey));
        });
        
        
        BaseCollection::macro('assertDoesNotContain', function ($items) {
            if (is_scalar($items)) {
                return Assert::assertFalse($this->contains($items));
            } else {
                foreach ($items as $item) {
                    Assert::assertFalse($this->contains($item));
                }
            }
        });
        
        BaseCollection::macro('equals', function ($items, $orderedKey = null) {
            if ($items->count() !== $this->count())
                return false;
            
            $self = $this;
            
            if (!is_null($orderedKey)) {
                $self = $this->sortBy($orderedKey);
                $items = $items->sortBy($orderedKey);
            }
            
            $self->zip($items)->each(function ($itemPair) {
                if (gettype($itemPair[0]) !== gettype($itemPair[1])) {
                    return false;
                }
                return (($itemPair[0] <=> $itemPair[1]) == 0);
            });
            
        });
        
        BaseCollection::macro('assertEquals', function ($items, $orderedKey = null) {
            if (is_array($items)) {
                $items = collect($items);
            }
            
            Assert::assertCount($items->count(), $this);
            
            $self = $this;
            
            if (!is_null($orderedKey)) {
                $self = $this->sortBy($orderedKey);
                $items = $items->sortBy($orderedKey);
            }
            
            $self->zip($items)->each(function ($itemPair) {
                if (gettype($itemPair[0]) !== gettype($itemPair[1])) {
                    Assert::fail(
                        $itemPair[0] . ' is a ' . gettype($itemPair[0]) . ' and ' .
                        $itemPair[1] . ' is a ' . gettype($itemPair[1])
                    );
                }
                
                Assert::assertTrue(
                    ($itemPair[0] <=> $itemPair[1]) == 0 || //primitives
                    $itemPair[0]->is($itemPair[1])   //laravel models and objects that inherit "is"
                    , $itemPair[0] . ' is not equal to ' . $itemPair[1]
                );
            });
        });
        
        
        // VUE PAGE COMPONENTS
        
        TestResponse::macro('assertPageComponentContains', function (...$options) {
            if (count($options) === 1) {
                $passes = collect($this->data('data'))->first(function ($item) use ($options) {
                    return ($item <=> $options[0]) == 0 || $item->is($options[0]);
                });
                
                Assert::assertNotNull($passes, $this->formatFailMessage($options[0], ':attribute not found in this component'));
                
                return $this;
            } elseif (count($options) === 2) {
                if(!isset ($this->data('data')[$options[0]])){
                    Assert::fail('No prop data : "'. $options[0]. '" foound for this component!' );
                }
                $expected = $this->data('data')[$options[0]];
                $given = $options[1];
                
                // There is no method_exists that works on a macro,
                // So make a try catch loop to check for assertions
                // an equality functions
                
                try {
                    $expected->assertEquals($given);
                } catch (\BadMethodCallException $e) {
                    try {
                        $expected->assertContains($given);
                    } catch (\BadMethodCallException $e) {
                        if (method_exists($expected, 'is')) {
                            Assert::assertTrue($expected->is($given));
                        } else {
                            Assert::assertTrue($expected <=> $given);
                        }
                    }
                }
            }
            
            return $this;
        });
        
        
        TestResponse::macro('assertPageComponentDoesNotContain', function (...$options) {
            if (count($options) === 1) {
                $passes = collect($this->data('data'))->first(function ($item) use ($options) {
                    return ($item <=> $options[0]) == 0 || $item->is($options[0]);
                });
                
                Assert::assertNull($passes, $this->formatFailMessage($options[0], ':attribute is found in this component'));
                
            } elseif (count($options) === 2) {
                $expected = $this->data('data')[$options[0]];
                $given = $options[1];
                
                // There is no method_exists that works on a macro,
                // So make a try catch loop to check for assertions
                // an equality functions
                
                try {
                    $expected->assertHasNoneOf($given);
                } catch (\BadMethodCallException $e) {
                    try {
                        $expected->assertHasNoneOf($given);
                    } catch (\BadMethodCallException $e) {
                        if (method_exists($expected, 'is')) {
                            Assert::assertFalse($expected->is($given));
                        } else {
                            Assert::assertFalse($expected <=> $given);
                        }
                    }
                }
            }
            
            return $this;
        });
        
        
        // BASIC TEST RESPONSES
        
        TestResponse::macro('assertCreated', function () {
            Assert::assertTrue(
                201 === $this->getStatusCode(),
                'Response status code [' . $this->getStatusCode() . '] does not match expected 201 status code.'
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
            Assert::assertTrue($callback((object)$this->oiginal->getData()));
            
            return $this;
        });
        
        TestResponse::macro('formatFailMessage', function ($variable, $message) {
            if (!is_array($variable)) {
                $str = str_replace(':attribute', get_class($variable), $message);
                
                return $str . "\nDetails - " . $variable;
            }
        });
        
        
        // MODEL
        
        
        Model::macro('tableHeaders', function () {
            return array_keys($this->attributes);
        });
        
        
        // DEFAULT FOR ALL TESTS
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