<?php

namespace App\Support;

class FeatureFlags
{
    public const MULTI_CURRENCY = 'multi_currency';
    public const DARK_THEME = 'dark_theme';
    public const AI_MODERATION = 'ai_moderation';
    public const EXPORT_PDF = 'export_pdf';
    public const SMS_NOTIFICATIONS = 'sms_notifications';
    public const GEO_SEARCH = 'geo_search';
    public const PREMIUM_ADS = 'premium_ads';
    public const REALTIME_CHAT = 'realtime_chat';

    public static function enabled(string $flag): bool
    {
        return false;
    }
}
