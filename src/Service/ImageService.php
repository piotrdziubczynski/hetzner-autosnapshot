<?php

/**
 * Company: White Rock Development
 * Author: Piotr Dziubczynski, Software Engineer
 * Website: https://wrd.com.pl/
 * E-mail: support@wrd.com.pl
 */

declare(strict_types=1);

namespace WRD\Hetzner\AutoSnapshot\Service;

use WRD\Hetzner\AutoSnapshot\Core\Config;
use WRD\Hetzner\AutoSnapshot\Core\Defaults;
use WRD\Hetzner\AutoSnapshot\Core\Model\Hetzner\Response\Image\ImagesModel;

final readonly class ImageService
{
    public function __construct(
        private Config $config,
    ) {
    }

    public function assignImagesToServers(?ImagesModel $model): ?ImagesModel
    {
        if (null === $model) {
            return null;
        }

        $mapped = [
            Defaults::UNASSIGN_IMAGES => [],
        ];

        foreach ($model->images as $image) {
            [, $serverName,] = explode(Defaults::IMAGE_NAME_FORMAT_DELIMITER, $image->description);

            if (!in_array($serverName, $this->config->getServers(), true)) {
                $mapped[Defaults::UNASSIGN_IMAGES][] = $image;

                continue;
            }

            $mapped[$serverName][] = $image;
        }

        $model->images = $mapped;

        return $model;
    }
}
