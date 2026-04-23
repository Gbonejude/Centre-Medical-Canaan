<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Specialty extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'is_active'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($specialty) {
            if (!$specialty->slug) {
                $specialty->slug = Str::slug($specialty->name);
            }
        });
    }

    public function doctors()
    {
        return $this->hasMany(Doctor::class);
    }
}
