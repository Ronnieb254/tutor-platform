<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClinicEnrollment extends Model
{
    protected $fillable = ['clinic_id','student_id','payment_id','session_code','attended'];

    protected function casts(): array
    {
        return ['attended' => 'boolean'];
    }

    public function clinic()  { return $this->belongsTo(ExaminerClinic::class); }
    public function student() { return $this->belongsTo(User::class, 'student_id'); }
    public function payment() { return $this->belongsTo(Payment::class); }
}
