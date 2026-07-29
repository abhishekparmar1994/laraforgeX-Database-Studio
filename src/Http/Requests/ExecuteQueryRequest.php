<?php

declare(strict_types=1);

namespace Laraforge\DatabaseStudio\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExecuteQueryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sql' => ['required', 'string', 'min:3'],
        ];
    }
}
