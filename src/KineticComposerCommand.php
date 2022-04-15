<?php

namespace Ambengers\Kinetic;

use Illuminate\Console\GeneratorCommand;

class KineticComposerCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kinetic:composer {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a Kinetic composer class.';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'../stubs/composer.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Inertia\Composers';
    }
}
