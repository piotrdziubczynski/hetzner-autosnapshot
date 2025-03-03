<?php

/**
 * Company: White Rock Development
 * Author: Piotr Dziubczynski, Software Engineer
 * Website: https://wrd.com.pl/
 * E-mail: support@wrd.com.pl
 */

declare(strict_types=1);

namespace WRD\Hetzner\AutoSnapshot\Core\Model\Hetzner\Response\Image;

use WRD\Hetzner\AutoSnapshot\Core\Model\Hetzner\Response\Parts\ActionModel;

final class DeleteImageModel
{
    public ?ActionModel $action = null;
}
