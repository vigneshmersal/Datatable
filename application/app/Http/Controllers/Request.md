# Request

```php
$requestHost = $request->header('route');
$requestHost = $request->header('Host');
'ip' => $request->getClientIp(),
'url' => Request::getRequestUri(),
'agent' => $request->header('User-Agent'),
```

```php
# request from api
if (Request::wantsJson()) {}
if( $request->is('api/*')){}

$request->getRequestUri(); // /api/v1/tests_in_pause?apikey=qw42yunk
$request->getQueryString();
```

## Remove
```php
unset($request->name)
```

## Modify
```php
$request->merge([
    'user_id' => $modified_user_id_here,
]);
$request->request->set(key, value).
$request->request->add(['newParam' => $newParam]);
```

# GET
```php
# Retrieve all data
$request->all();
$request->input();
$request->query();

# Retrieve individual data
$request->name;
$request->get('name');
$request->input('name', 'default');

# Retrieve data from only query string -> ?name=vignesh
$request->query('name', 'default');

# Retrieve checkboxes deal with either true (or) 1
$archived = $request->boolean('archived');

# It will useful while save the bulk records
$input = $request->only(['username', 'password']);
$input = $request->except(['credit_card']);

# Retrieving JSON Input Values -> Content-Type: application/json
$name = $request->input('user.name');

# When working with forms that contain array inputs, use "dot" notation to access the arrays:
$request->input('products.0.name'); # get individual data
$request->input('products.*.name'); # get array of data
```

## check condition
```php
# check var exist with the val
if ($request->has('name')) { }
if ($request->has(['name', 'email'])) { }
if ($request->hasAny(['name', 'email'])) { }
if ($request->filled('name')) { } // present & not empty
if ($request->exists('name')) { } // present (checkbox, query string)

if ($request->missing('name')) { } // absent
if ($request->missing('name')) {
    $request->request->add(['name' => 'vig']);
}

# check view exist
if (View::exists('emails.customer')) { }
```

## Old data flash session
```php
# save the current input to the session
$request->flash();
$request->flashOnly(['username', 'email']);
$request->flashExcept('password');

# Retrieving Old Input
$username = $request->old('username');

// If validation failed redirect with flash data
return redirect('form')->withInput(
    $request->except('password')
);
```
