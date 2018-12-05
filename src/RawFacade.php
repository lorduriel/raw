<?php

namespace LoRDFM\Raw;

use Illuminate\Support\Facades\Facade;

class RawFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'Raw';
    }
}