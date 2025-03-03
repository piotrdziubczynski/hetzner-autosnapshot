<?php

/**
 * Company: White Rock Development
 * Author: Piotr Dziubczynski, Software Engineer
 * Website: https://wrd.com.pl/
 * E-mail: support@wrd.com.pl
 */

declare(strict_types=1);

define('__ROOT_DIR__', dirname(__DIR__));

use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\BackedEnumNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use WRD\Hetzner\AutoSnapshot\Core\Config;
use WRD\Hetzner\AutoSnapshot\Core\Defaults;
use WRD\Hetzner\AutoSnapshot\Core\Model\Hetzner\Response\Parts\ImageModel;
use WRD\Hetzner\AutoSnapshot\Service\Client;
use WRD\Hetzner\AutoSnapshot\Service\Image\Action\ImageRemover;
use WRD\Hetzner\AutoSnapshot\Service\Image\ImageFetcher;
use WRD\Hetzner\AutoSnapshot\Service\ImageService;

require __ROOT_DIR__ . '/vendor/autoload.php';

$dotenv = new Dotenv();

$dotenv->usePutenv();
$dotenv->load(__ROOT_DIR__ . '/.env');

$config = Config::load();
$client = new Client($config);
$serializer = new Serializer([
    new ArrayDenormalizer(),
    new DateTimeNormalizer(),
    new BackedEnumNormalizer(),
    new ObjectNormalizer(propertyTypeExtractor: new ReflectionExtractor()),
], [
    new JsonEncoder(),
]);
$logger = new Logger(Defaults::LOGGER_NAME, [new StreamHandler($config->getLoggerPath(), Level::Info)]);
$imageFetcher = new ImageFetcher($client, $serializer, $logger);
$model = $imageFetcher->fetchAvailableByPrefix($config->getImagePrefix());

if (empty($model?->images ?? [])) {
    return;
}

$model = (new ImageService($config))->assignImagesToServers($model);
$remover = new ImageRemover($config, $client, $logger);
$minimumBackups = $config->getImageBackupMinimum();
$now = new DateTimeImmutable();

/**
 * @var string $serverName
 * @var ImageModel[] $images
 */
foreach ($model->images as $serverName => $images) {
    if (Defaults::UNASSIGN_IMAGES === $serverName) {
        if (!$config->isImageDeleteUnassign()) {
            continue;
        }

        foreach ($images as $image) {
            $deleted = $remover->delete($image);

            if (!$deleted) {
                continue;
            }

            $message = sprintf('The %s \'%s\' has been deleted.', $image->type->value, $image->description);

            $logger->info($message);
        }
    }

    if (0 !== $minimumBackups) {
        $images = array_slice($images, $minimumBackups);
    }

    foreach ($images as $image) {
        $deleted = $remover->delete($image, $now);

        if (!$deleted) {
            continue;
        }

        $message = sprintf(
            'Server: %s. The %s \'%s\' has been deleted.',
            $serverName,
            $image->type->value,
            $image->description
        );

        $logger->info($message);
    }
}
