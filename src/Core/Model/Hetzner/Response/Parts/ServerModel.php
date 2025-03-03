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

final class ServerModel
{
    public int $id;
    public string $name;
    #[Context(
        normalizationContext: [DateTimeNormalizer::FORMAT_KEY => DATE_ATOM],
        denormalizationContext: [DateTimeNormalizer::FORMAT_KEY => DATE_ATOM]
    )]
    public DateTimeInterface $created;
}
