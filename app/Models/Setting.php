<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Setting extends Model
{
    protected $fillable = [
        'agency_id',
        'key',
        'value',
        'type',
        'description',
        'group',
    ];

    // Relations
    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    // Helper methods
    public static function get($key, $default = null, $agencyId = null)
    {
        // D'abord chercher un setting spécifique à l'agence
        if ($agencyId) {
            $setting = static::where('key', $key)
                ->where('agency_id', $agencyId)
                ->first();
            
            if ($setting) {
                return static::castValue($setting->value, $setting->type);
            }
        }
        
        // Sinon chercher un setting global
        $setting = static::where('key', $key)
            ->whereNull('agency_id')
            ->first();

        if (!$setting) {
            return $default;
        }

        return static::castValue($setting->value, $setting->type);
    }

    public static function set($key, $value, $agencyId = null, $type = 'string', $group = 'general')
    {
        return static::updateOrCreate(
            [
                'key' => $key,
                'agency_id' => $agencyId,
            ],
            [
                'value' => is_array($value) ? json_encode($value) : $value,
                'type' => $type,
                'group' => $group,
            ]
        );
    }

    protected static function castValue($value, $type)
    {
        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            'float' => (float) $value,
            'json' => json_decode($value, true),
            default => $value,
        };
    }
}
