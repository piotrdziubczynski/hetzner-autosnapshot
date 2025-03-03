<?php

/**
 * Company: White Rock Development
 * Author: Piotr Dziubczynski, Software Engineer
 * Website: https://wrd.com.pl/
 * E-mail: support@wrd.com.pl
 */

declare(strict_types=1);

namespace WRD\Hetzner\AutoSnapshot\Core\Enum;

enum Frequency: string
{
    case DAILY = 'daily'; # Every day
    case WEEKLY = 'weekly'; # Every week
    case BIWEEKLY = 'biweekly'; # Every 2 weeks
    case MONTHLY = 'monthly'; # Every month
    case QUARTERLY = 'quarterly'; # Every 3 months
    case TERTIARY = 'tertiary'; # Every 4 months
    case SEMIANNUALLY = 'semiannually'; # Every 6 months
    case ANNUALLY = 'annually'; # Every year

    public function period(): string
    {
        return match ($this) {
            self::DAILY => '24 hours',
            self::WEEKLY => '7 days',
            self::BIWEEKLY => '14 days',
            self::MONTHLY => '1 month',
            self::QUARTERLY => '3 months',
            self::TERTIARY => '4 months',
            self::SEMIANNUALLY => '6 months',
            self::ANNUALLY => '1 year',
        };
    }
}
