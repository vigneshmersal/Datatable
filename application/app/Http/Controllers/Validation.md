# Validation - Error code 422
[40-additional-laravel-validation-rules/](https://laraveldaily.com/40-additional-laravel-validation-rules/)

# Email Validate
```php
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
	throw new InvalidArgumentException;
}
```

```php
# direct validate
$validatedData = $request->validate([
	'author.name' => ['required', new Uppercase,
			function ($attribute, $value, $fail) {
	            if ($value === 'foo') {
	            	$fail($attribute.' is invalid.');
	            }
	        }
		],
]);

$messages = [
	# field with rule
    'email.required' => 'We need to know your e-mail address!', // email.required
    # only rule
    'required' => 'The :attribute field is required.', // :attribute
    'same'    => 'The :attribute and :other must match.', // :other
    'size'    => 'The :attribute must be exactly :size.', // size
    'between' => 'The :attribute value :input is not between :min - :max.', // :input,:min,:max
    'in'      => 'The :attribute must be one of the following types: :values', // :values
];
$validator = Validator::make($input, $rules, $messages);

# Implict (not present or empty)
$validator = Validator::make($input, $rules, $messages)->passes();

# (further validation) & (add more error messages)
$v->after(function ($validator) {
    if ($this->somethingElseIsInvalid()) {
        $validator->errors()->add('field', 'Something is wrong with this field!');
    }
});

# extend validation
$v->sometimes(['field1', 'field2'], $rules, $Closure = true);
$v->sometimes('field', 'required|max:500', function ($input) {
    return $input->games >= 100; // custom validation
});

# Retrive all errors
$errors = $validator->errors();

# Retrive first error
$errors->first('email');

# Retrieving All Error Messages
foreach ($errors->get('email') as $message) { }
foreach ($errors->get('attachments.*') as $message) { }
foreach ($errors->all() as $message) { }

# check
if ($errors->has('email')) { }
```

## make:request validation
```php
# Retrieve the validated input data from make:request class
$validated = $request->validated();
```

## Controller validator
```php
$v = Validator::make($request->all(), [
    'body' => 'required',
]);

if ($v->fails()) {
    return redirect('post/create')->withErrors($validator)->withInput();
}

if($v->passes()) { }

// (or)

Validator::make($request->all(), [
    'body' => 'required',
])->validate();
```

## Named error bag
```php
# store any error messages within a named error bag
$validatedData = $request->validateWithBag('post', [
    'body' => ['required'],
]);

Validator::make($request->all(), [
    'body' => 'required',
])->validateWithBag('post');

// attach named error bag
return redirect('register')->withErrors($validator, 'login');
```

## [Rule](https://laravel.com/docs/7.x/validation#available-validation-rules)
```php
$var = [
	'bail', // 'Stopping On First Validation Failure'
	'required',
	'nullabe', // 'optional'
	'sometimes', // validate only when present
	'present', // must exist - but empty also allowed
	'filled', // must exist & must data
	'confirmed', // password match

	# array &
	'array',
    'min:1', 'max:5',
	'size:5', // chk array length
	'distinct', // check array has no duplicate values
	'person.email' => 'required', // access array by dot notation
	'person.*.email' => 'required', // access array by dot notation

	# json
	'json',

	# boolean
	'boolean', // true, false, 1, 0, "1", and "0"

	# string
	'string',
	'starts_with:foo,bar,...',
	'ends_with:foo,bar,...', // must ends with any one of this
	'in:foo,bar,...', // value must be present any one of this
	Rule::in(['first', 'second']),
	'not_in:foo,bar,...',
	Rule::notIn(['first', 'second']),
	'size:12', // string length

	# alphabetic & numeric
	'alpha', // [a-z]
	'alpha_num', // [a-z] , [0,1-9]
	'alpha_dash', // [a-z] , [0,1-9] , - , _

	# integer (1,2,..) & numeric (12.11)
	'numeric',
	'integer',
	'min:value',
	'max:value',
	'between:min,max', // Strings, numerics, arrays, and files
	'digits:value', // digit length
	'digits_between:min,max', // digit length between
	'size:10', // digits

	# Url & IP
	'url', // chk valid url
	'active_url',
	'ip',
	'timezone',

	# date
	'date_format:format',
	'date',
	'date_equals:date',
	'before:date',
	'before:-10 years',
	'before_or_equal:date',
	'after:tomorrow',
	'after_or_equal:date',

	# file (image)
	'file', // must be a successfully uploaded file
	'image', // image (jpeg, png, bmp, gif, svg, or webp)
	'dimensions:min_width=100,max_width:200,min_height=200,max_height:400,width:500,height:500,ratio:3/2,ratio:1.5',
	Rule::dimensions()->minWidth(100)->maxWidth(200)->minHeight(300)->maxHeight(500)->width(500)->height(500)->ratio(3 / 2)->ratio(1.5),
	'mimetypes:video/avi,video/mpeg,video/quicktime',
	'mimes:jpeg,bmp,png,pdf',
	'size:512',

	# Regular expression
	'regex:pattern', // check matching
	'not_regex:pattern', // check not matching
]
```

## Intract with another field Rule
```php
'different:field', // check field must changed
'same:field', // check field must changed

# exclude if & unless (skipping validation)
'exclude_if:anotherfield,true',
'exclude_unless:anotherfield,true',

# required if & unless
'required_if:anotherfield,"==",value',
'required_if:anotherfield.*', // chk another field present
Rule::requiredIf(true), // field will validate only true
Rule::requiredIf(function () use ($request) { return true; }),
'required_unless:anotherfield,value',

# required with & with_all & without & without_all
'required_with:foo,bar,...', // present & not empty -only- if any other fields are  present
'required_with_all:foo,bar,...',
'required_without:foo,bar,...',
'required_without_all:foo,bar,...',

'lt:field', // less than
'lte:field', // less than equal
'gt:field', // greater than
'gte:field', // greater than equal
'in_array:anotherfield.*', // $request['anotherfield'] = ['10', '11']; -> check custom array
```

## Eloquent Rule
```php
$var = [
	# unique
	'field' => 'unique:table',
	'field' => 'unique:table,column,ignoreId,id',
	'field' => 'unique:App\Model,column,ignoreId,id',
	'field' => 'unique:table,column,ignoreId,id,deleted_at,NULL', // with condition
	'field' => Rule::unique('table')->ignore($user->id),
	'field' => Rule::unique('table', 'column')->ignore($user),
	'field' => Rule::unique('table')->where(function ($q) { return $q->where('column', 'val'); }),

	# exists - check column exist in the table
	'field' => 'exists:table',
	'field' => 'exists:table,column', // using custom column
	'field' => 'exists:App\Model,column', //  using model
	'field' => 'exists:table,column,deleted_at,NULL,city_id,1', // with condition
	'field' => Rule::exists('table')->where(function ($query) { $query->where('column', 'val'); }),
	'field' => Rule::exists('table', 'customColumnName'),
];
```

## Column with Rule
```php
'name'   => 'required|string|max:255|unique:users',
'email'  => 'email',

'mobile' => 'required|numeric|digits:10',
'mobile' => 'required|numeric|between:9,11',
'mobile' => 'required|min:10|numeric',
'mobile' => 'required|regex:/(01)[0-9]{9}/',
'mobile' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',

'title'              => 'required|max:255|unique:posts,title,'.$id,
'body'               => ['required'],
'description'        => 'nullable|string|max:255',

'role' 				 => 'required|exists:roles,id',

'price'              => 'numeric',
'active'             => 'integer|filled',
'role_id' 			 => Rule::requiredIf(!$request->user()->is_admin), // role id is required when the user is not a admin

'terms_of_service'   => 'accepted', // yes, on, 1, or true
'password'           => 'string|min:6|confirmed',

'credit_card_number' => 'required_if:payment_type,cc'

# Date
'publish_at'         => 'date',
'start_date'         => 'required|date|after:tomorrow'
'finish_date'        => 'required|date|after:start_date' // use column start_date

# File
'photo' => 'mimes:jpeg,bmp,png',
'video' => 'mimetypes:video/avi,video/mpeg,video/quicktime',
```

## resources/views/en/validation.php
```php
# Custom message
'custom' => [
    'email' => [
        'required' => 'We need to know your e-mail address!',
    ],
    'person.*.email' => [
        'unique' => 'Each person must have a unique e-mail address',
    ]
],

# Custom field
'attributes' => [
    'email' => 'email address',
],

# custom value
'values' => [
    'payment_type' => [
        'cc' => 'credit card'
    ],
],
```

## Back
```php
return back()->withErrors(['name.required', 'Name is required'])->withInput();
return redirect()->back()->withInput(Input::all())->with('message', 'wrong!');
```
