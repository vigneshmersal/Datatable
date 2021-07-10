```php
// success
if (Request::header('X-Inertia')) {
    return Response::json([
        'component' => $component,
        'props' => $props,
        'url' => Request::getRequestUri(),
    ], 200, [
        'Vary' => 'Accept',
        'X-Inertia' => true,
    ]);
}

// exception
public function errorHandler($exception)
{
    return Response::json([
        'component' => 'Error',
        'props' => [
            'code' => $exception->getStatusCode(),
            'message' => $exception->getMessage(),
        ],
        'url' => Request::getRequestUri(),
    ], 200, [
        'Vary' => 'Accept',
        'X-Inertia' => true,
    ]);
}
```

Appserviceprovider.php
```php
// Synchronously
Inertia::share('app.name', Config::get('app.name'));

// Lazily by function()
// {{ page.props.auth.user.id }}
Inertia::share('auth.user', function () {
    if (Auth::user()) {
        return [
            'id' => Auth::user()->id,
        ];
    }
});
```