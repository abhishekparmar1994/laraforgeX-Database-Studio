<?php

declare(strict_types=1);

namespace Laraforge\DatabaseStudio\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'table_name'      => ['required', 'string', 'regex:/^[a-zA-Z0-9_]+$/', 'max:64'],
            'engine'          => ['nullable', 'string', 'in:InnoDB,MyISAM,MEMORY'],
            'collation'       => ['nullable', 'string', 'max:64'],
            'columns'         => ['required', 'array', 'min:1'],
            'columns.*.name'  => ['required', 'string', 'regex:/^[a-zA-Z0-9_]+$/', 'max:64'],
            'columns.*.type'  => ['required', 'string'],
            'columns.*.length' => ['nullable', 'string', 'max:64'],
            'columns.*.nullable' => ['nullable', 'boolean'],
            'columns.*.default' => ['nullable'],
            'columns.*.auto_increment' => ['nullable', 'boolean'],
            'columns.*.primary' => ['nullable', 'boolean'],
            'foreign_keys'    => ['nullable', 'array'],
            'indexes'         => ['nullable', 'array'],
        ];
    }
}
