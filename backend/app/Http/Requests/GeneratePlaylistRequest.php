<?php

namespace App\Http\Requests;

use App\Enums\MoodCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GeneratePlaylistRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'mood' => ['required', 'string', Rule::enum(MoodCategory::class)],
            'track_count' => ['nullable', 'integer', 'min:10', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'mood.required' => 'O clima da playlist é obrigatório.',
            'mood.enum' => 'O clima selecionado é inválido.',
            'track_count.min' => 'A playlist deve ter no mínimo 10 faixas.',
            'track_count.max' => 'A playlist deve ter no máximo 50 faixas.',
        ];
    }
}

