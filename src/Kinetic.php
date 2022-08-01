<?php

namespace Ambengers\Kinetic;

use Illuminate\Contracts\Support\Arrayable;
use Inertia\Response;
use Inertia\ResponseFactory;

/**
 * @mixin \Inertia\ResponseFactory
 */
class Kinetic extends ResponseFactory
{
    /** @var array */
    protected $composedProps = [];

    public function flushShared(): void
    {
        $this->composedProps = [];
        parent::flushShared();
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

        // The composer method also accepts the * character as a wildcard, allowing you to attach a composer to all components
        $this->resolveComposedProps('*');

        return parent::render($component, array_merge($props, $this->composedProps));
    }

    /**
     * @param  string|array             $components
     * @param  \Closure|array|string    $composers
     *
     * @return self
     */
    public function composer($components, $composers): self
    {
        $this->composerBag()->set($components, $composers);

        return $this;
    }

    /**
     * @param  string|array  $key
     * @param  mixed|null  $value
     *
     * @return self
     */
    public function with($key, $value = null): self
    {
        if (is_array($key)) {
            $this->composedProps = array_merge($this->composedProps, $key);
        } else {
            $this->composedProps[$key] = $value;
        }

        return $this;
    }

    /**
     * @return \Ambengers\Kinetic\ComposerBag
     */
    protected function composerBag()
    {
        return app(ComposerBag::class);
    }

    /**
     * @param  string $component
     * @return void
     */
    protected function resolveComposedProps(string $component) : void
    {
        $this->composerBag()->compose($component);
    }
}
