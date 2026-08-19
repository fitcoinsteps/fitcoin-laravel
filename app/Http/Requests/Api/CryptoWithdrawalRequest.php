<?php

namespace App\Http\Requests\Api;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class CryptoWithdrawalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var User|null $user */
        $user = $this->user(); // ✅ Use $this->user() instead of auth()->check()
        return $user !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'currency' => 'required|in:USDT,USDC,BTC',
            'crypto_amount' => 'required|numeric|min:0.0001',
            'wallet_address' => 'required|string|min:10',
            'network' => 'required|string',
            'fitcoins_per_unit' => 'required|integer|min:1',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'currency.required' => 'Please select a cryptocurrency.',
            'currency.in' => 'Invalid cryptocurrency selected.',
            'crypto_amount.required' => 'Please enter the amount.',
            'crypto_amount.min' => 'Minimum withdrawal amount is 0.0001.',
            'wallet_address.required' => 'Please enter your wallet address.',
            'wallet_address.min' => 'Wallet address must be at least 10 characters.',
            'network.required' => 'Please select a network.',
            'fitcoins_per_unit.required' => 'Invalid conversion rate.',
            'fitcoins_per_unit.min' => 'Conversion rate must be at least 1.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert currency to uppercase
        if ($this->has('currency')) {
            $this->merge([
                'currency' => strtoupper($this->currency),
            ]);
        }

        // Trim wallet address
        if ($this->has('wallet_address')) {
            $this->merge([
                'wallet_address' => trim($this->wallet_address),
            ]);
        }
    }
}