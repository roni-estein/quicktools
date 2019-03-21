<?php

namespace Quicktools;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Support\Traits\Macroable;

class Model extends EloquentModel
{
    use Macroable {
        __call as macroCall;
    }
//
    /**
     * Handle dynamic method calls into the model.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (in_array($method, ['increment', 'decrement'])) {
            return $this->$method(...$parameters);
        }
//        //ENABLE MACROS ON MODEL AND CHILDREN
        if(method_exists(static::class, 'hasMacro')){
            if (static::hasMacro($method)) {
                return $this->macroCall($method, $parameters);
            }
        }

        return $this->forwardCallTo($this->newQuery(), $method, $parameters);
    }
    
    
    /**
     * Dynamically handle calls to the class.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    public static function __callStatic($method, $parameters)
    {
        if( isset(static::$macros[$method])){
            if (static::$macros[$method] instanceof Closure) {
                return call_user_func_array(Closure::bind(static::$macros[$method], null, static::class), $parameters);
            }
        }
    
        return (new static)->$method(...$parameters);
    }
    
    protected $guarded = [];
    
}