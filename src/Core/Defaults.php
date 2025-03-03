<?php

/**
 * Company: White Rock Development
 * Author: Piotr Dziubczynski, Software Engineer
 * Website: https://wrd.com.pl/
 * E-mail: support@wrd.com.pl
 */

declare(strict_types=1);

namespace WRD\Hetzner\AutoSnapshot\Core;

use WRD\Hetzner\AutoSnapshot\Core\Enum\Frequency;

final class Defaults
{
    public const string API_BASE_URI_FORMAT = 'https://api.hetzner.cloud/%s/';
    public const string API_VERSION = 'v1';
    public const string CONTENT_TYPE = 'application/json';
    public const string DATETIME_FORMAT = 'Ymd';
    public const string FREQUENCY_BACKUP = Frequency::DAILY->value;
    public const string FREQUENCY_DELETING = Frequency::WEEKLY->value;
    public const int IMAGE_BACKUP_MINIMUM = 7;
    public const int IMAGE_DELETE_UNASSIGN = 1;
    public const string IMAGE_NAME_FORMAT = '%s__%s__%s';
    public const string IMAGE_NAME_FORMAT_DELIMITER = '__';
    public const string IMAGE_PREFIX = 'autosnapshot';
    public const int ITEMS_ON_PAGE = 100;
    public const string LOGGER_NAME = 'app';
    public const string LOGGER_PATH = __ROOT_DIR__ . '/var/log/app.log';
    public const string UNASSIGN_IMAGES = 'unassign';
}
