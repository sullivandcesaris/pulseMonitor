<?php

namespace App\Http\Requests;

use App\Models\Monitor;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MonitorRequest extends FormRequest
{
    public function authorize(): bool
    {
        $monitor = $this->route('monitor');

        return $monitor
            ? $this->user()->can('update', $monitor)
            : $this->user()->can('create', Monitor::class);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'target_url' => ['required', 'url:http,https', 'max:2048'],
            'method' => ['required', Rule::in(['GET', 'HEAD'])],
            'interval_seconds' => ['required', 'integer', 'min:10', 'max:86400'],
            'expected_status' => ['required', 'integer', 'between:100,599'],
            'timeout_seconds' => ['required', 'integer', 'min:1', 'max:120'],
            'is_enabled' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_enabled' => $this->boolean('is_enabled')]);
    }
}
