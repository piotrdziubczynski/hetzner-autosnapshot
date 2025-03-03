<?php

/**
 * Company: White Rock Development
 * Author: Piotr Dziubczynski, Software Engineer
 * Website: https://wrd.com.pl/
 * E-mail: support@wrd.com.pl
 */

declare(strict_types=1);

namespace WRD\Hetzner\AutoSnapshot\Core\Model\Hetzner\Response\Server;

use WRD\Hetzner\AutoSnapshot\Core\Model\Hetzner\Response\Parts\ActionModel;
use WRD\Hetzner\AutoSnapshot\Core\Model\Hetzner\Response\Parts\ImageModel;

final class CreateImageModel
{
    public ?ImageModel $image = null;
    public ?ActionModel $action = null;
}
