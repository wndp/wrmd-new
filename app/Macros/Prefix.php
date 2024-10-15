<?php

namespace App\Macros;

class Prefix
{
    public function __invoke()
    {
        return function (array $array, $prefix) {
            array_walk($array, function (&$item, $key, $prefix) {
                return $item = $prefix.$item;
            }, $prefix);

            return $array;
        };
    }
}
