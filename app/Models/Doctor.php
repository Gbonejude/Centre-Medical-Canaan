<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Uuid;

class Doctor extends Model
{
    use HasFactory, SoftDeletes, Uuid;

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    protected $fillable = [
        'user_id',
        'medical_service_id',
        'specialty_id',
        'bio',
        'availability',
        'is_available',
    ];

    protected $casts = [
        'availability' => 'json',
        'is_available' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function specialty()
    {
        return $this->belongsTo(Specialty::class);
    }

    public function specialties()
    {
        return $this->belongsToMany(Specialty::class, 'doctor_specialty');
    }

    public function medicalService()
    {
        return $this->belongsTo(MedicalService::class, 'medical_service_id');
    }


    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'doctor_id', 'user_id');
    }
}
