<?php

use App\Support\TimeSupport;
use App\Services\ModuleService;
use App\Settings\AppointmentSettings;
use App\Settings\CalendarSettings;
use App\Settings\CompanySettings;
use App\Settings\FrontendSetting;
use App\Settings\GeneralSettings;
use App\Settings\IntegrationSettings;
use App\Settings\InvoiceSettings;
use App\Settings\NotificationSetting;
use App\Settings\TimeSettings;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Number;

if (! function_exists('getDateFormat')) {
    function getDateFormat(): string
    {
        return app(GeneralSettings::class)->date_format;
    }
}

if (! function_exists('getTimeFormat')) {
    function getTimeFormat(): string
    {
        return app(GeneralSettings::class)->time_format;
    }
}

if (! function_exists('getDateTimeFormat')) {
    function getDateTimeFormat(): string
    {
        $general = app(GeneralSettings::class);

        return sprintf('%s %s', $general->date_format, $general->time_format);
    }
}

if (! function_exists('formatDate')) {
    function formatDate(Carbon|string|null $date): string
    {
        if(is_null($date)) {
            return '';
        }
        return Carbon::parse($date)->format(getDateFormat());
    }
}

if (! function_exists('formatTime')) {
    function formatTime(?int $minutes, string $format = '%H:%I'): string
    {
        if(is_null($minutes)) {
            return '';
        }

        return TimeSupport::formatTime($minutes, $format);
    }
}

if (! function_exists('deformatTime')) {
    function deformatTime($time): string
    {
        if(is_null($time)) {
            return '';
        }
        return TimeSupport::deformatTime($time);
    }
}

if (! function_exists('formatDateTime')) {
    function formatDateTime(Carbon|string|null $date): string
    {
        if(is_null($date)) {
            return '';
        }
        return Carbon::parse($date)->format(getDateTimeFormat());
    }
}

if (! function_exists('formatMoney')) {
    function formatMoney(null|string|int|float $amount): string
    {
        if (is_string($amount)) {
            $amount = floatval($amount);
        }

        return Number::currency($amount ?? 0.0, 'Eur', 'de');
    }
}

if (! function_exists('module')) {
    function module(): ModuleService
    {
        return app(ModuleService::class);
    }
}

if (! function_exists('frontend')) {
    function frontend(): FrontendSetting
    {
        return once(fn () => app(FrontendSetting::class));
    }
}

if (! function_exists('calendar')) {
    function calendar(): CalendarSettings
    {
        return once(fn () => app(CalendarSettings::class));
    }
}

if (! function_exists('company')) {
    function company(): CompanySettings
    {
        return once(fn () => app(CompanySettings::class));
    }
}

if (! function_exists('getCompanyImageAsBase64')) {
    function getCompanyImageAsBase64()
    {
        return Cache::rememberForever('company_setting_base64_image', function () {
            $logo = app(CompanySettings::class)->logo_path;
            if (! $logo) {
                return null;
            }
            try {
                return 'data:image/png;base64,'.base64_encode(Storage::disk('public')->get($logo));
            } catch (Exception $e) {
                return null;
            }
        });
    }
}

if (! function_exists('general')) {
    function general(): GeneralSettings
    {
        return once(fn () => app(GeneralSettings::class));
    }
}

if (! function_exists('timeSettings')) {
    function timeSettings(): TimeSettings
    {
        return once(fn () => app(TimeSettings::class));
    }
}

if (! function_exists('appointment')) {
    function appointment(): AppointmentSettings
    {
        return once(fn () => app(AppointmentSettings::class));
    }
}

if (! function_exists('notification')) {
    function notification(): NotificationSetting
    {
        return once(fn () => app(NotificationSetting::class));
    }
}

if (! function_exists('invoice')) {
    function invoice(): InvoiceSettings
    {
        return once(fn () => app(InvoiceSettings::class));
    }
}

if (! function_exists('integration')) {
    function integration(): IntegrationSettings
    {
        return once(fn () => app(IntegrationSettings::class));
    }
}

if (! function_exists('getColorValue')) {
    function getColorValue(string|array|null $color, int $shade): ?string
    {
        if (is_string($color)) {
            return "$color";
        }

        if (is_array($color)) {
            return "$color[$shade]";
        }

        return null;
    }
}
