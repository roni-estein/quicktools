<?php

namespace Quicktools;


class BaseProject
{
    public static function testPath($filename = null){
        if(is_null($filename)) return base_path().'/tests';
        return base_path().'/tests/' . $filename;
    }
}