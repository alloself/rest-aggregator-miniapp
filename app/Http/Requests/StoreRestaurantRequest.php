<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRestaurantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Авторизация проверяется в middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'telegram_bot_token' => ['required', 'string', 'regex:/^\d{8,10}:[A-Za-z0-9_-]{35}$/'],
            'settings' => ['nullable', 'array'],
            'settings.working_hours' => ['nullable', 'string'],
            'settings.delivery' => ['nullable', 'boolean'],
            'settings.min_order' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'created_by_admin_id' => ['required', 'uuid', 'exists:users,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Название ресторана обязательно для заполнения',
            'telegram_bot_token.required' => 'Токен Telegram бота обязателен',
            'telegram_bot_token.regex' => 'Неверный формат токена Telegram бота',
            'created_by_admin_id.required' => 'ID создавшего администратора обязательно',
            'created_by_admin_id.exists' => 'Указанный администратор не существует',
        ];
    }
}
