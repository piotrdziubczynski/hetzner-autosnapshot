<?php

/**
 * Company: White Rock Development
 * Author: Piotr Dziubczynski, Software Engineer
 * Website: https://wrd.com.pl/
 * E-mail: support@wrd.com.pl
 */

declare(strict_types=1);

namespace WRD\Hetzner\AutoSnapshot\Core\Model\Hetzner\Response\Parts;

use DateTimeInterface;
use Symfony\Component\Serializer\Attribute\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use WRD\Hetzner\AutoSnapshot\Core\Enum\ImageStatus;
use WRD\Hetzner\AutoSnapshot\Core\Enum\ImageType;

final class ImageModel
{
    public int $id;
    public ImageType $type;
    public ImageStatus $status;
    public string $description;
    public ?string $imageSize;
    #[Context(
        normalizationContext: [DateTimeNormalizer::FORMAT_KEY => DATE_ATOM],
        denormalizationContext: [DateTimeNormalizer::FORMAT_KEY => DATE_ATOM]
    )]
    public DateTimeInterface $created;
}
