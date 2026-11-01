<?php

namespace App\Models;

use Database\Factories\LogbookHistoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogbookHistory extends Model
{
    /** @use HasFactory<LogbookHistoryFactory> */
    use HasFactory;

    protected $fillable = [
        'logbook_id',
        'user_id',
        'action',
        'note',
    ];

    public function logbook(): BelongsTo
    {
        return $this->belongsTo(Logbook::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
