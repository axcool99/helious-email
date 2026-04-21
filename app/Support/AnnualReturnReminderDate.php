<?php

namespace App\Support;

use Illuminate\Support\Carbon;

class AnnualReturnReminderDate
{
    public static function nextReminderDate(Carbon $incorporationDate, ?Carbon $referenceDate = null): Carbon
    {
        $referenceDate ??= now()->startOfDay();

        $candidate = static::reminderDateForYear($incorporationDate, $referenceDate->year);

        if ($candidate->lt($referenceDate->copy()->startOfDay())) {
            $candidate = static::reminderDateForYear($incorporationDate, $referenceDate->year + 1);
        }

        return $candidate->startOfDay();
    }

    public static function reminderDateForYear(Carbon $incorporationDate, int $year): Carbon
    {
        $anniversary = Carbon::create(
            $year,
            $incorporationDate->month,
            min($incorporationDate->day, Carbon::create($year, $incorporationDate->month, 1)->daysInMonth),
            0,
            0,
            0,
            config('app.timezone')
        );

        return $anniversary->subDays(30)->startOfDay();
    }
}
