<?php

namespace Ambengers\Kinetic\Tests\Composers;

use Inertia\ResponseFactory;

class UserComposer
{
    public static array $data = ['foo' => 'bar', 'baz' => 'buzz'];

    /**
     * @param  \Ambengers\Kinetic\Kinetic $inertia
     * @return void
     */
    public function compose(ResponseFactory $inertia)
    {
        $inertia->with('list', static::$data);
    }
}
