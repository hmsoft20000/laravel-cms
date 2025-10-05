<?php

namespace HMsoft\Cms\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class DynamicValueCast implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes)
    {
        $type = $attributes['attribute_type'] ?? 'string';

        return match ($type) {
            // 'checkbox' => json_decode($value, true),
            'checkbox' => $value,
            'number' => (int) $value,
            'boolean' => (bool) $value,
            'radio' => (string) $value,
            'select' => (string) $value,
            'textarea' => (string) $value,
            'text' => (string) $value,
            'date' => \Carbon\Carbon::parse($value),
            default => $value,
        };
    }

    public function set($model, string $key, $value, array $attributes)
    {
        $type = $attributes['attribute_type'] ?? 'string';

        return match ($type) {
            // 'checkbox' => json_encode($value),
            'checkbox' => $value,
            'number' => (int) $value,
            'boolean' => (bool) $value,
            'radio' => (string) $value,
            'select' => (string) $value,
            'textarea' => (string) $value,
            'text' => (string) $value,
            'date' => $value instanceof \DateTimeInterface ? $value->format('Y-m-d') : $value,
            default => $value,
        };
    }
}
