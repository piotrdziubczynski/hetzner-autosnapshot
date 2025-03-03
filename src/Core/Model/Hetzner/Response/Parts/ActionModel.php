<?php

/**
 * Company: White Rock Development
 * Author: Piotr Dziubczynski, Software Engineer
 * Website: https://wrd.com.pl/
 * E-mail: support@wrd.com.pl
 */

declare(strict_types=1);

namespace WRD\Hetzner\AutoSnapshot\Core\Model\Hetzner\Response\Parts;

use WRD\Hetzner\AutoSnapshot\Core\Enum\ActionStatus;

final class ActionModel
{
    public int $id;
    public ActionStatus $status;
    public ?ErrorModel $error = null;
}
