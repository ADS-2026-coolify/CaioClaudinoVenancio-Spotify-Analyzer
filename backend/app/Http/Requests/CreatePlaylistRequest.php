<?php

namespace App\Http\Requests;

use App\Enums\MoodCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreatePlaylistRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:300'],
            'is_public' => ['boolean'],
            'mood' => ['required', 'string', Rule::enum(MoodCategory::class)],
            'track_uris' => ['required', 'array', 'min:1', 'max:100'],
            'track_uris.*' => ['required', 'string', 'regex:/^spotify:track:[a-zA-Z0-9]+$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome da playlist é obrigatório.',
            'name.max' => 'O nome da playlist não pode exceder 100 caracteres.',
            'description.max' => 'A descrição não pode exceder 300 caracteres.',
            'mood.required' => 'O clima da playlist é obrigatório.',
            'mood.enum' => 'O clima selecionado é inválido.',
            'track_uris.required' => 'Ao menos uma faixa deve ser selecionada.',
            'track_uris.min' => 'Ao menos uma faixa deve ser selecionada.',
            'track_uris.*.regex' => 'URI de faixa Spotify inválida.',
        ];
    }
}

