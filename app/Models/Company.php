<?php

namespace App\Models;

use App\Support\AnnualReturnReminderDate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class Company extends Model
{
    protected $fillable = [
        'import_batch_id',
        'source_type',
        'company_name',
        'email',
        'email_hash',
        'incorporation_date',
        'reminder_month',
        'reminder_day',
        'next_reminder_on',
        'last_reminder_sent_on',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'email' => 'encrypted',
            'incorporation_date' => 'date',
            'last_reminder_sent_on' => 'date',
            'next_reminder_on' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Company $company): void {
            if ($company->email !== null) {
                $company->email_hash = hash('sha256', Str::lower(trim($company->email)));
            }

            if ($company->isDirty('incorporation_date') || ! $company->exists || $company->next_reminder_on === null) {
                $company->syncReminderSchedule();
            }
        });
    }

    public function importBatch(): BelongsTo
    {
        return $this->belongsTo(ImportBatch::class);
    }

    public function reminderLogs(): HasMany
    {
        return $this->hasMany(ReminderLog::class);
    }

    public function scopeSourceType(Builder $query, string $sourceType): Builder
    {
        return $query->where('source_type', $sourceType);
    }

    public function syncReminderSchedule(?Carbon $referenceDate = null): void
    {
        if (! $this->incorporation_date instanceof Carbon) {
            $this->incorporation_date = Carbon::parse($this->incorporation_date)->startOfDay();
        }

        $this->reminder_month = $this->incorporation_date->month;
        $this->reminder_day = $this->incorporation_date->day;
        $this->next_reminder_on = AnnualReturnReminderDate::nextReminderDate(
            $this->incorporation_date,
            $referenceDate,
        );
    }

    public function sourceLabel(): string
    {
        return config("company_sources.{$this->source_type}.label", strtoupper($this->source_type));
    }
}
