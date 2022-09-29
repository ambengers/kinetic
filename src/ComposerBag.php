<?php

namespace Ambengers\Kinetic;

use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Inertia\ResponseFactory;

class ComposerBag
{
    /** @var ResponseFactory */
    protected $factory;

    /** @var array */
    protected $composers = [];

    /**
     * @param  ResponseFactory  $factory
     */
    public function __construct(ResponseFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param  string|array           $components
     * @param  \Closure|array|string  $composers
     *
     * @return self
     */
    public function set($components, $composers): self
    {
        $composers = is_array($composers) ? $composers : [$composers];

        foreach ((array) $components as $component) {
            // Let us merge the existing composers for the component if any
            // and then we will make sure we only set unique composers...
            $composers = Collection::make([
                ...Arr::wrap(Arr::get($this->composers, $component, [])),
                ...$composers,
            ])->unique()->toArray();

            $this->composers[$component] = $composers;
        }

        return $this;
    }

    /**
     * @param  string|null             $component
     * @param  \Closure|array|string   $default
     * @return array
     */
    public function get($component = null, $default = [])
    {
        if (is_null($component)) {
            return $this->composers;
        }

        $composers = $this->composers;

        foreach ($this->composers as $page => $composer) {
            if (Str::contains($page, '*') && Str::is($page, $component)) {
                $composers[$component] = array_merge(
                    Arr::wrap(Arr::get($composers, $component, [])),
                    Arr::wrap($composer),
                );
            }
        }
        
        return Arr::wrap(Arr::get($composers, $component, $default));
    }

    /**
     * @param  string  $component
     *
     * @return void
     */
    public function compose($component)
    {
        $composers = $this->get($component);

        foreach ($composers as $composer) {
            if (is_string($composer)) {
                app($composer)->compose($this->factory);
            } else {
                $composer($this->factory);
            }
        }
    }
}
