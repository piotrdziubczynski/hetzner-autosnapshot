<?php

/**
 * Company: White Rock Development
 * Author: Piotr Dziubczynski, Software Engineer
 * Website: https://wrd.com.pl/
 * E-mail: support@wrd.com.pl
 */

declare(strict_types=1);

namespace WRD\Hetzner\AutoSnapshot\Core\Model\Hetzner\Response\Server;

use WRD\Hetzner\AutoSnapshot\Core\Model\Hetzner\Response\Parts\ServerModel;

final class ServersModel
{
    /** @var ServerModel[] */
    public array $servers = [];

    public function addServer(ServerModel $server): void
    {
        $this->servers[] = $server;
    }

    public function removeServer(ServerModel $server): void
    {
    }
}
