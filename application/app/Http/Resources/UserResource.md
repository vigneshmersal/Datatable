# Modal Resource
Create both `Resource` and `collection`
> php artisan make:resource Users --collection

Create Resource
> php artisan make:resource User

```php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
{
	/**
	 * Indicates if the resource's collection keys should be preserved.
	 *
	 * @var bool
	 */
	public $preserveKeys = true;

	/**
     * The "data" wrapper that should be applied.
     *
     * @var string
     */
    public static $wrap = 'user'; // instead of o/p - data {} -> user {}

    protected $api;

	// pass additional data
	Model::get()->map(function ($post) {
		$post['url'] = 'http://your.url/here';
		return $post;
	});

    public function api($value){
        $this->api = $value;
        return $this;
    }

    public function set($key, $value){
        $this->{$key} = $value;
        return $this;
    }

    private $foo;
    // return new PostResource($post, 'my variable');
    public function __construct($resource, $foo)
    {
        // Ensure you call the parent constructor
        parent::__construct($resource);
        $this->resource = $resource;
        $this->foo = $foo;
    }

    public static function collection($resource){
        return new UserResourceCollection($resource);
    }

	/**
	* Transform the resource into an array.
	*
	* @param \Illuminate\Http\Request $request
	* @return array
	*/
	public function toArray($request) {

		return parent::toArray($request);

		// (or)
		// get attribute
		$convertTo = $this->attributes['meta']['convertTo'];
		// call function
		'conversion' => $this->convertTo($convertTo, $this->bitcoin)

		foreach ($this as $item) {
			$data[]['details'] =  [
				'subject' => $item->subject,
			];
		}
		return $data;

		return [
			'id' => $this->id,
			'name' => $this->name,
			'email' => $this->email,
			'created_at' => (string) $this->created_at,
			'created_at' => Carbon::parse($this->created_at)->format("Y-m-d"),
			'updated_at' => (string) $this->updated_at,

			// Relationships
			'posts' => PostResource::collection($this->posts),


			// Conditional Relationships (solve N+1 query loading problem)
			'posts' => PostResource::collection($this->whenLoaded('posts')),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'category' => $this->when($this->relationLoaded('category'), $this->category->name ?? ''),
			'posts' => $this->when($this->relationLoaded('posts'), function () {
                return PostResource::collection($this->posts);
            }),

			// get response 
			$product = new ProductResource($this->product);
			$status = $product->response()->getData(true)['data']['user']['purchase']['status'];

			// Conditional Pivot Information
			'expires_at' => $this->whenPivotLoaded('role_user', function () {
				return $this->pivot->expires_at;
			}),

			// If your intermediate table is using an accessor other than pivot
			'expires_at' => $this->whenPivotLoadedAs('subscription', 'role_user', function () {
				return $this->subscription->expires_at;
			}),

			// Conditional Attributes
			'secret' => $this->when(Auth::user()->isAdmin(), 'secret-value'),
			'test_type' => $this->when($this->test_type, MockTest::TEST_TYPE_SELECT[$this->test_type]),
			'secret' => $this->when(Auth::user()->isAdmin(), function () {
				return 'secret-value';
			}),

			// Merging Conditional Attributes
			$this->mergeWhen(Auth::user()->isAdmin(), [
				'first-secret' => 'value',
				'second-secret' => 'value',
			]),
			$this->mergeWhen($this->section, function() {
				$collect = collect();
				foreach($this->section as $key => $section)
				{
					$collect->push([
						"name"            => $section['name'],
						"mock_questions"  => collect([
							'english' => MockQuestionResource::collection($this->questions(1)),
							'hindi' => MockQuestionResource::collection($this->questions(2)),
						]),
					]);
				}
				return ['section' => $collect];
			}),

			'pagination' => [
				'base' => $paginated->url(1),
	            'total' => $this->total(),
	            'count' => $this->count(),
	            'per_page' => $this->perPage(),
	            'current_page' => $this->currentPage(),
	            'total_pages' => $this->lastPage(),
	            'next' => $paginated->nextPageUrl(),
        		'prev' => $paginated->previousPageUrl(),
	        ],

	        'meta' => ['song_count' => $this->collection->count()],

	        return [
	            'data' => $this->collection->transform(function($item) {
	                return $item->only(['id', 'technology_strategy', 'icon']);
	            })
	        ];
		];
	}

	/**
	 * Customize the outgoing response for the resource.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Illuminate\Http\Response  $response
	 * @return void
	 */
	public function withResponse($request, $response)
	{
		$response->header('X-Value', 'True');
	}

	# customize laravel resource collection pagination
	public function withResponse($request, $response)
    {
        $jsonResponse = json_decode($response->getContent(), true);
        unset($jsonResponse['links'],$jsonResponse['meta']);
        $response->setContent(json_encode($jsonResponse));
    }

	/**
	 * Adding Additional Data
	 *
	 * @param  [type] $request [description]
	 * @return [type]          [description]
	 */
	// return (new SongResource(Song::find(1)))->additional([
 //        'meta' => [
 //            'anything' => 'Some Value'
 //        ]
 //    ]);
	public function with($request)
	{
		return [
            'meta' => [
                'key' => 'value',
            ],
        ];
		return [
			'version' => '1.0.0',
			'api_url' => url('http://lpgvue/api')
		];
	}
}
```

## Sample Result

```json
{
	"data": [
		{
			"id": 1,
			"name": "Eladio Schroeder Sr.",
			"email": "therese28@example.com",
		},
		{
			"id": 2,
			"name": "Liliana Mayert",
			"email": "evandervort@example.com",
		}
	],
	"version": "1.0.0",
	"api_url": "http://lpgvue/api"
}
```
