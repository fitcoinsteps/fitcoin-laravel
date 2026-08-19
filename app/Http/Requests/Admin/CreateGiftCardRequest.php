<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class CreateGiftCardRequest extends FormRequest
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
            'provider' => 'required|in:amazon,google_play,steam,apple',
            'code' => 'required|string|max:255|unique:gift_cards,code',
            'pin' => 'nullable|string|max:50',
            'value' => 'required|numeric|min:0.01|max:10000',
            'currency' => 'required|string|size:3',
            'fitcoin_cost' => 'required|integer|min:1|max:1000000',
            'sku' => 'nullable|string|max:255',
            'expires_at' => 'nullable|date|after:today',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'provider.required' => 'Please select a gift card provider.',
            'provider.in' => 'Invalid gift card provider selected.',
            'code.required' => 'Gift card code is required.',
            'code.unique' => 'This gift card code already exists in the system.',
            'value.required' => 'Gift card value is required.',
            'value.min' => 'Gift card value must be at least 0.01.',
            'fitcoin_cost.required' => 'FIT coin cost is required.',
            'fitcoin_cost.min' => 'FIT coin cost must be at least 1.',
            'expires_at.after' => 'Expiry date must be in the future.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Trim the code
        if ($this->has('code')) {
            $this->merge([
                'code' => trim($this->code),
            ]);
        }

        // Convert currency to uppercase
        if ($this->has('currency')) {
            $this->merge([
                'currency' => strtoupper($this->currency),
            ]);
        }
    }
}