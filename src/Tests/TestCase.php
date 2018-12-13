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

        TestResponse::macro('data', function ($key = null) {
            if (!is_null($key)) {
                return $this->original->getData()[$key];
            }
            return is_null($this->original->getData()) ? null : collect($this->original->getData());

        });


        BaseCollection::macro('assertEquals', function ($items) {
            Assert::assertCount($items->count(), $this);

            $this->zip($items)->each(function ($itemPair) {
                Assert::assertTrue($itemPair[0]->is($itemPair[1]));
            });
        });

        BaseCollection::macro('assertContains', function ($item) {

            Assert::assertTrue($this->contains($item));
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
