<?php

namespace Quicktools\Tests;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

abstract class DBTestCase extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();

        DB::statement('PRAGMA foreign_keys=on');

        // Created refresh to remove deleted models from a collection once it's
        // refreshed to ensure collection size change

        EloquentCollection::macro('refresh', function ($with = []) {

            if ($this->isEmpty()) {
                return new static;
            }

            $model = $this->first();

            $freshModels = $model->newQueryWithoutScopes()
                ->with(is_string($with) ? func_get_args() : $with)
                ->whereIn($model->getKeyName(), $this->modelKeys())
                ->get()
                ->getDictionary();


            return $freshModels;

        });

    }

    /**
     * @param $expected
     * @param $actual
     * @return bool
     *
     * Returns a comparison with the fresh expected, to remove unwanted
     * metadata like was recently created flag producing false negatives
     */
    public function assertSameAttributes($expected, $actual): void
    {
        $this->assertEquals($expected->fresh(), $actual);
    }

    /**
     * Make a simple assertion that the object exists in the database
     *
     * @param $object
     * @return DBTestCase
     */
    public function assertDatabaseHasObject($object)
    {
        return $this->assertDatabaseHas($object->getTable(), ['id' => $object->id]);
    }


    /**
     * Make a simple assertion that the object does not exist in the database
     *
     * @param $object
     * @return DBTestCase
     */
    public function assertDatabaseMissingObject($object)
    {
        return $this->assertDatabaseMissing($object->getTable(), ['id' => $object->id]);
    }
}