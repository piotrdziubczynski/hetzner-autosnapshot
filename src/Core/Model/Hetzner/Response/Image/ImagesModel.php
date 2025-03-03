<?php

/**
 * Company: White Rock Development
 * Author: Piotr Dziubczynski, Software Engineer
 * Website: https://wrd.com.pl/
 * E-mail: support@wrd.com.pl
 */

declare(strict_types=1);

namespace WRD\Hetzner\AutoSnapshot\Core\Model\Hetzner\Response\Image;

use WRD\Hetzner\AutoSnapshot\Core\Model\Hetzner\Response\Parts\ImageModel;

final class ImagesModel
{
    /** @var ImageModel[] */
    public array $images = [];

    public function addImage(ImageModel $image): void
    {
        $this->images[] = $image;
    }

    public function removeImage(ImageModel $image): void
    {
    }
}
