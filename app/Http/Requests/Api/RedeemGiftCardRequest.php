<?php

namespace App\Http\Requests\Api;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class RedeemGiftCardRequest extends FormRequest
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
            'gift_card_id' => 'required|exists:gift_cards,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'gift_card_id.required' => 'Please select a gift card.',
            'gift_card_id.exists' => 'The selected gift card is not available.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Ensure gift_card_id is an integer
        if ($this->has('gift_card_id')) {
            $this->merge([
                'gift_card_id' => (int) $this->gift_card_id,
            ]);
        }
    }
}