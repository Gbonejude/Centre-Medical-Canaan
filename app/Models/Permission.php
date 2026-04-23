<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'guard_name',
        'is_customer',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_customer' => 'boolean',
    ];

    public function scopeCustomerOnly($query)
    {
        return $query->where('is_customer', true);
    }

    public function scopeNonCustomer($query)
    {
        return $query->where('is_customer', false);
    }
}
