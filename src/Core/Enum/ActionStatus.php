<?php

/**
 * Company: White Rock Development
 * Author: Piotr Dziubczynski, Software Engineer
 * Website: https://wrd.com.pl/
 * E-mail: support@wrd.com.pl
 */

declare(strict_types=1);

namespace WRD\Hetzner\AutoSnapshot\Core\Enum;

enum ActionStatus: string
{
    case RUNNING = 'running';
    case SUCCESS = 'success';
    case ERROR = 'error';

    public function isSuccess(): bool
    {
        return self::ERROR !== $this;
    }
}
