<?php

declare(strict_types=1);

namespace Laraforge\DatabaseStudio\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ManageIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'index_name' => ['required', 'string', 'regex:/^[a-zA-Z0-9_]+$/', 'max:64'],
            'index_type' => ['required', 'string', 'in:PRIMARY,UNIQUE,INDEX,FULLTEXT'],
            'columns'    => ['required', 'array', 'min:1'],
            'columns.*'  => ['required', 'string', 'regex:/^[a-zA-Z0-9_]+$/'],
        ];
    }
}
