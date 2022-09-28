<?php

namespace Ambengers\Kinetic\Tests\Features;

use Ambengers\Kinetic\ComposerBag;
use Ambengers\Kinetic\Tests\BaseTestCase;
use Ambengers\Kinetic\Tests\Composers\UserComposer;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

class KineticTest extends BaseTestCase
{
    public function test_can_set_class_based_composer_to_composer_bag(): void
    {
        app(ComposerBag::class)->set('User', UserComposer::class);

        $this->assertEquals([UserComposer::class], app(ComposerBag::class)->get('User'));
    }

    public function test_can_set_closure_based_composer_to_composer_bag(): void
    {
        app(ComposerBag::class)->set('User', $callback = fn () => 'Hello User!');

        $this->assertEquals([$callback], app(ComposerBag::class)->get('User'));
    }

    public function test_can_set_array_of_composers_to_composer_bag(): void
    {
        $composers = [
            UserComposer::class,
            $callback = fn () => 'Hello User!',
        ];

        app(ComposerBag::class)->set('User', $composers);

        $this->assertEquals($composers, app(ComposerBag::class)->get('User'));
    }


    public function test_additional_composers_are_merged_with_existing_ones(): void
    {
        $composers = [
            UserComposer::class,
            fn () => 'Hello User!',
        ];

        app(ComposerBag::class)->set('User', $composers);

        app(ComposerBag::class)->set('User', $callback = fn () => 'Hey There!');

        $this->assertEquals([...$composers, $callback], app(ComposerBag::class)->get('User'));
    }

    public function test_duplicate_composers_are_removed_from_the_bag(): void
    {
        $composers = [
            UserComposer::class,
            $callback = fn () => 'Hello User!',
        ];

        app(ComposerBag::class)->set('User', $composers);

        app(ComposerBag::class)->set('User', UserComposer::class);
        app(ComposerBag::class)->set('User', $callback);

        $this->assertEquals($composers, app(ComposerBag::class)->get('User'));
    }

    public function test_can_get_all_composers_from_composer_bag(): void
    {
        app(ComposerBag::class)->set('User', UserComposer::class);
        app(ComposerBag::class)->set('User', $user = fn () => 'Hello User!');
        app(ComposerBag::class)->set('User/Profile', $profile = fn () => 'Hello User Profile!');

        $this->assertEquals([
            'User' => [UserComposer::class, $user],
            'User/Profile' => [$profile],
        ], app(ComposerBag::class)->get());
    }

    public function test_can_set_same_composer_for_multiple_components(): void
    {
        Inertia::composer(['User', 'User/Profile'], UserComposer::class);

        $this->assertEquals([
            'User' => [UserComposer::class],
            'User/Profile' => [UserComposer::class],
        ], app(ComposerBag::class)->get());
    }

    public function test_can_use_wildcard_composer_for_nested_components()
    {
        Inertia::composer('User/*', UserComposer::class);

        $this->assertEquals(
            [],
            app(ComposerBag::class)->get('User')
        );

        $this->assertEquals(
            [UserComposer::class],
            app(ComposerBag::class)->get('User/Profile')
        );

        $this->assertEquals(
            [UserComposer::class],
            app(ComposerBag::class)->get('User/Admin/Profile')
        );
    }

    public function test_can_use_wildcard_composer_with_named_and_closure_composers()
    {
        Inertia::composer('User/*', UserComposer::class);
        Inertia::composer('User/Profile', $callback = function () {});

        $this->assertEquals(
            [$callback, UserComposer::class],
            app(ComposerBag::class)->get('User/Profile')
        );
    }

    public function test_can_use_nested_wildcard_composers()
    {
        Inertia::composer(['User/*', 'Users/*/Profile'], UserComposer::class);

        $this->assertEquals(
            [UserComposer::class],
            app(ComposerBag::class)->get('User/Profile')
        );

        $this->assertEquals(
            [UserComposer::class],
            app(ComposerBag::class)->get('User/Admin/Profile')
        );
    }

    public function test_can_use_class_based_composers_for_a_component(): void
    {
        Inertia::composer('User/Profile', UserComposer::class);

        $this->assertEquals(
            [UserComposer::class],
            app(ComposerBag::class)->get('User/Profile')
        );

        Route::middleware([StartSession::class])->get('/', function () {
            return Inertia::render('User/Profile', ['user' => 'John Doe']);
        });

        $response = $this->withoutExceptionHandling()->get('/', ['X-Inertia' => 'true']);

        $response->assertSuccessful();
        $response->assertJson([
            'component' => 'User/Profile',
            'props' => array_merge(['user' => 'John Doe'], ['list' => UserComposer::$data]),
        ]);
    }

    public function test_can_use_closure_based_composer_for_a_component(): void
    {
        $post = [
            'title' => 'Composer from callback',
            'description' => 'This is just a test. Please disregard.',
        ];

        Inertia::composer('User/Profile', $callback = function ($inertia) use ($post) {
            $inertia->with(['post' => $post]);
        });

        $this->assertEquals(
            [$callback],
            app(ComposerBag::class)->get('User/Profile')
        );

        Route::middleware([StartSession::class])->get('/', function () {
            return Inertia::render('User/Profile', ['user' => 'John Doe']);
        });

        $response = $this->withoutExceptionHandling()->get('/', ['X-Inertia' => 'true']);

        $response->assertSuccessful();
        $response->assertJson([
            'component' => 'User/Profile',
            'props' => array_merge(['user' => 'John Doe'], ['post' => $post]),
        ]);
    }

    public function test_can_use_multiple_composer_for_a_component(): void
    {
        $post = [
            'title' => 'Composer from callback',
            'description' => 'This is just a test. Please disregard.',
        ];

        Inertia::composer('User/Profile', UserComposer::class);
        Inertia::composer('User/Profile', $callback = function ($inertia) use ($post) {
            $inertia->with(['post' => $post]);
        });

        $this->assertEquals(
            [UserComposer::class, $callback],
            app(ComposerBag::class)->get('User/Profile')
        );

        Route::middleware([StartSession::class])->get('/', function () {
            return Inertia::render('User/Profile', ['user' => 'John Doe']);
        });

        $response = $this->withoutExceptionHandling()->get('/', ['X-Inertia' => 'true']);

        $response->assertSuccessful();
        $response->assertJson([
            'component' => 'User/Profile',
            'props' => array_merge(
                ['user' => 'John Doe'],
                ['list' => UserComposer::$data],
                ['post' => $post]
            ),
        ]);
    }
}
