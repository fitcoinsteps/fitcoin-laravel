<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UpdateWithdrawalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var User|null $user */
        $user = $this->user();  // ✅ Use $this->user() instead of auth()->user()
        
        if (!$user) {
            return false;
        }
        
        // Check if user has admin role
        $roles = $user->roles->pluck('slug')->toArray();
        return in_array('super-admin', $roles) || in_array('admin', $roles);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'status' => 'sometimes|in:pending,processing,completed,failed',
            'transaction_hash' => 'nullable|string|max:255',
            'admin_notes' => 'nullable|string|max:1000',
            'reason' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'status.in' => 'Invalid status selected.',
            'admin_notes.max' => 'Admin notes cannot exceed 1000 characters.',
            'reason.max' => 'Reason cannot exceed 500 characters.',
        ];
    }

    /**
     * Get the validation rules for processing a withdrawal.
     */
    public static function processRules(): array
    {
        return [
            'transaction_hash' => 'nullable|string|max:255',
            'admin_notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get the validation rules for failing a withdrawal.
     */
    public static function failRules(): array
    {
        return [
            'reason' => 'required|string|max:500',
            'admin_notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Trim transaction hash
        if ($this->has('transaction_hash')) {
            $this->merge([
                'transaction_hash' => trim($this->transaction_hash),
            ]);
        }

        // Trim admin notes
        if ($this->has('admin_notes')) {
            $this->merge([
                'admin_notes' => trim($this->admin_notes),
            ]);
        }

        // Trim reason
        if ($this->has('reason')) {
            $this->merge([
                'reason' => trim($this->reason),
            ]);
        }
    }
}