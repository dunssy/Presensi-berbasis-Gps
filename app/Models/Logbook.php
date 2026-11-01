<?php

namespace App\Models;

use Database\Factories\LogbookFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Logbook extends Model
{
    /** @use HasFactory<LogbookFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'projek_id',
        'date',
        'title',
        'description',
        'progress_percentage',
        'status',
        'review_notes',
        'reviewed_by',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'progress_percentage' => 'integer',
            'reviewed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function projek(): BelongsTo
    {
        return $this->belongsTo(Projek::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(LogbookAttachment::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(LogbookHistory::class);
    }
}
