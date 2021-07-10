
```php
latest() // order by created_at
oldest() // order by created_at
newest('updated_at')
reverse()
```

## orderBy
```php
orderBy('column', 'desc')
```

## sort
```php
sort() | sortDesc()
```

## sortBy
```php
sortBy($column) | sortByDesc($column)

# sortBy sub items count
sortBy(function ($item, $key) { return count($item['sub']); }); 

# Order by relationship
$users = User::with('latestPost')->get()->sortByDesc('latestPost.created_at');

# Order by Mutator
$users = User::get()->sortBy('full_name'); // first_name.' '.last_name
```

## raw
```php
->orderByRaw('YEAR(birth_date)')

# WhereIn(id) sort
->orderByRaw("field(id,".implode(',',$videoIds).")")
```

## Make all columns sortable
```php
<th><a class="{{ request('order', 'name') === 'name' ? 'text-dark' : '' }}" href="{{ route('customers', ['order' => 'name'] + request()->except('page')) }}">Name</a></th>
<th><a class="{{ request('order') === 'company' ? 'text-dark' : '' }}" href="{{ route('customers', ['order' => 'company'] + request()->except('page')) }}">Company</a></th>
<th><a class="{{ request('order') === 'birthday' ? 'text-dark' : '' }}" href="{{ route('customers', ['order' => 'birthday'] + request()->except('page')) }}">Birthday</a></th>
<th><a class="{{ request('order') === 'last_interaction' ? 'text-dark' : '' }}" href="{{ route('customers', ['order' => 'last_interaction'] + request()->except('page')) }}">Last Interaction</a></th>

$customers = Customer::with('company')
    ->withLastInteraction()
    ->orderByField($request->get('order', 'name'))
    ->paginate();

public function scopeOrderByField($query, $field)
{
    if ($field === 'name') {
        $query->orderByName();
    } elseif ($field === 'company') {
        $query->orderByCompany();
    } elseif ($field === 'birthday') {
        $query->orderByBirthday();
    } elseif ($field === 'last_interaction') {
        $query->orderByLastInteractionDate();
    }
}

public function scopeOrderByCompany($query)
{
    $query->join('companies', 'companies.id', '=', 'customers.company_id')->orderBy('companies.name');
}
// or
public function scopeOrderByCompany($query)
{
    $query->orderBySub(Company::select('name')->whereRaw('customers.company_id = companies.id'));
}
Builder::macro('orderBySub', function ($query, $direction = 'asc') {
    return $this->orderByRaw("({$query->limit(1)->toSql()}) {$direction}");
});

Builder::macro('orderBySubDesc', function ($query) {
    return $this->orderBySub($query, 'desc');
});

public function scopeOrderByBirthday($query)
{
    $query->orderbyRaw("to_char(birth_date, 'MMDD')");
}

public function scopeOrderByLastInteractionDate($query)
{
    $query->orderBySubDesc(Interaction::select('created_at')->whereRaw('customers.id = interactions.customer_id')->latest());
}
```

## global scope
```php
protected static function booted()
{
    static::addGlobalScope(fn ($query) => $query->orderBy('name'));
}
```

## relationship order
### Hasone / belongsTo
```php
// join query - very fast
$users = User::select('users.*')
    ->join('companies', 'companies.user_id', '=', 'users.id')
    ->orderBy('companies.name')
    ->get();

// subquery order - very slow
$users = User::orderBy(
    Company::select('name')->whereColumn('companies.user_id', 'users.id')
)->get();
```
### Hasmany
```php
// subquery order - fast
$users = User::orderByDesc(Login::select('created_at')
    ->whereColumn('logins.user_id', 'users.id')
    ->latest()->take(1)
)->get();

// join - fast
$users = User::select('users.*')
    ->join('logins', 'logins.user_id', '=', 'users.id')
    ->groupBy('users.id')
    ->orderByRaw('max(logins.created_at) desc') // using max is correct, 
    // ->orderByDesc('logins.created_at') is this incorrect for group by order
    ->get();

// set index
$table->index(['user_id', 'created_at']);
```

### Ordering by belongs-to-many relationships
```php
class Book extends Model
{
    public function user()
    {
        return $this->belongsToMany(User::class, 'checkouts')
            ->using(Checkout::class)->withPivot('borrowed_date');
    }
}
// subquery with pivot table
$books = Books::orderByDesc(Checkout::select('borrowed_date')
    ->whereColumn('book_id', 'books.id')
    ->latest('borrowed_date')->limit(1)
)->get();
// (or) using closure
$books = Books::orderByDesc(function ($query) {
    $query->select('borrowed_date')
        ->from('checkouts')
        ->whereColumn('book_id', 'books.id')
        ->latest('borrowed_date')
        ->limit(1);
})->get();

// subquery with relation table - slow performance issue
$books = Book::orderBy(User::select('name')
    ->join('checkouts', 'checkouts.user_id', '=', 'users.id')
    ->whereColumn('checkouts.book_id', 'books.id')
    ->latest('checkouts.borrowed_date')
    ->take(1)
)->get();
// or
$table->foreignId('last_checkout_id')->nullable()->constrained('checkouts');
```