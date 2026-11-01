<?php

namespace App\Models;

use Database\Factories\ProjekFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Projek extends Model
{
    /** @use HasFactory<ProjekFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'client_name',
        'start_date',
        'end_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function attendancePoints(): HasMany
    {
        return $this->hasMany(AttendancePoint::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(ProjectAssignment::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_assignments')
            ->withPivot(['role_in_project', 'assigned_at'])
            ->withTimestamps();
    }

    public function logbooks(): HasMany
    {
        return $this->hasMany(Logbook::class);
    }
}
