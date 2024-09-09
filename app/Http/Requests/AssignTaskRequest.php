<?php

namespace App\Http\Requests;

use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
class AssignTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Check if the user is logged in
    if (!Auth::check()) {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'message' => 'unAuthorized',
        ], 401));
    }

    // Get the currently authenticated user
    $user = Auth::user();

    // Check if the user is a manager
    if ($user->role !== 'Manager' && $user->role !=='Admin') {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'message' => 'unAuthorized',
        ], 403));
    }

    // The user is logged in and is a manager, so authorize the request
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
            'assigned_to' => 'required|exists:users,id'
        ];
    }

    /**
     * Get the custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'assigned_to.required' => 'The assigned user field is required.',
            'assigned_to.exists' => 'The selected user does not exist.',
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
            'status' => false,
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422));
    }
}
