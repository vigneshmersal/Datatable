# optimize

## PHP memory usage
```php
round(memory_get_peak_usage() / 1024 / 1024)
return 'Memory Usage: '.round(xdebug_peak_memory_usage()/1048576, $precision=2) . 'MB';
```

## Insert query
```php
User::insert([]); // manually add 'created_at,updated_at' => now()->toDateTimeString()
DB::insert('insert into users (email, votes) values (?, ?)', ['john@example.com', '0']);
```

## where condition
```php
where('created_at', '>', now()->subDays(29)) // use date filter first
where('name', 'like', '%vig%') // use like filter second
```

## cursor / chunk
```php
foreach( App\Model::cursor() as $each ) { }

# To load bulk data from server like 100120 data's it will take more memory 100MB
# But if u use cursor() it will split the query and optimize the memory into 16MB
# Usecase - No, one will show 100000 records on a single page. But there are much other use cases where you may need to fetch 100000 records. For example, you may need for report generating like analytics, or to update each record for some reason, or you may need to send a notification to 100000 users or you may need to clean up some records based on some conditions. There are many many use cases where you may need to fetch 100000 records.
Question::all();
Question::cursor(10000, function($questions){});

cursor() - It takes less time, Uses more memory
chunk(1000) - Uses less memory , It takes longer
```

## belongsto relation select columns
```php
public function user() { return $this->belongsTo('User')->select(['id', 'username']); }
Post::with(['user' => function ($query) { $query->select('id','username') }])->get();
Post::with('user:user.id,username')->get();
```

## hasmany relation select columns
```php
$s=Sector::with(['courses' => function($qry) {
     $qry->select(['id', 'name', 'sector_id']); // sector_id - foreign key is required
}])->get();
public function courses() { return $this->hasMany(Course::class)->select(['id', 'sector_id']); }
```

## select columns
```php
# instead of passing hole response, get used data's only in array format
User::all(); // bulk
User::get()->toArray(); // hole column
User::get()->pluck('name')->toArray(); // less column
```

## cache query
```php
$cacheKey = md5(vsprintf('%s.%s', [$user->id,$days]))
Cache::remember('index.posts', $seconds = 30, function() { // time - 60*60*24
    return Post::all(['id', 'name']);
    return Post::with(['user:id,name', 'comments'])->get()->map(function($post) {
        return [
            'title' => $post->title,
            'comments' => $post->comments->pluck('body'),
        ];
    })->toArray(); // toArray() - store data's only
}); // use blade files as array format
Cache::forget('index.posts'); // php artisan cache:clear
```

# with condition - solve n+1 query issue
```php
Post::with('user')->get(); // instead of Post::all()
Hotel::with('city')->withCount(['bookings'])->get(); // bookings_count
$post->load('user');
```

## livewire computed property (won't make a seperate database query every time)
```php
public function getPostProperty() { // access by $this->post
    return Post::find($this->postId);
}
```

## faster application into 100x
https://deliciousbrains.com/optimizing-laravel-performance-basics/

```php
- php artisan route:cache (boostrap/cache/route.php) - closure functions wont work
- php artisan optimize

faster config/ dir: (get .env value and minimized array stored in - boostrap/config/cache.php)
dont use env() function outside of config folder
- php artisan config:cache (to remove -> clear:cache)

Composer optimize autoload (one-to-one-association of the class)
- composer dumpautoload -o

Fastest cache & session driver:
- memcached

remove unused service, add in
- config/app.php

package
- laravel page speed package

Object cache
- singleton
```
