<?php

namespace App\Http\Requests;

use App\Package;
use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;

// php artisan make:request StoreBlogPost
class PackageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        # get comment id from -> Route::post('comment/{comment}');
        $comment = Comment::find($this->route('comment'));
        return $comment && $this->user()->can('update', $comment);

        // or

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request)
    {
        $city = $this->get('city_id');
        $employeeId = $this->request->get('employee_id');
        $types = implode(',', Cab::pluck('type')->toArray());
        $id = $this->route('bookingId');

        if ($this->method() == "POST")
        {
            return [
                'package_name' => 'required|string|max:255|unique:packages',
                'type' => 'required|'. 
                    (
                        ($this->method() == "POST") 
                            ? 'unique:cab_types' 
                            : 'unique:cab_types,cab_type,' . array_values($this->route()->parameters())[0] . ',id'
                    ),
            ];
        }
        else // PATCH
        {
            $rule = [
                'package_name' => 'required|string|max:255|unique:packages,package_name,'.$request->id,
            ];

            if ($request->filled('password')) { $rule['password'] = 'required|string|min:6|confirmed'; }

            return $rule;
        }

        $rules = [];
        return array_merger($rules, []);
    }

    /**
     * Custom message for validation
     *
     * @return array
     */
    public function messages()
    {
        $cabNumber = $this->request->get('cab_number');

        return [
            'title.required' => 'A title is required',
            'city_id.in' => 'Selected City is Invalid',
            'booking_id.exists' => 'Selected Booking Id is Invalid',
            'after_or_equal' => 'The :attribute must be greater than or equal to purchase date.',
            'before_or_equal' => 'The :attribute must be less than or equal to today.',
            'field.*.id.exists' => 'Some.',
            'address.max'  => 'Address May Not be Greater Than 255 Characters',
            'lat.numeric'  => 'Lat Must be a Number',
            'password.confirmed'  => 'Password Confirmation does not Match',
            'pick_up_suburb_id.required_if' => 'Pick Up Suburb is required',
            'type.*.distinct' => 'The type field has a duplicate value.',
            'type.*.min' => 'The type must be at least :min.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'phone' => 'mobile',
            'drop_date_time' => 'expected drop date & time',
            'code.*' => 'code',
        ];
        // or
        $attributes = parent::attributes();
        $attributes['owner_id'] = 'owner';
        return $attributes;
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'slug' => Str::slug($this->slug),
        ]);
    }

    /**
     * Get all of the input and files for the request.
     *
     * @return array
     */
    public function all()
    {
        $data = parent::all();
        $data['phone'] = formatMobileNumber($data['phone']);
        $data['package'] = PACKAGE_TYPE_SLUG_MAP[$data['package_type_slug']];
        $this->cityId = $data['city_id'];
        unset($data['rental_fare_id'], $data['outstation_package_slot_id']);
        unset($data['outstation_city'][0]);
        return $data;

        // or

        $this->merge([ 'new_field' => 'val' ]);
        return parent::all();
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->somethingElseIsInvalid()) {
                $validator->errors()->add('field', 'Something is wrong with this field!');
            }
        });
    }

    /**
     * Get the proper failed validation response for the request.
     *
     * @param  array $errors
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function response(array $errors)
    {
        return response()->json(['error' => $errors['cab_number']], 422);
        
        // or
        
        if ($this->expectsJson()) {
            return new JsonResponse(['message' => 'validation failed', 'error' => $errors], 422);
        }

        return parent::response($errors);
    }
}
