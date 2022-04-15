<?php

namespace Ambengers\Kinetic;

use Illuminate\Contracts\Support\Arrayable;
use Inertia\Response;
use Inertia\ResponseFactory;

class Kinetic extends ResponseFactory
{
    /** @var array */
    protected $composedProps = [];

    public function flushShared(): void
    {
        $this->sharedProps = [];
        $this->composedProps = [];
    }

    /**
     * @param  string  $component
     * @param  array|Arrayable  $props
     * @return Response
     */
    public function render(string $component, $props = []): Response
    {
        if ($props instanceof Arrayable) {
            $props = $props->toArray();
        }

        $this->resolveComposedProps($component);

        return new Response(
            $component,
            array_merge($this->sharedProps, $props, $this->composedProps),
            $this->rootView,
            $this->getVersion()
        );
    }

    /**
     * @param  string|array  $component
     * @param  Closure|string  $composer
     */
    public function composer($components, $composer)
    {
        foreach ((array) $components as $component) {
            $this->composerBag()->set($component, $composer);
        }

        return $this;
    }

    /**
     * @param  string|array|Arrayable  $key
     * @param  mixed|null  $value
     */
    public function with($key, $value = null)
    {
        if (is_array($key)) {
            $this->composedProps = array_merge($this->composedProps, $key);
        } else {
            $this->composedProps[$key] = $value;
        }

        return $this;
    }

    protected function composerBag()
    {
        return app(ComposerBag::class);
    }

    protected function resolveComposedProps($component) : void
    {
        $this->composerBag()->compose($component);
    }
}
