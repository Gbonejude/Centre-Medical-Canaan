<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Appointment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'reference',
        'patient_id',
        'medical_service_id',
        'doctor_id',
        'appointment_date',
        'appointment_time',
        'status',
        'reason',
        'receptionist_notes',
        'doctor_notes',
        'confirmed_at',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'confirmed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
            $model->reference = self::generateUniqueReference();
        });
    }

    private static function generateUniqueReference()
    {
        do {
            $reference = str_pad(mt_rand(1, 9999999), 7, '0', STR_PAD_LEFT);
        } while (self::where('reference', $reference)->exists());

        return $reference;
    }

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function medicalService()
    {
        return $this->belongsTo(MedicalService::class, 'medical_service_id');
    }
}
