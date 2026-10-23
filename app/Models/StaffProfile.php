<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StaffProfile extends Model
{
    use SoftDeletes;

    protected $table = 'staff_profiles';

    protected $fillable = [
        'uuid',
        'user_id',
        'department_id',
        'employee_number',
        'job_title',
        'employment_type',
        'hire_date',
        'termination_date',
        'salary',
        'emergency_contact_name',
        'emergency_contact_phone',
        'notes',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'termination_date' => 'date',
        'salary' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function skills()
    {
        return $this->belongsToMany(
            StaffSkill::class,
            'staff_skill_assignments',
            'staff_id',
            'skill_id'
        );
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class, 'staff_id');
    }

    public function assignedEvents()
    {
        return $this->belongsToMany(
            BookingEvent::class,
            'booking_event_staff',
            'staff_id',
            'booking_event_id'
        );
    }
}
