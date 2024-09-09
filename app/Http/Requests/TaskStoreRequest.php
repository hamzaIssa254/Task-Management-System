<?php

namespace App\Http\Requests;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\UnauthorizedException;

class TaskStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
       if( !(Auth::check() && (Auth::user()->role == 'Manager' || Auth::user()->role == 'Admin') ))
       {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'message' => 'unAuthorized',

        ], 422));
       }
       return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:30',
            'description' => 'required|string|min:10|max:30',
            'priority' => 'required|in:low,medium,high',
            'due_date' => 'required|date',
            'status' => 'required|in:pending,in_progress,completed',
            'assigned_to' => 'required|exists:users,id',
        ];
    }



    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 422));
    }



    /**
     * Get the custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'The title field is required.',
            'title.min' => 'The title must be at least 10 characters long.',
            'title.max' => 'The title may not be more than 30 characters long.',
            'description.required' => 'The description field is required.',
            'description.min' => 'The description must be at least 10 characters long.',
            'description.max' => 'The description may not be more than 30 characters long.',
            'priority.required' => 'Please select a valid priority.',
            'priority.exists' => 'The selected priority is invalid.',
            'due_date.required' => 'A due date is required.',
            'due_date.date_format' => 'The due date format must be Y-m-d.',
            'status.required' => 'The status field is required.',
            'status.exists' => 'The selected status is invalid.',
        ];
    }


}
