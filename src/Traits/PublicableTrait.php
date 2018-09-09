<?php

namespace App\Traits;

trait PublicableTrait
{
    public function getPublicObject()
    {
        $array = [];
        foreach ($this as $key => $value) {
            $array[$key] = $value;
        }
        return $array;
    }
}