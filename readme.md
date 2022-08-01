# Kinetic

A package that adds view-composer like feature to Inertia.js Laravel adapter.

Use to be able to share props based on the Inertia component name.

[![CircleCI](https://circleci.com/gh/ambengers/kinetic/tree/main.svg?style=svg)](https://circleci.com/gh/ambengers/kinetic/tree/main)

## Installation

```sh
$ composer require ambengers/kinetic
```

## Usage

This should be very intuitive if you are already familiar on how view composers work in Laravel.

You can use `Inertia::composer()` in any service provider to register composers for specific components.
The first argument accepts either a string or an array of Inertia components, and the second argument accepts either class string or a closure.

```php
use Inertia;
use Inertia\ResponseFactory;
use App\Composers\UserComposer;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Class-based composer..
        Inertia::composer('User/Profile', UserComposer::class);

        // Closure-based composer..
        Inertia::composer('User/Profile', function (ResponseFactory $inertia) {
            //
        });
    }
}
```

### Class-based composers

You can generate your composer class using this command:

```sh
$ php artisan kinetic:composer UserComposer
```

Then you can call the `$inertia->with()` method within the compose method to set the composed props, like so:

```php
class UserComposer
{
    public function compose(ResponseFactory $inertia)
    {
        $inertia->with('list',  ['foo' => 'bar', 'baz' => 'buzz']);
    }
}
```

### Closure-based composers

If you opt for a closure-based composer, your closure must accept an instance of `Inertia\ResponseFactory` class as the first argument.
Then you can call the `with()` method from the factory class to set the composed props like so:

```php
Inertia::composer('User/Profile', function (ResponseFactory $inertia) {
    $inertia->with([
        'post' => [
            'subject' => 'Hello World!', 'description' => 'This is a description.'
         ]
    ]);
});
```

### Multiple composers

You can also set multiple composers to components using array, like so:

```php
Inertia::composer(['User/Profile', 'User/Index'], [
    UserComposer::class,
    function (ResponseFactory $inertia) {
        $inertia->with(...);
    }
]);
```

The array will be automatically merged with any existing composers for the components.

When you call the `Inertia::render('User/Profile')` the props should now include the composed data.

### Attaching a composer to all components

You can also set a composer or multiple composers to all components using the * character as a wildcard, like so:

```php
Inertia::composer('*', [
    UserComposer::class,
    function (ResponseFactory $inertia) {
        $inertia->with(...);
    }
]);
```

## Security

If you discover any security related issues, please email author email instead of using the issue tracker.

## License

Please see the [license file](license.md) for more information.
