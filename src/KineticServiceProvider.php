<?php

namespace Ambengers\Kinetic;

use Illuminate\Support\ServiceProvider;
use Inertia\ResponseFactory;

class KineticServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton(ComposerBag::class);
        $this->app->singleton(Kinetic::class);
        $this->app->bind(ResponseFactory::class, Kinetic::class);

        if ($this->app->runningInConsole()) {
            $this->commands(KineticComposerCommand::class);
        }
    }
}
