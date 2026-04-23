<?php

namespace App\Models;

use App\Enums\Gender;
use App\Uuid;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasMedia, MustVerifyEmail
{
    use HasFactory, HasPermissions, HasRoles, InteractsWithMedia, Notifiable, Uuid;

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    protected $appends = ['name'];

    protected $fillable = [
        'firstname',
        'lastname',
        'gender',
        'active',
        'phone',
        'email',
        'password',
        'birthday',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'gender' => Gender::class,
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function name(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->firstname.' '.$this->lastname
        );
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('users');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('thumb')
            ->fit(Fit::Contain, 80, 80)
            ->nonQueued();
    }

    /**
     * Un utilisateur peut être un Médecin (profil lié)
     */
    public function doctor(): HasOne
    {
        return $this->hasOne(Doctor::class);
    }

    /**
     * Un utilisateur peut être un Patient (profil lié)
     */
    public function patient(): HasOne
    {
        return $this->hasOne(Patient::class);
    }

    /**
     * Scope: Exclure les utilisateurs système (super admins)
     */
    public function scopeExcludeSystemAdmins($query)
    {
        return $query->whereNotIn('email', self::getSystemAdminEmails());
    }

    /**
     * Get the list of system admin emails
     */
    public static function getSystemAdminEmails(): array
    {
        return [
            'ahadjimathieu@gmail.com',
            'judasgbone@gmail.com',
            'admin@ccs.com',
        ];
    }

    /**
     * Check if user is a system admin
     */
    public function isSystemAdmin(): bool
    {
        return in_array($this->email, self::getSystemAdminEmails());
    }
}
