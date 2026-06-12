<?php

namespace App\Enum;

enum SubscriptionEnum
{
    public static function allowedSubscriptionHosts(): array
    {
        return [
            'www.olx.ua',
        ];
    }

    public static function allowedSubscriptionHttpSchemas(): array
    {
        return [
            //            'http',
            'https',
        ];
    }
}
