<?php

namespace App\Models;

use Database\Factories\LogbookAttachmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogbookAttachment extends Model
{
    /** @use HasFactory<LogbookAttachmentFactory> */
    use HasFactory;

    protected $fillable = [
        'logbook_id',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
        ];
    }

    public function logbook(): BelongsTo
    {
        return $this->belongsTo(Logbook::class);
    }
}
