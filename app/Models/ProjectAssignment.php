<?php

namespace App\Models;

use Database\Factories\ProjectAssignmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectAssignment extends Model
{
    /** @use HasFactory<ProjectAssignmentFactory> */
    use HasFactory;

    protected $fillable = [
        'projek_id',
        'user_id',
        'role_in_project',
        'assigned_at',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'date',
        ];
    }

    public function projek(): BelongsTo
    {
        return $this->belongsTo(Projek::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
