# Kinetic

A package that adds view-composer like feature to Inertia.js Laravel adapter.

Use to be able to share props based on the component's name.

## Installation

`composer require ambengers/kinetic`

## Usage

This should be very intuitive if you are already familiar on how view composers work in Laravel.

You can use `Inertia::composer($component, $composer)` in any service provider to register composers for specific components.
The first argument accepts either a string or an array of Inertia components, and the second argument accepts either class or a callback function.

```
use Inertia;
use Inertia\ResponseFactory;
use App\Composers\UserComposer;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Class-based composer..
        Inertia::composer('User/Profile', UserComposer::class);

        // Callback-based composer..
        Inertia::composer('User/Profile', function (ResponseFactory $inertia) {
            //
        });
    }
}
```

### Class-based composers

Your class-based composers must contain a `compose` method that will accept an instance of `Inertia\ResponseFactory` class as the first argument.
Then you can call the `with(...)` method from the factory class to set the composed props.

```
class UserComposer
{
    public function compose(ResponseFactory $inertia)
    {
        $inertia->with('list',  ['foo' => 'bar', 'baz' => 'buzz']);
    }
}
```

### Callback-based composers

If you opt for a callback-based composer, your callback must accept an instance of `Inertia\ResponseFactory` class as the first argument.
Then you can call the `with(...)` method from the factory class to set the composed props.

```
Inertia::composer('User/Profile', function (ResponseFactory $inertia) {
    $inertia->with([
        'post' => [
            'subject' => 'Hello World!', 'description' => 'This is a description.'
         ]
    ]);
});
```

## Security

If you discover any security related issues, please email author email instead of using the issue tracker.

## License

Please see the [license file](license.md) for more information.
