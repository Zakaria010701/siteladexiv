<?php

namespace App\Support;

use Carbon\CarbonInterval;

class TimeSupport
{
    public static function formatTime($minutes, string $format = '%H:%I'): string
    {
        if (is_null($minutes) || $minutes === 0 || $minutes == '--:--') {
            return '--:--';
        }
        CarbonInterval::setCascadeFactors([
            'minute' => [60, 'seconds'],
            'hour' => [60, 'minutes'],
        ]);
        if ($minutes < 0) {
            return '-'.CarbonInterval::minutes($minutes)->cascade()->format($format);
        }

        return CarbonInterval::minutes($minutes)->cascade()->format($format);
    }

    public static function deformatTime($time)
    {
        if (preg_match('/^\d+:\d+$/', $time)) {
            return intval(CarbonInterval::createFromFormat('H:i', str_replace('-', '0', $time))->totalMinutes);
        }
        if (preg_match('/^-\d+:\d+$/', $time)) {
            return intval(-CarbonInterval::createFromFormat('H:i', str_replace('-', '', $time))->totalMinutes);
        }
        if (preg_match('/^-\d+$/', $time)) {
            return intval(-CarbonInterval::createFromFormat('H', str_replace('-', '', $time))->totalMinutes);
        }
        if (preg_match('/^\d+$/', $time)) {
            return intval(CarbonInterval::createFromFormat('H', str_replace('-', '0', $time))->totalMinutes);
        }
        return 0;
    }
}
