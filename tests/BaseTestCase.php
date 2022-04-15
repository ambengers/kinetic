<?php

namespace Ambengers\Kinetic\Tests;

use Ambengers\Kinetic\KineticServiceProvider;
use Illuminate\Support\Facades\View;
use Inertia\Inertia;
use Orchestra\Testbench\TestCase;

abstract class BaseTestCase extends TestCase
{
    public function setUp() : void
    {
        parent::setUp();

        View::addLocation(__DIR__.'/Views');

        Inertia::setRootView('app');
    }

    protected function getPackageProviders($app)
    {
        return [
            KineticServiceProvider::class,
        ];
    }
}
