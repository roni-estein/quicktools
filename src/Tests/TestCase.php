<?php

namespace Quicktools\Tests;

use Quicktools\Model;
use PHPUnit\Framework\Assert;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Foundation\Testing\Assert as PHPUnit;
use Illuminate\Support\Collection as BaseCollection;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Symfony\Component\Console\Output\ConsoleOutput;

abstract class TestCase extends BaseTestCase
{
    use \Tests\CreatesApplication;
    
    // Determine in a view has been loaded or if a view is missing
    // This will even drill down to view partials
    use Viewable;
    public $output;
    
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->output = new ConsoleOutput();
    }
    
    public function setUp(): void
    {
        parent::setUp();
        
        //HELPER
        
        //JSON
        
        
        //COLLECTIONS
        
        BaseCollection::macro('assertHasNoneOf', function ($items) {
            if ($items == null) {
                throw new Exception('Trying to assert Collection does not contains a root object null');
            }
            if (is_scalar($items)) {
                return Assert::assertFalse($this->contains($items));
            } elseif (is_array($items)) {
                return $this->assertDoesNotContain(collect($items));
            }

            return $this->assertDoesNotContain($items);
        });
        
        BaseCollection::macro('assertContains', function ($item, $orderedKey = null) {
            Assert::assertTrue($this->contains($item) || $this->equals($item, $orderedKey));
        });
        
        
        BaseCollection::macro('assertDoesNotContain', function ($items) {
            if (is_scalar($items)) {
                return Assert::assertFalse($this->contains($items));
            }
            foreach ($items as $item) {
                Assert::assertFalse($this->contains($item));
            }
        });
        
        BaseCollection::macro('equals', function ($items, $orderedKey = null) {
            if ($items->count() !== $this->count()) {
                return false;
            }
            
            $self = $this;
            
            if ( ! is_null($orderedKey)) {
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
            
            if ( ! is_null($orderedKey)) {
                $self = $this->sortBy($orderedKey);
                $items = $items->sortBy($orderedKey);
            }
            
            $self->zip($items)->each(function ($itemPair) use ($self, $items) {
                if (gettype($itemPair[0]) !== gettype($itemPair[1])) {
                    Assert::fail(
                        $itemPair[0] . ' is a ' . gettype($itemPair[0]) . ' and ' .
                        $itemPair[1] . ' is a ' . gettype($itemPair[1])
                    );
                }
    
                if (is_a($itemPair[0], "Illuminate\Support\Collection") && is_a($itemPair[0], "Illuminate\Support\Collection")) {
                    $itemPair[0]->assertEquals($itemPair[1]);
                } else {
                    Assert::assertTrue(
                        ($itemPair[0] <=> $itemPair[1]) == 0 || //primitives
                        $itemPair[0]->is($itemPair[1])   //laravel models and objects that inherit "is"
                        , $itemPair[0] . ' is not equal to ' . $itemPair[1] . 'in collections :'."\n\n".$self."\n\n".$this
                    );
                }
            });
        });
        
        
        // VUE PAGE COMPONENTS
        
        TestResponse::macro('assertComponentIs', function($name){
            $this->ensureResponseHasView();
            
            PHPUnit::assertEquals($name, $this->data('name'));
            
            return $this;
        });
        
        TestResponse::macro('assertPageComponentContains', function (...$options) {
            if (count($options) === 1 || count($options) === 2 && gettype($options[1]) == 'string') {
                //todo: figure out how to sort when we dont know where the object is
                $passes = collect($this->data('data'))->first(function ($item) use ($options) {
                    return ($item <=> $options[0]) == 0 || $item->is($options[0]);
                });
                
                Assert::assertNotNull($passes, $this->formatFailMessage($options[0], ':attribute not found in this component'));
                
                return $this;
            } elseif (count($options) > 1) {
                if ( ! isset($this->data('data')[$options[0]])) {
                    Assert::fail('No prop data : "' . $options[0] . '" foound for this component!');
                }
                
                $expected = $this->data('data')[$options[0]];
                $given = $options[1];
                $sortBy = isset($options[2]) ? $options[2] : null;
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
            if ( ! is_null($key)) {
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
        
        TestResponse::macro('formatFailMessage', function ($variable, $message) {
            if ( ! is_array($variable)) {
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
        if(! is_null(session()->get('errors'))){
    
            ddf(session()->get('errors')->getBag($errorBag),2,1);
        }else{
            $this->output->writeln('There were no validation errors');
            return ;
        }
    }
    
    protected function logoutUser($guard = null)
    {
        return $this->post(route('logout'));
    }
    
    public function decodeJson($data, $key = null)
    {
        $decodedJson = json_decode($data, true);

//        if (is_null($decodedJson) || $decodedJson === false) {
//            if ($this->exception) {
//                throw $this->exception;
//            } else {
//                PHPUnit::fail('Invalid JSON was returned from the route.');
//            }
//        }
        
        return data_get($decodedJson, $key);
    }
    
    public function assertJsonMessage(array $expected, array $actual)
    {
        $expected = json_encode($expected, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        
        $actual = json_encode($actual, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        
        return 'Unable to find JSON: '.PHP_EOL.PHP_EOL.
            "[{$expected}]".PHP_EOL.PHP_EOL.
            'within response JSON:'.PHP_EOL.PHP_EOL.
            "[{$actual}].".PHP_EOL.PHP_EOL;
    }
    
    public function assertJsonSubset($expected, $actual, $strict = false)
    {
        
        if ( ! is_array($expected)) {
            $expected = $this->decodeJson($expected);
        }
        if(count($expected) === 0){
            ddf('Failure: $expected dataset cannot be empty', 2);
        }
        
        if ( ! is_array($actual)) {
            $actual = $this->decodeJson($actual);
        }
    
        if(count($expected) === 0){
            $this->fail('$actual dataset was empty');
        }
        
        PHPUnit::assertArraySubset($expected,
            $actual, $strict, $this->assertJsonMessage($expected, $actual));
    
        return $this;
    }
}
