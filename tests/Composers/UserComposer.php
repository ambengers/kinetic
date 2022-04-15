<?php

namespace Ambengers\Kinetic\Tests\Composers;

use Inertia\ResponseFactory;

class UserComposer
{
    public static $data = ['foo' => 'bar', 'baz' => 'buzz'];

    public function compose(ResponseFactory $inertia)
    {
        $inertia->with('list', static::$data);
    }
}
