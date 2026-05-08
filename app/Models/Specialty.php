<?php

namespace App\Models;

use App\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Specialty extends Model
{
    use HasFactory, Uuid;

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    protected $fillable = ['name', 'slug', 'description', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($specialty) {
            if (! $specialty->slug) {
                $specialty->slug = Str::slug($specialty->name);
            }
        });
    }

    public function doctors()
    {
        return $this->hasMany(Doctor::class);
    }
}
