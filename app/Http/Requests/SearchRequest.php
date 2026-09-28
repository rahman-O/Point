<?php

namespace App\Http\Requests;

use App\Search\SearchIndexer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'q' => ['required', 'string', 'max:100'],
            'type' => ['nullable', 'string', Rule::in(array_keys(SearchIndexer::MODELS))],
            'lang' => ['nullable', 'string', Rule::in(['en', 'ar'])],
            'page' => ['nullable', 'integer', 'min:1', 'max:50'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:30'],
        ];
    }
}
