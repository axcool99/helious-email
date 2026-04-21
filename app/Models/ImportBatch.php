<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ImportBatch extends Model
{
    protected $fillable = [
        'source_type',
        'original_filename',
        'imported_rows',
        'skipped_rows',
        'replaced_rows',
        'imported_by',
    ];

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'imported_by');
    }
}
