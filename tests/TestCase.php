<?php

namespace LoRDFM\Raw\Test;

use LoRDFM\Raw\RawFacade;
use LoRDFM\Raw\RawServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    /**
     * Load package service provider
     * @param  \Illuminate\Foundation\Application $app
     * @return LoRDFM\Raw\RawServiceProvider
     */
    protected function getPackageProviders($app)
    {
        return [RawServiceProvider::class];
    }

    /**
     * Load package alias
     * @param  \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'Raw' => RawFacade::class,
        ];
    }
}