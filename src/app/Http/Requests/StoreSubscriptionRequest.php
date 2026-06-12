<?php

namespace App\Http\Requests;

use App\Enum\SubscriptionEnum;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSubscriptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'url' => [
                'required',
                'url',
                'max:255',
                function (string $attribute, mixed $value, Closure $fail): void {
                    $parsed = parse_url($value);

                    if (! in_array($parsed['scheme'], SubscriptionEnum::allowedSubscriptionHttpSchemas(), true)
                        || ! in_array($parsed['host'], SubscriptionEnum::allowedSubscriptionHosts(), true)) {
                        $fail('The :attribute must be a valid and allowed subscription URL.');
                    }
                },
            ],
            'email' => ['required', 'email', 'max:255'],
        ];
    }
}
